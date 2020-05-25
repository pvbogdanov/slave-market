<?php


namespace SlaveMarket;

class SlaveDto
{
    public Slave $slave;

    public Master $master;

    public array $leaseContracts;

    /** @var string время начала работ Y-m-d H:i:s */
    public string $timeFrom;

    /** @var string время окончания работ Y-m-d H:i:s */
    public string $timeTo;

    public function __construct(Slave $slave, Master $master, array $leaseContracts, string $timeFrom, string $timeTo)
    {
        $this->slave = $slave;
        $this->master = $master;
        $this->leaseContracts = $leaseContracts;
        $this->timeFrom = $timeFrom;
        $this->timeTo = $timeTo;
    }
}