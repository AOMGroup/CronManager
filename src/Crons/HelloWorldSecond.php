<?php

declare(strict_types=1);

namespace AOM\CronManager\Crons;

use AOM\CronManager\Lib\Cron\CronJobInterface;
use AOM\CronManager\Lib\Cron\Job;

class HelloWorldSecond extends Job implements CronJobInterface
{
    protected string $schedule = '*/1 * * * *';

    public function run()
    {
        sleep(150);
        return [
            'status' => 'ok',
            'errors' => []
        ];
    }
}