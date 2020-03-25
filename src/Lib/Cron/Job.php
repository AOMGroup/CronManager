<?php

declare(strict_types=1);

namespace AOM\CronManager\Lib\Cron;

use AOM\CronManager\Lib\Database;
use AOM\CronManager\Lib\Database\Manager;
use AOM\CronManager\Lib\Locks;
use Cron\CronExpression;
use PDO;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use React\Promise\Deferred;

abstract class Job
{
    protected PDO $connection;

    protected string $schedule = '* * * * *';

    protected Locks $lock;

    protected CronExpression $checker;

    private LoggerInterface $logger;

    private Deferred $deferred;

    private Database\Manager $manager;

    public function __construct(Database\Manager $manager, LoggerInterface $logger)
    {
        $this->manager = $manager;
        $this->connection = $manager->getConnection();
        $this->lock = Locks::getLock(static::class);
        $this->checker = CronExpression::factory($this->schedule);
        $this->logger = $logger;
        $this->deferred = new Deferred();
    }

    final public function isAvailable(): bool
    {
        if(!$this->checker->isDue()) {
            return false;
        }
        if ($this->lock->isLocked()) {
            $this->logger->log(LogLevel::WARNING, static::class , ['message' => 'Skipped', 'status'=> 'running']);
            return false;
        }
        return true;
    }

    public function startJob(): void
    {
        $this->connection = $this->manager::restart()->getConnection();
        $this->lock->lock();
        $this->logger->log(LogLevel::INFO, static::class, ['message' => 'Started', 'status'=> 'running']);
    }

    final public function finish(): void {
        $this->lock->free();
    }

    abstract public function run();

}