<?php

namespace SlaveMarket;

use SlaveMarket\Lease\LeaseResponse;

interface ScheduleInterface
{
    public function run(): LeaseResponse;
}
