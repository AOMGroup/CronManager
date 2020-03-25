<?php

declare(strict_types=1);

namespace AOM\CronManager\Lib;

class Locks
{
    private static array $locks;

    private bool $isLocked = false;

    private function __construct()
    {
    }


    public static function getLock($process): self {
        if(!isset(self::$locks[$process])){
            self::$locks[$process] = new self();
        }
        return self::$locks[$process];
    }

    public function isLocked(): bool
    {
        return $this->isLocked;
    }

    public function lock(): self
    {
        $this->isLocked = true;
        return $this;
    }

    public function free(): self
    {
        $this->isLocked = false;
        return $this;
    }
}