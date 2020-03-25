<?php

use AOM\CronManager\Lib\CronIterator;
use DI\Container;
use AOM\CronManager\Lib\Database;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

return [
    Database\Manager::class => function (Container $container) {
        $dbConfig = $container->get('database');
        return AOM\CronManager\Lib\Database\Manager::getManager(
            $dbConfig['dsn'],
            $dbConfig['user'],
            $dbConfig['pass'],
            $dbConfig['options']
        );
    },

    LoggerInterface::class => function (Container $container) {
        $logConfig = $container->get('log');
        $handler = new StreamHandler($logConfig['path'], Logger::INFO);
        return (new Logger($logConfig['name']))->pushHandler($handler);
    },

    CronIterator::class => function (Container $container) {
        return new CronIterator(
            $container->get('jobs')['folder'],
            $container->get('jobs')['namespace'],
            $container->get(Database\Manager::class),
            $container->get(LoggerInterface::class)
        );
    }

];