<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup old log files based on retention policy';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $logsPath = storage_path('logs');

        if (!File::isDirectory($logsPath)) {
            $this->error('Logs directory not found');
            return 1;
        }

        // Define retention policies (in days)
        $policies = [
            'laravel*.log' => 14,      // 2 weeks
            'audit*.log' => 90,        // 3 months
            'errors*.log' => 30,       // 1 month
        ];

        $now = now();
        $deletedCount = 0;

        foreach ($policies as $pattern => $days) {
            $files = File::glob($logsPath . '/' . $pattern);
            
            foreach ($files as $file) {
                $lastModified = File::lastModified($file);
                $lastModifiedDate = \Carbon\Carbon::createFromTimestamp($lastModified);
                
                // Delete if older than retention days
                if ($lastModifiedDate->addDays($days)->isPast()) {
                    File::delete($file);
                    $deletedCount++;
                    $this->line("Deleted: " . basename($file));
                }
            }
        }

        $this->info("Cleanup complete. Deleted {$deletedCount} log files.");
        return 0;
    }
}
