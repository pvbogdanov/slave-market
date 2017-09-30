<?php

namespace SlaveMarket\Lease;

/**
 * Запрос на аренду раба
 *
 * @package SlaveMarket\Lease
 */
class LeaseRequest
{
    /** @var int id хозяина */
    public $masterId;

    /** @var int id раба */
    public $slaveId;

    /** @var string время начала работ Y-m-d H:i:s */
    public $timeFrom;

    /** @var string время окончания работ Y-m-d H:i:s */
    public $timeTo;
}