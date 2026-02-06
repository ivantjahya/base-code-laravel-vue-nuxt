<?php

namespace App\Logging;

use App\Models\UserLog;
use Monolog\Logger;

class UserLogHandler
{
    /**
     * Create a custom Monolog instance.
     */
    public function __invoke(array $config): Logger
    {
        $logger = new Logger('user');
        $logger->pushHandler(new DatabaseHandler(UserLog::class, $config['level'] ?? Logger::DEBUG));

        return $logger;
    }
}
