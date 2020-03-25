<?php

declare(strict_types=1);

namespace AOM\CronManager\Lib;

use AOM\CronManager\Lib\Database;
use DirectoryIterator;
use Psr\Log\LoggerInterface;

class CronIterator
{
    private string $folder;

    private string $namespace;

    private Database\Manager $_dm;

    private LoggerInterface $logger;

    public function __construct(
        string $folder,
        string $namespace,
        Database\Manager $databaseManager,
        LoggerInterface $logger
    )
    {
        $this->folder = $folder;
        $this->namespace = $namespace;
        $this->_dm = $databaseManager;
        $this->logger = $logger;
    }

    public function getCrons() {
        $iterator = [];
        foreach (new DirectoryIterator($this->folder) as $fileInfo) {
            if($fileInfo->isDot()) {
                continue;
            }
            $filename = pathinfo($fileInfo->getFilename())['filename'];
            $class = $this->namespace.'\\'.$filename;
            $iterator[] = new $class($this->_dm, $this->logger);
        }
        return $iterator;
    }
}