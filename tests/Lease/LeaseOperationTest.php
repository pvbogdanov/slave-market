<?php

namespace SlaveMarket\Lease;

use PHPUnit\Framework\TestCase;
use SlaveMarket\Lease\LeaseContract;
use SlaveMarket\Lease\LeaseContractsRepository;
use SlaveMarket\Master;
use SlaveMarket\MastersRepository;
use SlaveMarket\Slave;
use SlaveMarket\SlavesRepository;

/**
 * Тесты операции аренды раба
 *
 * @package SlaveMarket\Lease
 */
class LeaseOperationTest extends TestCase
{
    private static $IS_REPO_SET_UP = false;

    /** @var MastersRepository */
    private static $MASTER_REPO;

    /** @var SlavesRepository */
    private static $SLAVE_REPO;

    /** @var LeaseContractsRepository */
    private static $CONTRACTS_REPO;

    /**
     * Stub репозитория хозяев
     *
     * @param Master[] ...$masters
     * @return MastersRepository
     */
    private function makeFakeMasterRepository(...$masters): MastersRepository
    {
        $mastersRepository = $this->prophesize(MastersRepository::class);
        foreach ($masters as $master) {
            $mastersRepository->getById($master->getId())->willReturn($master);
        }

        return $mastersRepository->reveal();
    }

    /**
     * Stub репозитория рабов
     *
     * @param Slave[] ...$slaves
     * @return SlavesRepository
     */
    private function makeFakeSlaveRepository(...$slaves): SlavesRepository
    {
        $slavesRepository = $this->prophesize(SlavesRepository::class);
        foreach ($slaves as $slave) {
            $slavesRepository->getById($slave->getId())->willReturn($slave);
        }

        return $slavesRepository->reveal();
    }

    private function contractToArray(?LeaseContract $leaseContract): array
    {
        if ($leaseContract === null) {
            return [];
        }
        $responseArray = [
            'masterId' => $leaseContract->master->getId(),
            'slaveId' => $leaseContract->slave->getId(),
            'price' => $leaseContract->price,
        ];
        $leasedHours = [];
        foreach ($leaseContract->leasedHours as $leasedHour) {
            $leasedHours[] = $leasedHour->getDateString();
        }
        $responseArray['leasedHours'] = $leasedHours;
        return $responseArray;
    }

    protected function setUp()
    {
        if (!static::$IS_REPO_SET_UP) {
            static::$IS_REPO_SET_UP = true;

            // Хозяева
            $master1    = new Master(1, 'Господин Боб');
            $master2    = new Master(2, 'сэр Вонючка');
            static::$MASTER_REPO = $this->makeFakeMasterRepository($master1, $master2);

            // Раб
            $slave1    = new Slave(1, 'Уродливый Фред', 20);
            static::$SLAVE_REPO = $this->makeFakeSlaveRepository($slave1);

            // Договор аренды. 1й хозяин арендовал раба
            $leaseContract1 = new LeaseContract($master1, $slave1, 80, [
                new LeaseHour('2017-01-01 00'),
                new LeaseHour('2017-01-01 01'),
                new LeaseHour('2017-01-01 02'),
                new LeaseHour('2017-01-01 03'),
            ]);

            static::$CONTRACTS_REPO = $this->prophesize(LeaseContractsRepository::class);
            static::$CONTRACTS_REPO
                ->getForSlave($slave1->getId(), '2017-01-01', '2017-01-01')
                ->willReturn([$leaseContract1]);
            static::$CONTRACTS_REPO
                ->getForSlave($slave1->getId(), '2018-01-01', '2018-01-01')
                ->willReturn([]);
        }
    }

    public function providerLeaseOperation() {
        return [
            [
                'masterId' => 2,
                'slaveId' => 1,
                'timeFrom' => '2017-01-01 01:30:00',
                'timeTo' => '2017-01-01 02:01:00',
                'expectedErrors' => [
                    'Ошибка. Раб #1 "Уродливый Фред" занят. Занятые часы: "2017-01-01 01", "2017-01-01 02"',
                ],
                'expectedContract' => [],
            ],
            [
                'masterId' => 1,
                'slaveId' => 1,
                'timeFrom' => '2018-01-01 01:30:00',
                'timeTo' => '2018-01-01 02:01:00',
                'expectedErrors' => [],
                'expectedContract' => [
                    'masterId' => 1,
                    'slaveId' => 1,
                    'price' => 40,
                    'leasedHours' => [
                        '2018-01-01 01',
                        '2018-01-01 02',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerLeaseOperation
     *
     * Если раб занят, то арендовать его не получится
     */
    public function test_periodIsBusy_failedWithOverlapInfo(
        $masterId,
        $slaveId,
        $timeFrom,
        $timeTo,
        $expectedErrors,
        $expectedContract
    ) {
        // -- Arrange
        {
            // Запрос на новую аренду. 2й хозяин выбрал занятое время
            $leaseRequest           = new LeaseRequest();
            $leaseRequest->masterId = $masterId;
            $leaseRequest->slaveId  = $slaveId;
            $leaseRequest->timeFrom = $timeFrom;
            $leaseRequest->timeTo   = $timeTo;

            // Операция аренды
            $leaseOperation = new LeaseOperation(
                static::$CONTRACTS_REPO->reveal(),
                static::$MASTER_REPO,
                static::$SLAVE_REPO
            );
        }

        // -- Act
        $response = $leaseOperation->run($leaseRequest);

        $this->assertEquals($expectedErrors, $response->getErrors());
        $this->assertEquals($expectedContract, $this->contractToArray($response->getLeaseContract()));
    }
}