<?php

namespace SlaveMarket\Lease;

use App\Domain\Shared\Exception\UnexpectedValueException;
use App\Domain\Shared\ValueObject\DateTimeRange;
use DateTime;
use Ramsey\Uuid\Uuid;
use SlaveMarket\Master;

/**
 * Арендованный час
 *
 * @package SlaveMarket\Lease
 */
class LeaseHour
{
    public CONST MAX_HOUR_IN_DAY = 16;

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
        $this->dateTime = DateTime::createFromFormat('Y-m-d H', $dateTime);
    }

    /**
     * Возвращает строку, представляющую час
     *
     * @return string
     */
    public function getDateString(): string
    {
        return $this->dateTime->format('Y-m-d H');
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

    public function modify(string $interval): self
    {
        $clone = clone $this;
        $clone->getDateTime()->modify($interval);

        return $clone;
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

    public static function fromString(string $dateTime): self
    {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);

        return new self($dateTime->format('Y-m-d H'));
    }

    /**
     * @return self[]
     */
    public static function getHours(string $from, string $to): array
    {
        [$from, $to] = self::getCorrectRanges($from, $to);
        $hours = ceil(($to->getTimestamp() - $from->getTimestamp()) / (60 * 60) );

        $result = [];
        for ($i = 0; $i < (int) $hours; $i++) {
            $result[] = new self($from->format('Y-m-d H'));
            $from->modify('+1 hour');
        }

        return $result;
    }

    public static function checkInterval(array $contracts, string $from, string $to, Master $master): ?LeaseHour
    {
        /** @var LeaseContract $contract */
        foreach ($contracts as $contract) {
            $hours = $contract->leasedHours;
            $checkedHours = self::getCorrectRanges($from, $to);
            $checkedHours = array_map(
                static fn(DateTime $row): self => new self($row->format('Y-m-d H')),
                $checkedHours,
            );
            $hours = [...$hours, ...$checkedHours];

            usort($hours, static fn(self $current, self $next) => $current->getDateString() > $next->getDateString());

            for ($i = 0, $count = count($hours); $i < $count; ++$i) {
                $current = $hours[$i];
                $next = null;
                if ($i < $count - 1) {
                    $next = $hours[$i + 1];
                }

                if (
                    null !== $next
                    && !$master->canRent($contract->master)
                    && $current->getDateString() >= $next->getDateString()
                ) {
                    return $current;
                }
            }
        }

        return null;
    }

    private static function getCorrectRanges(string $from, string $to): array
    {
        $from = DateTime::createFromFormat('Y-m-d H:i:s', $from);
        $from->setTime($from->format('H'), 0, 0);
        $to = DateTime::createFromFormat('Y-m-d H:i:s', $to);
        if (!$to->format('Y-m-d H:i:s') !== $to->format('Y-m-d H:00:00')) {
            $to = $to->modify('+1 hour');
        }
        $to->setTime($to->format('H'), 0, 0);
        if ($to < $from) {
            throw new \Exception('Invalid interval');
        }

        return [$from, $to];
    }
}
