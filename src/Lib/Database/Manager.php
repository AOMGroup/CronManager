<?php

declare(strict_types=1);

namespace AOM\CronManager\Lib\Database;

use PDO;
use PDOException;

class Manager
{
    private PDO $_pdo;

    private static string $dsn;
    private static string $user;
    private static string $password;
    private static array $driverOptions;

    private static Manager $instance;

    private function __construct(string $dsn, string $user, string $password, array $driverOptions)
    {
        self::$dsn = $dsn;
        self::$user = $user;
        self::$password = $password;
        self::$driverOptions = $driverOptions;
        try {
            $this->_pdo = new PDO($dsn, $user, $password, $driverOptions);
            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    public static function getManager(string $dsn, string $user, string $password, array $driverOptions):Manager {
        if(!isset(self::$instance)) {
            self::$instance = new self($dsn, $user, $password, $driverOptions);
        }
        return self::$instance;
    }

    public static function restart():Manager {
        return new self(self::$dsn, self::$user, self::$password, self::$driverOptions);

    }

    public function getConnection(): PDO
    {
        return $this->_pdo;
    }

}