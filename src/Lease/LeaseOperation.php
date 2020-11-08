<?php

namespace SlaveMarket\Lease;

use DateInterval;
use DatePeriod;
use DateTime;
use SlaveMarket\MastersRepository;
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
        $this->mastersRepository   = $mastersRepo;
        $this->slavesRepository    = $slavesRepo;
    }

    /**
     * Выполнить операцию
     *
     * @param LeaseRequest $request
     * @return LeaseResponse
     */
    public function run(LeaseRequest $request): LeaseResponse
    {
        $leaseResponse = new LeaseResponse();
        $master = $this->mastersRepository->getById($request->masterId);
        $slave = $this->slavesRepository->getById($request->slaveId);
        $dateFrom = DateTime::createFromFormat('Y-m-d H:i:s', $request->timeFrom);
        $dateTo = DateTime::createFromFormat('Y-m-d H:i:s', $request->timeTo);

        $leaseContracts = $this->contractsRepository->getForSlave(
            $slave->getId(),
            $dateFrom->format('Y-m-d H'),
            $dateTo->format('Y-m-d H')
        );
        if (count($leaseContracts) > 0) {
            $leasedHours = [];
            foreach ($leaseContracts as $leaseContract) {
                foreach ($leaseContract->leasedHours as $leasedHour) {
                    $leasedHours[] = sprintf('"%s"', $leasedHour->getDateString());
                }
            }
            $leaseResponse->addOccupiedError(
                $slave->getId(),
                $slave->getName(),
                $leasedHours
            );
        }
        else {
            $leaseHours = $this->getLeaseHoursBetweenDates($dateFrom, $dateTo);
            $leaseContract = new LeaseContract(
                $master,
                $slave,
                $slave->getPricePerHour() * count($leaseHours),
                $leaseHours
            );
            $leaseResponse->setLeaseContract($leaseContract);
        }
        return $leaseResponse;
    }

    private function getLeaseHoursBetweenDates(DateTime $dateFrom, DateTime $dateTo): array
    {
        $dateLeft = DateTime::createFromFormat('Y-m-d H', $dateFrom->format('Y-m-d H'));
        while ($dateLeft < $dateTo) {
            $leaseHours[] = new LeaseHour($dateLeft->format('Y-m-d H'));
            $dateLeft->modify('+ 1 Hour');
        }
        return $leaseHours;
    }
}