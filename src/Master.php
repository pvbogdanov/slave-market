<?php

namespace SlaveMarket;

/**
 * Хозяин
 *
 * @package SlaveMarket
 */
class Master
{
    /** @var int id хозяина */
    protected $id;

    /** @var string имя хозяина */
    protected $name;

    /** @var bool является ли VIP-клиентом */
    protected $isVIP;

    private const LEVELS = [
        'GOLD' => 3,
        'SILVER' => 2,
        'BRONZE' => 1,
        'DEFAULT' => 0,
    ];

    private string $level = 'DEFAULT';

    /**
     * Master constructor.
     *
     * @param int $id
     * @param string $name
     * @param bool $isVIP
     */
    public function __construct(int $id, string $name, bool $isVIP = false)
    {
        $this->id    = $id;
        $this->name  = $name;
        $this->isVIP = $isVIP;
    }

    /**
     * Возвращает id хозяина
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Возвращает имя хозяина
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Является ли хозяин VIP-клиентом
     *
     * @return bool
     */
    public function isVIP(): bool
    {
        return $this->isVIP;
    }

    public function setLevel(string $level): void
    {
        $this->level = $level;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function canRent(self $master): bool
    {
        return ($this->isVIP && !$master->isVIP())
            || self::LEVELS[$this->level] > self::LEVELS[$master->getLevel()];
    }
}
