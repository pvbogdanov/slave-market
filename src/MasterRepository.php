<?php

namespace SlaveMarket;

/**
 * Репозиторий хозяев
 *
 * @package SlaveMarket
 */
interface MasterRepository
{
    /**
     * Возвращает хозяина по его id
     *
     * @param int $id
     * @return Master
     */
    public function getById(int $id) : Master;
}