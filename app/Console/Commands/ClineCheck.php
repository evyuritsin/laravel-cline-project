<?php

namespace App\Console\Commands;

use App\Services\ClineService;
use Illuminate\Console\Command;

class ClineCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cline:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Cline CLI availability and configuration';

    /**
     * Execute the console command.
     */
    public function handle(ClineService $clineService): int
    {
        $this->info('Checking Cline CLI configuration...');
        $this->newLine();

        $status = $clineService->checkAvailability();

        // Display availability status
        if ($status['available']) {
            $this->components->info('Cline CLI is available');
            $this->line("  Version: <info>{$status['version']}</info>");
            $this->line("  Path: <info>{$status['path']}</info>");
        } else {
            $this->components->error('Cline CLI is NOT available');
        }

        $this->newLine();

        // Display API keys status
        $this->info('API Keys Configuration:');
        if (!empty($status['api_keys_configured'])) {
            foreach ($status['api_keys_configured'] as $provider) {
                $this->line("  - <info>{$provider}</info>: configured");
            }
        } else {
            $this->line('  <comment>No API keys configured</comment>');
        }

        $this->newLine();

        // Display errors
        if (!empty($status['errors'])) {
            $this->components->error('Issues found:');
            foreach ($status['errors'] as $error) {
                $this->line("  - <error>{$error}</error>");
            }
        }

        // Summary
        $this->newLine();
        if ($status['available'] && !empty($status['api_keys_configured'])) {
            $this->components->info('Cline CLI is properly configured and ready to use!');
            return Command::SUCCESS;
        } else {
            $this->components->warn('Cline CLI needs additional configuration.');
            return Command::FAILURE;
        }
    }
}
