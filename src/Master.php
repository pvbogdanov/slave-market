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
}