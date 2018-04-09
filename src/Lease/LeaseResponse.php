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
    public function getLeaseContract()
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
    public function addError(string $message)
    {
        $this->errors[] = $message;
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
}