<?php

namespace SlaveMarket;

use SlaveMarket\Lease\LeaseContract;
use SlaveMarket\Lease\LeaseHour;
use SlaveMarket\Lease\LeaseResponse;

class SlaveSchedule implements ScheduleInterface
{
    private SlaveDto $data;

    public function __construct(SlaveDto $data)
    {
        $this->data = $data;
    }

    public function run(): LeaseResponse
    {
        $result = new LeaseResponse();
        $from = $this->data->timeFrom;
        $to = $this->data->timeTo;
        $hours = LeaseHour::getHours($from, $to);
        $leaseContracts = $this->data->leaseContracts;
        $slave = $this->data->slave;
        $master = $this->data->master;

        if (empty($leaseContracts)) {
            $result->setLeaseContract(new LeaseContract($master, $slave, $slave->getPricePerHours($hours), $hours));

            return $result;
        }

        $leaseHour = LeaseHour::checkInterval($leaseContracts, $from, $to, $master);
        if ($leaseHour instanceof LeaseHour) {
            self::addSlaveBusyError($leaseHour, $result, $slave);

            return $result;
        }

        if (Slave::canAddedHours($hours, $leaseContracts)) {
            $result->setLeaseContract(new LeaseContract($master, $slave, $slave->getPricePerHours($hours), $hours));
        } else {
            self::addSlaveWorkOverError($result, $slave);
        }

        return $result;
    }

    private static function addSlaveBusyError(LeaseHour $leaseHour, LeaseResponse $result, Slave $slave): void
    {
        $dateString = $leaseHour->getDateString();
        $result->addError(
            sprintf(
                'Ошибка. Раб #%d "%s" занят. Занятые часы: "%s", "%s"',
                $slave->getId(),
                $slave->getName(),
                $dateString,
                $leaseHour->modify('+1 hour')->getDateString(),
            )
        );
    }

    private static function addSlaveWorkOverError(LeaseResponse $result, Slave $slave): void
    {
        $result->addError(
            sprintf(
                'Ошибка. Раб #%d "%s" не может работать больше %d часов в день.',
                $slave->getId(),
                $slave->getName(),
                LeaseHour::MAX_HOUR_IN_DAY,
            )
        );
    }
}