<?php

namespace SlaveMarket\Lease;

use DateTime;
use SlaveMarket\Master;
use SlaveMarket\MastersRepository;
use SlaveMarket\Slave;
use SlaveMarket\SlavesRepository;

/**
 * Запрос на аренду раба
 *
 * @package SlaveMarket\Lease
 */
class LeaseRequest
{
    public const FORMAT = 'Y-m-d H:i:s';

    /** @var Master */
    public $master;

    /** @var Slave */
    public $slave;

    /** @var DateTime */
    public $dateFrom;

    /** @var DateTime */
    public $dateTo;

    public function __construct(MastersRepository $mastersRepo, SlavesRepository $slavesRepo)
    {
        $this->mastersRepository = $mastersRepo;
        $this->slavesRepository = $slavesRepo;
    }

    public function setData(
        int $masterId,
        int $slaveId,
        string $timeFrom,
        string $timeTo
    ): self {
        $this->master = $this->mastersRepository->getById($masterId);
        $this->slave = $this->slavesRepository->getById($slaveId);
        $this->dateFrom = DateTime::createFromFormat(static::FORMAT, $timeFrom);
        $this->dateTo = DateTime::createFromFormat(static::FORMAT, $timeTo);

        return $this;
    }
}