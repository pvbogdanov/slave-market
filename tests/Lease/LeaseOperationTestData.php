<?php

namespace SlaveMarket\Lease;

class LeaseOperationTestData
{
    public function getTestData() {
        return [
            [
                'masterId' => 2,
                'slaveId' => 1,
                'timeFrom' => '2017-01-01 01:30:00',
                'timeTo' => '2017-01-01 02:01:00',
                'expectedErrors' => [
                    'Ошибка. Раб #1 "Уродливый Фред" занят. Занятые часы: "2017-01-01 01", "2017-01-01 02"',
                ],
                'expectedContract' => [],
            ],
            [
                'masterId' => 1,
                'slaveId' => 1,
                'timeFrom' => '2018-01-01 01:30:00',
                'timeTo' => '2018-01-01 02:01:00',
                'expectedErrors' => [],
                'expectedContract' => [
                    'masterId' => 1,
                    'slaveId' => 1,
                    'price' => 40,
                    'leasedHours' => [
                        '2018-01-01 01',
                        '2018-01-01 02',
                    ],
                ],
            ],
            [
                'masterId' => 3,
                'slaveId' => 1,
                'timeFrom' => '2018-01-01 01:30:00',
                'timeTo' => '2018-01-01 02:01:00',
                'expectedErrors' => [
                    'Ошибка. Мастер не найден',
                ],
                'expectedContract' => [],
            ],
            [
                'masterId' => 1,
                'slaveId' => 3,
                'timeFrom' => '2018-01-01 01:30:00',
                'timeTo' => '2018-01-01 02:01:00',
                'expectedErrors' => [
                    'Ошибка. Раб не найден',
                ],
                'expectedContract' => [],
            ],
            [
                'masterId' => 1,
                'slaveId' => 1,
                'timeFrom' => '',
                'timeTo' => '2018-01-01 02:01:00',
                'expectedErrors' => [
                    'Ошибка. Дата начала имеет неверный формат',
                ],
                'expectedContract' => [],
            ],
            [
                'masterId' => 1,
                'slaveId' => 1,
                'timeFrom' => '2018-01-01 01:30:00',
                'timeTo' => '',
                'expectedErrors' => [
                    'Ошибка. Дата конца имеет неверный формат',
                ],
                'expectedContract' => [],
            ],
            [
                'masterId' => 1,
                'slaveId' => 1,
                'timeFrom' => '2018-01-01 02:01:00',
                'timeTo' => '2018-01-01 01:30:00',
                'expectedErrors' => [
                    'Ошибка. Дата конца меньше даты начала',
                ],
                'expectedContract' => [],
            ],
            [
                'masterId' => 3,
                'slaveId' => 3,
                'timeFrom' => '',
                'timeTo' => '',
                'expectedErrors' => [
                    'Ошибка. Мастер не найден',
                    'Ошибка. Раб не найден',
                    'Ошибка. Дата начала имеет неверный формат',
                    'Ошибка. Дата конца имеет неверный формат',
                ],
                'expectedContract' => [],
            ],
        ];
    }
}