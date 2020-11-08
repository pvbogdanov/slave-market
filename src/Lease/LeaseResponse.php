<?php

namespace SlaveMarket\Lease;

/**
 * Результат операции аренды
 *
 * @package SlaveMarket\Lease
 */
class LeaseResponse
{
    /** @var LeaseContract договор аренды */
    protected $leaseContract;

    /** @var string[] список ошибок */
    protected $errors = [];

    /**
     * Возвращает договор аренды, если аренда была успешной
     *
     * @return LeaseContract
     */
    public function getLeaseContract(): ?LeaseContract
    {
        return $this->leaseContract;
    }

    /**
     * Указать договор аренды
     *
     * @param LeaseContract $leaseContract
     */
    public function setLeaseContract(LeaseContract $leaseContract)
    {
        $this->leaseContract = $leaseContract;
    }

    /**
     * Сообщить об ошибке
     *
     * @param string $message
     */
    public function addOccupiedError(int $id, string $name, array $hours): void
    {
        $hoursString = implode(', ', $hours);
        $this->addError(
            sprintf(
                'Ошибка. Раб #%d "%s" занят. Занятые часы: %s',
                $id,
                $name,
                $hoursString
            )
        );
    }

    /**
     * Возвращает все ошибки в процессе аренды
     *
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Сообщить об ошибке
     *
     * @param string $message
     */
    private function addError(string $message): void
    {
        $this->errors[] = $message;
    }
}