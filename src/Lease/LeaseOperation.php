<?php

namespace SlaveMarket\Lease;

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
        // Your code here :-)
    }
}