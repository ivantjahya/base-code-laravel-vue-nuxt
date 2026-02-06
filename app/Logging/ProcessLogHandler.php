<?php

namespace App\Logging;

use App\Models\ProcessLog;
use Monolog\Logger;

class ProcessLogHandler
{
    /**
     * Create a custom Monolog instance.
     */
    public function __invoke(array $config): Logger
    {
        $logger = new Logger('process');
        $logger->pushHandler(new DatabaseHandler(ProcessLog::class, $config['level'] ?? Logger::DEBUG));

        return $logger;
    }
}
