<?php

namespace SlaveMarket\Lease;

use DateTime;

/**
 * Арендованный час
 *
 * @package SlaveMarket\Lease
 */
class LeaseHour
{
    public const FORMAT = 'Y-m-d H';

    /**
     * Время начала часа
     *
     * @var DateTime
     */
    protected $dateTime;

    /**
     * LeaseHour constructor.
     *
     * @param string $dateTime Y-m-d H
     */
    public function __construct(string $dateTime)
    {
        $this->dateTime = DateTime::createFromFormat(static::FORMAT, $dateTime);
    }

    /**
     * Возвращает строку, представляющую час
     *
     * @return string
     */
    public function getDateString(): string
    {
        return $this->dateTime->format(static::FORMAT);
    }

    /**
     * Возвращает объект даты
     *
     * @return DateTime
     */
    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }

    /**
     * Возвращает день аренды
     *
     * @return string
     */
    public function getDate(): string
    {
        return $this->dateTime->format('Y-m-d');
    }

    /**
     * Возвращает час аренды
     *
     * @return string
     */
    public function getHour(): string
    {
        return $this->dateTime->format('H');
    }
}
