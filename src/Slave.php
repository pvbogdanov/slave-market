<?php

namespace SlaveMarket;

use SlaveMarket\Lease\LeaseContract;
use SlaveMarket\Lease\LeaseHour;

/**
 * Раб (Бедняга :-()
 *
 * @package SlaveMarket
 */
class Slave
{
    /** @var int id раба */
    protected $id;

    /** @var string имя раба */
    protected $name;

    /** @var float Стоимость раба за час работы */
    protected $pricePerHour;

    /**
     * Slave constructor.
     *
     * @param int $id
     * @param string $name
     * @param float $pricePerHour
     */
    public function __construct(int $id, string $name, float $pricePerHour)
    {
        $this->id           = $id;
        $this->name         = $name;
        $this->pricePerHour = $pricePerHour;
    }

    /**
     * Возвращает id раба
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Возвращает имя раба
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Возвращает стоимость раба за час
     *
     * @return float
     */
    public function getPricePerHour(): float
    {
        return $this->pricePerHour;
    }

    /**
     * @param LeaseHour[] $hours
     * @return float
     */
    public function getPricePerHours(array $hours): float
    {
        $days = [];
        foreach ($hours as $leasedHour) {
            $days[$leasedHour->getDate()][] = $leasedHour;
        }

        $result = 0;
        foreach ($days as $day) {
            for ($i = 0, $count = count($hours); $i < $count; $i++) {
                if ($i >= LeaseHour::MAX_HOUR_IN_DAY) {
                    break;
                }
                $result += $this->pricePerHour;
            }
        }

        return $result;
    }

    /**
     * @param LeaseHour[] $hours
     * @param LeaseContract[] $leaseContracts
     * @return bool
     */
    public static function canAddedHours(array $hours, array $leaseContracts): bool
    {
        $days = [];
        foreach ($hours as $leasedHour) {
            $days[$leasedHour->getDate()][] = $leasedHour;
        }
        if (count($days) > 1) {
            return true;
        }

        foreach ($leaseContracts as $leaseContract) {
            $hoursInDay = $leaseContract->hoursInDay;

            if (array_key_exists(key($hoursInDay), $days)) {
                $hoursInDay = count(current($hoursInDay)) + count($days[key($hoursInDay)]);
               if ($hoursInDay > LeaseHour::MAX_HOUR_IN_DAY) {
                   return false;
               }
            }
        }

        return true;
    }
}