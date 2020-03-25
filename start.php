<?php

use AOM\CronManager\Lib\Cron\CronJobInterface;
use AOM\CronManager\Lib\CronIterator;
use AOM\CronManager\Lib\Hive;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;
use React\EventLoop\Factory;

require_once "vendor/autoload.php";

$builder = new ContainerBuilder();

$builder->addDefinitions(__DIR__ . '/config/config.php');
$builder->addDefinitions(__DIR__ . '/config/services.php');

try {
    $container = $builder->build();
} catch (Throwable $e) {
    echo $e->getMessage();
    exit(1);
}

/** @var CronJobInterface[] $jobs */
$jobs = $container->get(CronIterator::class)->getCrons();
$loop = Factory::create();

/** @var LoggerInterface $logger */
$logger = $container->get(LoggerInterface::class);
$tick = 0;
$jobsLimiter = [];
$processes = [];
$loop->addPeriodicTimer(1, function () use ($jobs, $logger, &$tick, &$jobsLimiter, &$processes) {

    $tick++;
    $shouldReset = false;
    if ($tick % 60 === 0) {
        $tick = 0;
        $shouldReset = true;
    }
    foreach ($jobs as $index => $job) {
        $jobsLimiter[$index] = $shouldReset ? false : true;
        if (isset($jobsLimiter[$index]) && $jobsLimiter[$index] === true) {
            continue;
        }
        if (!$job->isAvailable()) {
            continue;
        }
        $processes[Hive::spawn($job, $logger)] = $index;
    }
    foreach ($processes as $process)
    {
        $pid = pcntl_waitpid(-1, $status, WNOHANG);
        if($pid > 0) {
            $jobs[$processes[$pid]]->finish();
            unset($processes[$pid]);
        }

    }
});

$loop->run();
