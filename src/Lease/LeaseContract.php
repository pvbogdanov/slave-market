<?php

namespace SlaveMarket\Lease;

use SlaveMarket\Master;
use SlaveMarket\Slave;

/**
 * Договор аренды
 *
 * @package SlaveMarket\Lease
 */
class LeaseContract
{
    /** @var Master Хозяин */
    public $master;

    /** @var Slave Раб */
    public $slave;

    /** @var LeaseHour[] Список арендованных часов */
    public $leasedHours = [];

    public function __construct(Master $master, Slave $slave, array $leasedHours)
    {
        $this->master = $master;
        $this->slave  = $slave;
    }
}
