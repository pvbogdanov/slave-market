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

    public function providerLeaseOperation() {
        $leaseOperationTestData = new LeaseOperationTestData();

        return $leaseOperationTestData->getTestData();
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
        // Запрос на новую аренду. 2й хозяин выбрал занятое время
        $leaseRequest = new LeaseRequest(static::$MASTER_REPO, static::$SLAVE_REPO);
        $leaseRequest->setData($masterId, $slaveId, $timeFrom, $timeTo);

        // Операция аренды
        $leaseOperation = new LeaseOperation(
            static::$CONTRACTS_REPO->reveal(),
            static::$MASTER_REPO,
            static::$SLAVE_REPO
        );
        $response = $leaseOperation->run($leaseRequest);

        $this->assertEquals($expectedErrors, $response->getErrors());
        $this->assertEquals($expectedContract, $this->contractToArray($response->getLeaseContract()));
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
                new LeaseHour('2017-01-01 01'),
                new LeaseHour('2017-01-01 02'),
            ]);

            static::$CONTRACTS_REPO = $this->prophesize(LeaseContractsRepository::class);
            static::$CONTRACTS_REPO
                ->getForSlave($slave1->getId(), '2017-01-01 01', '2017-01-01 02')
                ->willReturn([$leaseContract1]);
            static::$CONTRACTS_REPO
                ->getForSlave($slave1->getId(), '2018-01-01 01', '2018-01-01 02')
                ->willReturn([]);
        }
    }

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
        $mastersRepository->getById(3)->willReturn(null);

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
        $slavesRepository->getById(3)->willReturn(null);

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
}