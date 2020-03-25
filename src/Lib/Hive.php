<?php

declare(strict_types=1);

namespace AOM\CronManager\Lib;

use AOM\CronManager\Lib\Cron\CronJobInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Hive
{
    public static function spawn(CronJobInterface $job, LoggerInterface $logger): int
    {
        $job->startJob();
        $pid = pcntl_fork();
        if ($pid === -1) {
            $logger->log(
                LogLevel::CRITICAL,
                static::class,
                [
                    'message' => 'Error',
                    'status' => 'unable to create child process'
                ]
            );
            exit(1);
        }
        if ($pid === 0) {
            $result = $job->run();
            $context = ['message' => 'Finished'];
            $context = array_merge($context, $result);
            $logger->log(
                LogLevel::INFO,
                get_class($job),
                $context
            );
            exit(0);
        }
        return $pid;
    }

}