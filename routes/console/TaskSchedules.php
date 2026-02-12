<?php

use Illuminate\Support\Facades\Schedule;

/** Packages Cron */
Schedule::command('model:prune')->dailyAt('00:00');
