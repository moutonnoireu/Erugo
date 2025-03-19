<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class backUpDatabase implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        Log::info('Backing up database');

        //the path of the sqlite database (from laravels settings)
        $databasePath = config('database.connections.sqlite.database');

        //create a backup of the database
        $backupPath = storage_path('app/backups/');

        //check the backups directory exists
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0777, true);
        }

        $backupName = 'database_backup_' . now()->format('Y-m-d_H-i-s') . '.sqlite';
        copy($databasePath, $backupPath . $backupName);

        Log::info('Database backup created: ' . $backupName);

        //delete any backups over 7 days old
        $backups = glob($backupPath . '*.sqlite');
        $prunedBackups = 0;
        foreach ($backups as $backup) {
            if (filemtime($backup) < now()->subDays(7)->timestamp) {
                unlink($backup);
                $prunedBackups++;
            }
        }

        Log::info('Database backups pruned: ' . $prunedBackups);
    }
}
