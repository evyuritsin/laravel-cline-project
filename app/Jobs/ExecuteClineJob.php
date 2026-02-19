<?php

namespace App\Jobs;

use App\Models\ClineExecution;
use App\Services\ClineService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * ExecuteClineJob - Asynchronous job for executing Cline CLI commands
 *
 * This job handles long-running Cline CLI tasks in the background
 * to prevent blocking HTTP requests.
 */
class ExecuteClineJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The task to execute
     */
    protected string $task;

    /**
     * Execution options
     */
    protected array $options;

    /**
     * Task ID for tracking
     */
    protected string $taskId;

    /**
     * Maximum number of attempts
     */
    public int $tries = 1;

    /**
     * Job timeout in seconds
     */
    public int $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(string $taskId, string $task, array $options = [])
    {
        $this->taskId = $taskId;
        $this->task = $task;
        $this->options = $options;
    }

    /**
     * Execute the job.
     */
    public function handle(ClineService $clineService): void
    {
        Log::info('ClineJob: Starting execution', [
            'task_id' => $this->taskId,
        ]);

        try {
            $result = $clineService->execute($this->task, $this->options);

            // Store result in database
            ClineExecution::create([
                'task_id' => $this->taskId,
                'task' => $this->task,
                'success' => $result['success'],
                'output' => $result['output'],
                'error' => $result['error'],
                'exit_code' => $result['exit_code'],
                'execution_time' => $result['execution_time'],
                'working_directory' => $this->options['working_directory'] ?? null,
                'provider' => $this->options['provider'] ?? null,
                'model' => $this->options['model'] ?? null,
                'options' => $this->options,
            ]);

            Log::info('ClineJob: Execution completed', [
                'task_id' => $this->taskId,
                'success' => $result['success'],
            ]);

        } catch (\Exception $e) {
            Log::error('ClineJob: Execution failed', [
                'task_id' => $this->taskId,
                'error' => $e->getMessage(),
            ]);

            // Store failure record
            ClineExecution::create([
                'task_id' => $this->taskId,
                'task' => $this->task,
                'success' => false,
                'error' => $e->getMessage(),
                'exit_code' => 1,
                'working_directory' => $this->options['working_directory'] ?? null,
                'options' => $this->options,
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ClineJob: Job failed', [
            'task_id' => $this->taskId,
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return ['cline', "task:{$this->taskId}"];
    }
}
