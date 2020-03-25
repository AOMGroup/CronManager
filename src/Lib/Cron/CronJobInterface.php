<?php

declare(strict_types=1);

namespace AOM\CronManager\Lib\Cron;

use React\Promise\Deferred;

interface CronJobInterface
{
    public function run();
    public function isAvailable(): bool;
    public function startJob():void;
    public function finish(): void;
}