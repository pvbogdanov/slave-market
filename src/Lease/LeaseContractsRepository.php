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
     * @param string $dateFrom Y-m-d
     * @param string $dateTo Y-m-d
     * @return LeaseContract[]
     */
    public function getForSlave(int $slaveId, string $dateFrom, string $dateTo) : array;
}