<?php

declare(strict_types=1);

use App\Console\Commands\DeleteNonEmailVerifiedUsersCommand;
use App\Console\Commands\PerformDatabaseBackupCommand;
use App\Console\Commands\SendReminderEmailsCommand;
use App\Console\Commands\SyncVerifiedUsersCommand;
use App\Jobs\CleanUnusedUploadedImages;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SendReminderEmailsCommand::class)->dailyAt('13:00');
Schedule::command(SendReminderEmailsCommand::class, ['--weekly' => true])->weekly()->mondays()->at('13:00');
Schedule::command(PerformDatabaseBackupCommand::class)->everySixHours();
Schedule::command(DeleteNonEmailVerifiedUsersCommand::class)->hourly();
Schedule::command(SyncVerifiedUsersCommand::class)->daily();
Schedule::job(CleanUnusedUploadedImages::class)->hourly();
