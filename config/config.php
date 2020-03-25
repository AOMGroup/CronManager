<?php

return [
    'database' => [
        'dsn' => 'mysql:host=database;dbname=crons',
        'user' => 'root',
        'pass' => 'xxxxxxxxx',
        'options' => [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    ],
    'jobs' => [
        'folder' => '/app/src/Crons',
        'namespace' => 'AOM\CronManager\Crons'
    ],

    'log' => [
        'name' => 'crons',
        'path' => '/app/var/log/events.log'
    ]
];