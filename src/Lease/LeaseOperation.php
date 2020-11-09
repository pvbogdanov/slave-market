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
        $leaseContracts = $this->contractsRepository->getForSlave(
            $request->slave->getId(),
            $request->dateFrom->format(LeaseHour::FORMAT),
            $request->dateTo->format(LeaseHour::FORMAT)
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
        $leaseContract = new LeaseContract(
            $request->master,
            $request->slave,
            $request->slave->getPricePerHour() * count($leaseHours),
            $leaseHours
        );
        $leaseResponse->setLeaseContract($leaseContract);

        return $leaseResponse;
    }

    private function getLeaseHoursBetweenDates(DateTime $dateFrom, DateTime $dateTo): array
    {
        $dateLeft = DateTime::createFromFormat(LeaseHour::FORMAT, $dateFrom->format(LeaseHour::FORMAT));
        while ($dateLeft < $dateTo) {
            $leaseHours[] = new LeaseHour($dateLeft->format(LeaseHour::FORMAT));
            $dateLeft->modify('+ 1 Hour');
        }
        return $leaseHours;
    }
}