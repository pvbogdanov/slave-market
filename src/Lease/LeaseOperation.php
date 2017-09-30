<?php

namespace SlaveMarket\Lease;

use SlaveMarket\MasterRepository;
use SlaveMarket\SlaveRepository;

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
     * @var MasterRepository
     */
    protected $mastersRepository;

    /**
     * @var SlaveRepository
     */
    protected $slavesRepository;

    /**
     * LeaseOperation constructor.
     *
     * @param LeaseContractsRepository $contractsRepo
     * @param MasterRepository $mastersRepo
     * @param SlaveRepository $slavesRepo
     */
    public function __construct(LeaseContractsRepository $contractsRepo, MasterRepository $mastersRepo, SlaveRepository $slavesRepo)
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
        // Your code here :-)
    }
}