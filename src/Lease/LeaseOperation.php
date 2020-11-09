<?php

namespace SlaveMarket\Lease;

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
        if (count($request->validationErrors) > 0) {
            foreach ($request->validationErrors as $validationError) {
                $leaseResponse->addError($validationError);
            }

            return $leaseResponse;
        }
        $dateTo = clone $request->dateTo;
        if ($dateTo->format('i:s') === '00:00') {
            $dateTo->modify('- 1 Hour');
        }
        $leaseContracts = $this->contractsRepository->getForSlave(
            $request->slave->getId(),
            $request->dateFrom->format(LeaseHour::FORMAT),
            $dateTo->format(LeaseHour::FORMAT),
            $request->master->isVIP()
        );
        if (count($leaseContracts) > 0) {
            $leaseResponse->addOccupiedError(
                $request->slave->getId(),
                $request->slave->getName(),
                $leaseContracts
            );

            return $leaseResponse;
        }
        $leaseHours = $this->getLeaseHoursBetweenDates($request->dateFrom, $request->dateTo);
        if (LeaseHour::isSameDay($request->dateFrom, $request->dateTo)) {
            $hoursCount = count($leaseHours);
        } else {
            $hoursCount = count($leaseHours) / 24 * 16;
        }

        $leaseContract = new LeaseContract(
            $request->master,
            $request->slave,
            $request->slave->getPricePerHour() * $hoursCount,
            $leaseHours
        );
        $leaseResponse->setLeaseContract($leaseContract);

        return $leaseResponse;
    }

    private function getLeaseHoursBetweenDates(DateTime $dateFrom, DateTime $dateTo): array
    {
        if (LeaseHour::isSameDay($dateFrom, $dateTo)) {
            $dateLeft = DateTime::createFromFormat(LeaseHour::FORMAT, $dateFrom->format(LeaseHour::FORMAT));
            $dateRight = $dateTo;
        } else {
            $dateLeft = DateTime::createFromFormat('Y-m-d H:i:s', $dateFrom->format('Y-m-d') . ' 00:00:00');
            $dateRight = DateTime::createFromFormat('Y-m-d H:i:s', $dateTo->format('Y-m-d') . ' 23:00:01');
        }
        $leaseHours = [];
        while ($dateLeft < $dateRight) {
            $leaseHours[] = new LeaseHour($dateLeft->format(LeaseHour::FORMAT));
            $dateLeft->modify('+ 1 Hour');
        }
        return $leaseHours;
    }
}