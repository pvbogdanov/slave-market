<?php

namespace SlaveMarket\Lease;

use PHPUnit\Framework\TestCase;
use SlaveMarket\Master;
use SlaveMarket\Slave;

/**
 * @coversDefaultClass \SlaveMarket\Lease\LeaseHour
 */
class LeaseHourTest extends TestCase
{
    /**
     * @dataProvider hoursProvider
     */
    public function testGetHours(string $from, string $to, int $expected): void
    {
        static::assertCount($expected, LeaseHour::getHours($from, $to));
    }

    /**
     * @dataProvider correctIntervalProvider
     */
    public function testCheckInterval(array $contracts, string $from, string $to): void
    {
        static::assertNull(LeaseHour::checkInterval($contracts, $from, $to, false));
    }

    /**
     * @dataProvider incorrectIntervalProvider
     */
    public function testIncorrectCheckInterval(array $contracts, string $from, string $to): void
    {
        static::assertInstanceOf(LeaseHour::class, LeaseHour::checkInterval($contracts, $from, $to, false));
    }

    public function hoursProvider(): array
    {
        return [
            [
                '2010-01-01 10:00:00',
                '2010-01-02 10:00:00',
                25,
            ],
            [
                '2010-01-01 12:00:00',
                '2010-01-01 12:30:00',
                1,
            ],
            [
                '2010-01-01 10:00:00',
                '2010-01-01 11:00:00',
                2,
            ],
            [
                '2010-01-01 10:00:00',
                '2010-01-01 11:00:00',
                2,
            ],
            [
                '2010-01-01 11:30:00',
                '2010-01-01 13:00:00',
                3,
            ],
        ];
    }

    public function correctIntervalProvider(): array
    {
        return [
            [
                [
                    new LeaseContract(new Master(1, 'name'), new Slave(1, 'name', 10), 10, [
                        new LeaseHour('2017-01-01 03'),
                        new LeaseHour('2017-01-01 04'),
                        new LeaseHour('2017-02-01 04'),
                    ]),
                    new LeaseContract(new Master(2, 'name'), new Slave(1, 'name', 10), 10, [
                        new LeaseHour('2018-01-01 03'),
                    ]),
                ],
                '2010-01-01 10:00:00',
                '2010-01-02 10:00:00',
            ],
            [
                [
                    new LeaseContract(new Master(1, 'name'), new Slave(1, 'name', 10), 10, [
                        new LeaseHour('2017-01-01 03'),
                        new LeaseHour('2001-01-01 03'),
                    ]),
                ],
                '2010-01-01 10:00:00',
                '2010-01-02 10:00:00',
            ],
            [
                [
                    new LeaseContract(new Master(1, 'name'), new Slave(1, 'name', 10), 10, [
                        new LeaseHour('2017-01-01 03'),
                        new LeaseHour('2001-01-01 03'),
                        new LeaseHour('2018-01-01 03'),
                    ]),
                ],
                '2010-01-01 10:00:00',
                '2010-01-02 10:00:00',
            ],
        ];
    }

    public function incorrectIntervalProvider(): array
    {
        return [
            [
                [
                    new LeaseContract(new Master(1, 'name'), new Slave(1, 'name', 10), 10, [
                        new LeaseHour('2017-01-02 03'),
                    ]),
                    new LeaseContract(new Master(2, 'name'), new Slave(1, 'name', 10), 10, [
                        new LeaseHour('2018-01-02 03'),
                    ]),
                ],
                '2017-01-01 10:00:00',
                '2017-01-02 02:10:00',
            ],
            [
                [
                    new LeaseContract(new Master(1, 'name'), new Slave(1, 'name', 10), 10, [
                        new LeaseHour('2017-01-01 03'),
                        new LeaseHour('2001-01-01 03'),
                    ]),
                ],
                '2001-01-01 03:00:00',
                '2001-01-01 03:30:00',
            ],
            [
                [
                    new LeaseContract(new Master(1, 'name'), new Slave(1, 'name', 10), 10, [
                        new LeaseHour('2017-01-01 03'),
                        new LeaseHour('2001-01-01 03'),
                        new LeaseHour('2018-01-01 03'),
                    ]),
                ],
                '2018-01-01 03:00:00',
                '2018-01-01 03:30:00',
            ],
        ];
    }
}
