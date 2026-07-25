<?php

use Illuminate\Support\Facades\Schedule;

// Daily: send renewal reminders and expire ended subscriptions.
// In production make sure the scheduler runs: `* * * * * php artisan schedule:run`
Schedule::command('subscriptions:process')->dailyAt('02:00');
