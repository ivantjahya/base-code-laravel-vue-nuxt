<?php

namespace App\Logging;

use App\Models\UserLog;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\LogRecord;

class DatabaseHandler extends AbstractProcessingHandler
{
    protected string $model;

    public function __construct(string $model, $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->model = $model;
    }

    protected function write(LogRecord $record): void
    {
        $data = [
            'message' => $record->message,
            'channel' => $record->channel,
            'level' => $record->level->value,
            'level_name' => $record->level->getName(),
            'datetime' => $record->datetime->format('Y-m-d H:i:s'),
            'context' => $record->context,
            'extra' => $record->extra,
        ];

        // Add user_id if authenticated and model is UserLog
        if ($this->model === UserLog::class && auth()->check()) {
            $data['user_id'] = auth()->id();
        }

        $this->model::create($data);
    }
}
