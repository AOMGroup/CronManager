<?php

declare(strict_types=1);

namespace AOM\CronManager\Crons;

use AOM\CronManager\Lib\Cron\CronJobInterface;
use AOM\CronManager\Lib\Cron\Job;

class DatabaseTest extends Job implements CronJobInterface
{
    protected string $schedule = '*/1 * * * *';

    public function run()
    {
        return [
            'status' => 'ok',
            'errors' => [],
            'record' => $this->connection->query('SELECT * FROM `pet`')->fetch()
        ];
    }
}