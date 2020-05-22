<?php

namespace SlaveMarket\Lease;

use SlaveMarket\MastersRepository;
use SlaveMarket\Slave;
use SlaveMarket\SlavesRepository;

/**
 * Операция "Арендовать раба"
 *
 * @package SlaveMarket\Lease
 */
class LeaseOperation
{
    /**
     * @var LeaseContractsRepository
     */
    protected $contractsRepository;

    /**
     * @var MastersRepository
     */
    protected $mastersRepository;

    /**
     * @var SlavesRepository
     */
    protected $slavesRepository;

    /**
     * LeaseOperation constructor.
     *
     * @param LeaseContractsRepository $contractsRepo
     * @param MastersRepository $mastersRepo
     * @param SlavesRepository $slavesRepo
     */
    public function __construct(LeaseContractsRepository $contractsRepo, MastersRepository $mastersRepo, SlavesRepository $slavesRepo)
    {
        $this->contractsRepository = $contractsRepo;
        $this->mastersRepository = $mastersRepo;
        $this->slavesRepository = $slavesRepo;
    }

    /**
     * Выполнить операцию
     *
     * @param LeaseRequest $request
     * @return LeaseResponse
     */
    public function run(LeaseRequest $request): LeaseResponse
    {
        $result = new LeaseResponse();
        $slave = $this->slavesRepository->getById($request->slaveId);
        $master = $this->mastersRepository->getById($request->masterId);
        $leaseContracts = $this->contractsRepository->getForSlave(
            $request->slaveId,
            LeaseHour::fromString($request->timeFrom)->getDate(),
            LeaseHour::fromString($request->timeTo)->getDate(),
        );
        $hours = LeaseHour::getHours($request->timeFrom, $request->timeTo);

        if (empty($leaseContracts)) {
            $result->setLeaseContract(new LeaseContract($master, $slave, $slave->getPricePerHours($hours), $hours));
            return $result;
        }

        $leaseHour = LeaseHour::checkInterval($leaseContracts, $request->timeFrom, $request->timeTo, $master->isVIP());
        if ($leaseHour instanceof LeaseHour) {
            $dateString = $leaseHour->getDateString();
            $result->addError(
                sprintf(
                    'Ошибка. Раб #%d "%s" занят. Занятые часы: "%s", "%s"',
                    $slave->getId(),
                    $slave->getName(),
                    $dateString,
                    $leaseHour->modify('+1 hour')->getDateString(),
                )
            );

            return $result;
        }

        if (Slave::canAddedHours($hours, $leaseContracts)) {
            $result->setLeaseContract(new LeaseContract($master, $slave, $slave->getPricePerHours($hours), $hours));
        } else {
            $result->addError(
                sprintf(
                    'Ошибка. Раб #%d "%s" не может работать больше %d часов в день.',
                    $slave->getId(),
                    $slave->getName(),
                    LeaseHour::MAX_HOUR_IN_DAY,
                )
            );
        }

        return $result;
    }
}