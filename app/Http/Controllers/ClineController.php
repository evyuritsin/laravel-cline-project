<?php

namespace App\Http\Controllers;

use App\Jobs\ExecuteClineJob;
use App\Models\ClineExecution;
use App\Services\ClineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * ClineController - API Controller for Cline CLI and shell commands
 */
class ClineController extends Controller
{
    protected ClineService $clineService;

    public function __construct(ClineService $clineService)
    {
        $this->clineService = $clineService;
    }

    /**
     * Execute a Cline AI task
     */
    public function execute(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'task' => 'required|string|max:10000',
            'working_directory' => 'sometimes|string|max:500',
            'timeout' => 'sometimes|integer|min:10|max:600',
            'async' => 'sometimes|boolean',
            'options' => 'sometimes|array',
            'options.auto_approve' => 'sometimes|boolean',
            'options.provider' => 'sometimes|string|in:anthropic,openrouter,openai,deepseek',
            'options.model' => 'sometimes|string|max:100',
            'options.mode' => 'sometimes|string|in:act,plan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $task = $request->input('task');

        $taskValidation = $this->clineService->validateTask($task);
        if (!$taskValidation['valid']) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid task',
                'errors' => $taskValidation['errors'],
            ], 400);
        }

        $availability = $this->clineService->checkAvailability();
        if (!$availability['available']) {
            return response()->json([
                'success' => false,
                'error' => 'Cline CLI is not available',
                'details' => $availability['errors'],
            ], 503);
        }

        $options = [
            'timeout' => $request->input('timeout', 300),
            'working_directory' => $request->input('working_directory'),
            'model' => $request->input('options.model'),
            'mode' => $request->input('options.mode'),
        ];

        if ($request->input('async', false)) {
            return $this->executeAsync($task, $options);
        }

        $result = $this->clineService->execute($task, $options);
        $this->storeExecution($result, $task, $options);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Execute a shell command
     */
    public function command(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'command' => 'required|string|max:5000',
            'working_directory' => 'sometimes|string|max:500',
            'timeout' => 'sometimes|integer|min:1|max:300',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $command = $request->input('command');
        $options = [
            'timeout' => $request->input('timeout', 60),
            'working_directory' => $request->input('working_directory'),
        ];

        $result = $this->clineService->executeCommand($command, $options);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Execute an Artisan command
     */
    public function artisan(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'command' => 'required|string|max:1000',
            'timeout' => 'sometimes|integer|min:1|max:300',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $command = $request->input('command');
        $options = [
            'timeout' => $request->input('timeout', 120),
        ];

        $result = $this->clineService->executeArtisan($command, $options);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Execute a Composer command
     */
    public function composer(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'command' => 'required|string|max:1000',
            'timeout' => 'sometimes|integer|min:1|max:600',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $command = $request->input('command');
        $options = [
            'timeout' => $request->input('timeout', 300),
        ];

        $result = $this->clineService->executeComposer($command, $options);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Execute an NPM command
     */
    public function npm(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'command' => 'required|string|max:1000',
            'timeout' => 'sometimes|integer|min:1|max:600',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $command = $request->input('command');
        $options = [
            'timeout' => $request->input('timeout', 300),
        ];

        $result = $this->clineService->executeNpm($command, $options);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Execute a Git command
     */
    public function git(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'command' => 'required|string|max:1000',
            'timeout' => 'sometimes|integer|min:1|max:300',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $command = $request->input('command');
        $options = [
            'timeout' => $request->input('timeout', 120),
        ];

        $result = $this->clineService->executeGit($command, $options);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Get list of allowed commands
     */
    public function allowedCommands(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'commands' => $this->clineService->getAllowedCommands(),
        ]);
    }

    /**
     * Get task status
     */
    public function status(string $taskId): JsonResponse
    {
        $execution = ClineExecution::where('task_id', $taskId)->first();

        if (!$execution) {
            return response()->json([
                'success' => false,
                'error' => 'Execution not found',
                'task_id' => $taskId,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'task_id' => $taskId,
            'status' => $execution->success ? 'completed' : 'failed',
            'output' => $execution->output,
            'error' => $execution->error,
            'exit_code' => $execution->exit_code,
            'execution_time' => $execution->execution_time,
            'created_at' => $execution->created_at->toIso8601String(),
        ]);
    }

    /**
     * Get execution history
     */
    public function history(Request $request): JsonResponse
    {
        $limit = min($request->input('limit', 20), 100);
        $offset = $request->input('offset', 0);

        $query = ClineExecution::query()->orderBy('created_at', 'desc');

        if ($request->has('success')) {
            $query->where('success', $request->boolean('success'));
        }

        if ($request->has('provider')) {
            $query->where('provider', $request->input('provider'));
        }

        $total = $query->count();
        $executions = $query->offset($offset)->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data' => $executions->map(function ($execution) {
                return [
                    'task_id' => $execution->task_id,
                    'task' => $execution->task,
                    'success' => $execution->success,
                    'execution_time' => $execution->execution_time,
                    'provider' => $execution->provider,
                    'model' => $execution->model,
                    'created_at' => $execution->created_at->toIso8601String(),
                ];
            }),
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    /**
     * Check Cline CLI availability
     */
    public function check(): JsonResponse
    {
        $status = $this->clineService->checkAvailability();
        return response()->json($status, $status['available'] ? 200 : 503);
    }

    /**
     * Configure Cline settings
     */
    public function configure(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'sometimes|string|in:anthropic,openrouter,openai,deepseek',
            'model' => 'sometimes|string|max:100',
            'settings' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Configuration updated successfully',
            'applied_settings' => $request->only(['provider', 'model', 'settings']),
        ]);
    }

    /**
     * Get specific execution details
     */
    public function show(string $taskId): JsonResponse
    {
        $execution = ClineExecution::where('task_id', $taskId)->first();

        if (!$execution) {
            return response()->json([
                'success' => false,
                'error' => 'Execution not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $execution,
        ]);
    }

    /**
     * Delete execution record
     */
    public function destroy(string $taskId): JsonResponse
    {
        $execution = ClineExecution::where('task_id', $taskId)->first();

        if (!$execution) {
            return response()->json([
                'success' => false,
                'error' => 'Execution not found',
            ], 404);
        }

        $execution->delete();

        return response()->json([
            'success' => true,
            'message' => 'Execution record deleted',
        ]);
    }

    /**
     * Execute asynchronously
     */
    protected function executeAsync(string $task, array $options): JsonResponse
    {
        $taskId = \Illuminate\Support\Str::uuid()->toString();

        ExecuteClineJob::dispatch($taskId, $task, $options);

        return response()->json([
            'success' => true,
            'task_id' => $taskId,
            'status' => 'queued',
            'message' => 'Task has been queued for execution',
            'status_url' => route('api.cline.status', ['taskId' => $taskId]),
        ], 202);
    }

    /**
     * Store execution result
     */
    protected function storeExecution(array $result, string $task, array $options): void
    {
        try {
            ClineExecution::create([
                'task_id' => $result['task_id'],
                'task' => $task,
                'success' => $result['success'],
                'output' => $result['output'],
                'error' => $result['error'],
                'exit_code' => $result['exit_code'],
                'execution_time' => $result['execution_time'],
                'working_directory' => $options['working_directory'] ?? null,
                'provider' => $options['provider'] ?? null,
                'model' => $options['model'] ?? null,
                'options' => $options,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to store Cline execution', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
