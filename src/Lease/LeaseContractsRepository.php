<?php

namespace SlaveMarket\Lease;

/**
 * Репозиторий договоров аренды
 *
 * @package SlaveMarket\Lease
 */
interface LeaseContractsRepository
{
    /**
     * Возвращает список договоров аренды для раба, в которых заняты часы из указанного периода
     *
     * @param int $slaveId
     * @param string $dateFrom Y-m-d HH
     * @param string $dateTo Y-m-d HH
     * @return LeaseContract[]
     */
    public function getForSlave(int $slaveId, string $dateFrom, string $dateTo) : array;
}