<?php

namespace App\Console\Commands;

use App\Models\ClineExecution;
use Illuminate\Console\Command;

class ClineCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cline:cleanup
                            {--days=30 : Number of days to keep execution history}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old Cline CLI execution history records';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $cutoffDate = now()->subDays($days);

        $this->info("Cleaning up Cline execution history older than {$days} days...");
        $this->info("Cutoff date: {$cutoffDate->toDateTimeString()}");

        $query = ClineExecution::where('created_at', '<', $cutoffDate);
        $count = $query->count();

        if ($count === 0) {
            $this->info('No records to delete.');
            return Command::SUCCESS;
        }

        if ($dryRun) {
            $this->warn("[DRY RUN] Would delete {$count} record(s).");
            return Command::SUCCESS;
        }

        $deleted = $query->delete();
        $this->info("Deleted {$deleted} record(s).");

        return Command::SUCCESS;
    }
}
