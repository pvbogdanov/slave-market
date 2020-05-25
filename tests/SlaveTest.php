<?php

namespace SlaveMarket;

use PHPUnit\Framework\TestCase;
use SlaveMarket\Lease\LeaseContract;
use SlaveMarket\Lease\LeaseHour;

class SlaveTest extends TestCase
{
    /**
     * @dataProvider hoursProvider
     */
    public function testGetPricePerHours(int $count, int $expected): void
    {
        $slave = new Slave(1, 'name', 10);
        $pricePerHours = $slave->getPricePerHours($this->getHours($count));
        static::assertEquals($expected, $pricePerHours);
    }

    public function testCanAddedHours(): void
    {
        $result = Slave::canAddedHours([
            new LeaseHour('2010-01-01 01')
        ], [
            new LeaseContract(new Master(1, 'name'), new Slave(1, 'name', 10), 10, [
                new LeaseHour('2017-01-01 03'),
                new LeaseHour('2017-01-01 04'),
                new LeaseHour('2017-02-01 04'),
            ]),
        ]);
        static::assertTrue($result);

        $result = Slave::canAddedHours([
            new LeaseHour('2010-01-01 01')
        ], [
            new LeaseContract(new Master(1, 'name'), new Slave(1, 'name', 10), 10, $this->getHours(17)),
        ]);
        static::assertFalse($result);
    }

    private function getHours(int $count): array
    {
        $result = [];
        $begin = new \DateTime('2010-01-01 00:00:00');

        for ($i = 0; $i < $count; $i++) {
            $current = clone $begin;
            $current->modify(sprintf('+%d hour', $i));
            $result[] = new LeaseHour($current->format('Y-m-d H'));
        }

        return $result;
    }

    public function hoursProvider(): array
    {
        return [
            [18, 160],
            [36, 320],
            [10, 100],
        ];
    }
}
