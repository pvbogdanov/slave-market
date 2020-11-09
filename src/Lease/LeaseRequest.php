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

    /** @var array */
    public $validationErrors = [];

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
        $this->validationErrors = $this->getValidationErrors();

        return $this;
    }

    private function getValidationErrors(): array
    {
        $validationErrors = [];
        if (null === $this->master) {
            $validationErrors[] = 'Ошибка. Мастер не найден';
        }
        if (null === $this->slave) {
            $validationErrors[] = 'Ошибка. Раб не найден';
        }
        if (false === $this->dateFrom) {
            $validationErrors[] = 'Ошибка. Дата начала имеет неверный формат';
        }
        if (false === $this->dateTo) {
            $validationErrors[] = 'Ошибка. Дата конца имеет неверный формат';
        }
        if (
            false !== $this->dateFrom &&
            false !== $this->dateTo
        ) {
            if ($this->dateTo <= $this->dateFrom) {
                $validationErrors[] = 'Ошибка. Дата конца меньше даты начала';
            }
            if (
                $this->dateFrom->format('Y-m-d') === $this->dateTo->format('Y-m-d') &&
                (
                    $this->dateTo->format('H') - $this->dateFrom->format('H') > 16 ||
                    (
                        $this->dateTo->format('H') - $this->dateFrom->format('H') == 16 &&
                        $this->dateTo->format('i:s') > '00:00'
                    )
                ) 
            ) {
                $validationErrors[] = 'Ошибка. Указано более 16 часов';
            }
        }

        return $validationErrors;
    }
}