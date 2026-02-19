<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * ClineService - Service for executing Cline CLI commands and shell commands
 */
class ClineService
{
    protected int $defaultTimeout;
    protected int $maxExecutionTime;
    protected string $workingDirectory;
    protected string $clineBinary;

    public function __construct()
    {
        $this->defaultTimeout = (int) env('CLINE_TIMEOUT', 300);
        $this->maxExecutionTime = (int) env('CLINE_MAX_EXECUTION_TIME', 600);
        $this->workingDirectory = base_path();
        $this->clineBinary = $this->findClineBinary();
    }

    protected function findClineBinary(): string
    {
        $output = shell_exec('which cline 2>/dev/null');
        if (!empty($output)) {
            return trim($output);
        }
        return 'cline';
    }

    /**
     * Execute a Cline AI task
     */
    public function execute(string $task, array $options = []): array
    {
        $taskId = Str::uuid()->toString();
        $timeout = min(
            $options['timeout'] ?? $this->defaultTimeout,
            $this->maxExecutionTime
        );
        $workingDir = $options['working_directory'] ?? $this->workingDirectory;

        $startTime = microtime(true);

        Log::info('Cline CLI: Starting execution', [
            'task_id' => $taskId,
            'task' => $task,
            'working_dir' => $workingDir,
        ]);

        try {
            // Check if Cline is available
            $availability = $this->checkAvailability();
            if (!$availability['available']) {
                return [
                    'success' => false,
                    'task_id' => $taskId,
                    'output' => '',
                    'error' => 'Cline CLI is not available: ' . implode(', ', $availability['errors']),
                    'exit_code' => 1,
                    'execution_time' => 0,
                    'debug' => [
                        'cline_available' => false,
                        'availability' => $availability,
                    ],
                ];
            }

            // Note: Cline CLI uses its own config (~/.cline/) from 'cline auth'
            // API keys in Laravel .env are optional - they can be passed as env vars

            $command = $this->buildClineCommand($task, $options);

            Log::info('Cline CLI: Command', ['command' => $command]);

            $output = shell_exec($command);

            $executionTime = microtime(true) - $startTime;

            $rawOutput = $output ?? '';
            $output = $this->cleanOutput($rawOutput);

            // Check for various success indicators
            $success = $this->detectSuccess($output, $executionTime);

            Log::info('Cline CLI: Execution completed', [
                'task_id' => $taskId,
                'success' => $success,
                'execution_time' => $executionTime,
                'output_length' => strlen($output),
            ]);

            return [
                'success' => $success,
                'task_id' => $taskId,
                'output' => trim($output),
                'error' => $success ? '' : 'Command completed but success could not be verified',
                'exit_code' => $success ? 0 : 1,
                'execution_time' => round($executionTime, 2),
                'command' => $command,
                'debug' => [
                    'cline_version' => $availability['version'] ?? null,
                    'api_keys' => $availability['api_keys_configured'],
                    'output_length' => strlen($output),
                ],
            ];

        } catch (\Exception $e) {
            $executionTime = microtime(true) - $startTime;

            Log::error('Cline CLI: Execution failed', [
                'task_id' => $taskId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'task_id' => $taskId,
                'output' => '',
                'error' => $e->getMessage(),
                'exit_code' => 1,
                'execution_time' => round($executionTime, 2),
            ];
        }
    }

    /**
     * Detect if the execution was successful based on output
     */
    protected function detectSuccess(string $output, float $executionTime): bool
    {
        // Check for explicit success messages
        $successPatterns = [
            'Successfully',
            'Task completed',
            'Done',
            'Created',
            'Updated',
            'File created',
            'Migration created',
        ];

        foreach ($successPatterns as $pattern) {
            if (str_contains($output, $pattern)) {
                return true;
            }
        }

        // Check for error messages
        $errorPatterns = [
            'Error:',
            'Failed',
            'Exception',
            'Unable to',
            'API key',
            'authentication',
            'rate limit',
        ];

        foreach ($errorPatterns as $pattern) {
            if (stripos($output, $pattern) !== false) {
                return false;
            }
        }

        // If execution took reasonable time and there's output, consider it success
        if ($executionTime > 2 && strlen($output) > 50) {
            return true;
        }

        // If empty output but execution took time, might still be success
        if ($executionTime > 5 && empty($output)) {
            return false; // Likely an issue
        }

        return false;
    }

    /**
     * Execute a shell command directly
     */
    public function executeCommand(string $command, array $options = []): array
    {
        $taskId = Str::uuid()->toString();
        $timeout = min(
            $options['timeout'] ?? 60,
            $this->maxExecutionTime
        );
        $workingDir = $options['working_directory'] ?? $this->workingDirectory;

        $startTime = microtime(true);

        Log::info('Shell: Starting execution', [
            'task_id' => $taskId,
            'command' => $command,
        ]);

        try {
            // Validate command for security
            $validation = $this->validateCommand($command);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'task_id' => $taskId,
                    'output' => '',
                    'error' => implode('; ', $validation['errors']),
                    'exit_code' => 1,
                    'execution_time' => 0,
                    'command' => $command,
                ];
            }

            // Build safe command
            $safeCommand = sprintf(
                'cd %s && timeout %d sh -c %s 2>&1',
                escapeshellarg($workingDir),
                $timeout,
                escapeshellarg($command)
            );

            Log::info('Shell: Safe command', ['safe_command' => $safeCommand]);

            $output = shell_exec($safeCommand);

            $executionTime = microtime(true) - $startTime;

            $output = $this->cleanOutput($output ?? '');

            // Success if output exists or no error
            $success = !empty($output) || $executionTime < $timeout;

            Log::info('Shell: Execution completed', [
                'task_id' => $taskId,
                'success' => $success,
                'execution_time' => $executionTime,
            ]);

            return [
                'success' => $success,
                'task_id' => $taskId,
                'output' => trim($output),
                'error' => '',
                'exit_code' => $success ? 0 : 1,
                'execution_time' => round($executionTime, 2),
                'command' => $command,
            ];

        } catch (\Exception $e) {
            $executionTime = microtime(true) - $startTime;

            Log::error('Shell: Execution failed', [
                'task_id' => $taskId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'task_id' => $taskId,
                'output' => '',
                'error' => $e->getMessage(),
                'exit_code' => 1,
                'execution_time' => round($executionTime, 2),
            ];
        }
    }

    /**
     * Execute an Artisan command
     */
    public function executeArtisan(string $command, array $options = []): array
    {
        // Prefix with php artisan
        $artisanCommand = 'php artisan ' . $command;
        return $this->executeCommand($artisanCommand, $options);
    }

    /**
     * Execute a Composer command
     */
    public function executeComposer(string $command, array $options = []): array
    {
        $composerCommand = 'composer ' . $command;
        return $this->executeCommand($composerCommand, $options);
    }

    /**
     * Execute an NPM command
     */
    public function executeNpm(string $command, array $options = []): array
    {
        $npmCommand = 'npm ' . $command;
        return $this->executeCommand($npmCommand, $options);
    }

    /**
     * Execute a Git command
     */
    public function executeGit(string $command, array $options = []): array
    {
        $gitCommand = 'git ' . $command;
        return $this->executeCommand($gitCommand, $options);
    }

    /**
     * Get list of allowed commands
     */
    public function getAllowedCommands(): array
    {
        return [
            'artisan' => [
                'migrate', 'migrate:rollback', 'migrate:fresh', 'migrate:reset',
                'db:seed', 'db:wipe',
                'make:model', 'make:controller', 'make:migration', 'make:seeder',
                'make:factory', 'make:middleware', 'make:command', 'make:event',
                'make:listener', 'make:job', 'make:mail', 'make:notification',
                'make:policy', 'make:request', 'make:resource', 'make:rule',
                'cache:clear', 'config:clear', 'route:clear', 'view:clear',
                'route:list', 'config:cache', 'route:cache', 'view:cache',
                'queue:work', 'queue:listen', 'queue:restart',
                'schedule:run', 'schedule:list',
                'storage:link', 'vendor:publish',
                'tinker', 'down', 'up', 'env',
                'key:generate', 'optimize', 'optimize:clear',
            ],
            'composer' => [
                'install', 'update', 'require', 'remove', 'dump-autoload',
                'show', ' outdated', 'validate',
            ],
            'npm' => [
                'install', 'update', 'run', 'run-script', 'list', 'outdated',
            ],
            'git' => [
                'status', 'log', 'diff', 'branch', 'tag', 'remote', 'stash',
                'pull', 'fetch', 'clone',
            ],
            'shell' => [
                'ls', 'cat', 'pwd', 'echo', 'find', 'grep', 'head', 'tail',
                'mkdir', 'touch', 'cp', 'mv', 'chmod', 'chown',
                'php -v', 'php -m', 'node -v', 'npm -v', 'composer -V',
            ],
        ];
    }

    /**
     * Validate command for security
     */
    public function validateCommand(string $command): array
    {
        $errors = [];

        // Dangerous patterns that are always blocked
        $blockedPatterns = [
            '/rm\s+-rf\s+\//',
            '/rm\s+-rf\s+~/',
            '/>\s*\/dev\/sd/',
            '/mkfs/',
            '/dd\s+if=/',
            '#:\(\)\{.*:\|:&\};:#',  // Fork bomb - using # as delimiter
            '/chmod\s+[-+]?000/',
            '/chown\s+.*:.*\s+\//',
            '/shutdown/',
            '/reboot/',
            '/init\s+[06]/',
            '/systemctl\s+(stop|disable|mask)/',
            '/service\s+\w+\s+stop/',
            '/iptables/',
            '/ufw\s+disable/',
            '/crontab\s+-r/',
            '/userdel/',
            '/passwd/',
            '/visudo/',
            '/wget.*\|\s*(ba)?sh/',
            '/curl.*\|\s*(ba)?sh/',
        ];

        foreach ($blockedPatterns as $pattern) {
            if (preg_match($pattern, $command)) {
                $errors[] = 'Command contains blocked pattern for security reasons';
                break;
            }
        }

        // Check for sudo
        if (str_contains($command, 'sudo ')) {
            $errors[] = 'sudo is not allowed';
        }

        // Check for redirection to sensitive files
        if (preg_match('/>\s*\/etc\//', $command)) {
            $errors[] = 'Writing to /etc is not allowed';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    protected function cleanOutput(string $output): string
    {
        // Remove ANSI escape sequences
        $output = preg_replace('/\x1b\[[0-9;]*[a-zA-Z]/', '', $output);
        // Remove control characters except newlines
        $output = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $output);
        return $output;
    }

    protected function buildClineCommand(string $task, array $options = []): string
    {
        $parts = [];
        
        // Change to working directory first
        $workingDir = $options['working_directory'] ?? $this->workingDirectory;
        $parts[] = 'cd ' . escapeshellarg($workingDir) . ' &&';
        
        // Build environment variables (includes HOME and API keys)
        $envVars = $this->buildEnvironmentVars();
        if (!empty($envVars)) {
            $parts[] = $envVars;
        }
        
        $parts[] = 'unbuffer';
        $parts[] = $this->clineBinary;
        $parts[] = '--config';
        $parts[] = '/var/www/.cline';
        $parts[] = '--yolo';

        $timeout = $options['timeout'] ?? $this->defaultTimeout;
        $parts[] = '--timeout';
        $parts[] = (string) $timeout;

        if (!empty($options['model'])) {
            $parts[] = '--model';
            $parts[] = escapeshellarg($options['model']);
        }

        if (!empty($options['mode'])) {
            $parts[] = '--' . $options['mode'];
        }

        $parts[] = escapeshellarg($task);
        
        $command = implode(' ', $parts);
        $command .= ' 2>&1';

        return $command;
    }
    
    /**
     * Build environment variables for API keys
     */
    protected function buildEnvironmentVars(): string
    {
        $vars = [];
        
        // Set HOME first so Cline can find its config
        $vars[] = 'HOME=/var/www';
        
        // Map Laravel env vars to Cline expected vars
        $mapping = [
            'ANTHROPIC_API_KEY' => 'ANTHROPIC_API_KEY',
            'OPENAI_API_KEY' => 'OPENAI_API_KEY',
            'DEEPSEEK_API_KEY' => 'DEEPSEEK_API_KEY',
            'OPENROUTER_API_KEY' => 'OPENROUTER_API_KEY',
        ];
        
        foreach ($mapping as $laravelKey => $clineKey) {
            $value = env($laravelKey);
            if (!empty($value) && $value !== 'your-deepseek-api-key-here') {
                $vars[] = "$clineKey=" . escapeshellarg($value);
            }
        }
        
        return implode(' ', $vars);
    }

    public function checkAvailability(): array
    {
        $status = [
            'available' => false,
            'version' => null,
            'path' => null,
            'api_keys_configured' => [],
            'errors' => [],
        ];

        $output = shell_exec("{$this->clineBinary} --version 2>/dev/null");

        if (!empty($output)) {
            $status['available'] = true;
            $status['version'] = trim($output);
            $status['path'] = $this->clineBinary;
        } else {
            $status['errors'][] = 'Cline CLI not found';
        }

        // Check Laravel env vars (optional - Cline has its own config)
        if (env('ANTHROPIC_API_KEY')) $status['api_keys_configured'][] = 'anthropic';
        if (env('OPENROUTER_API_KEY')) $status['api_keys_configured'][] = 'openrouter';
        if (env('OPENAI_API_KEY')) $status['api_keys_configured'][] = 'openai';
        if (env('DEEPSEEK_API_KEY')) $status['api_keys_configured'][] = 'deepseek';
        
        // Check Cline's own config
        $clineConfigPath = $_SERVER['HOME'] ?? '/root';
        $clineConfigFile = $clineConfigPath . '/.cline/config.json';
        if (file_exists($clineConfigFile)) {
            $status['cline_config_exists'] = true;
            $config = json_decode(file_get_contents($clineConfigFile), true);
            if (!empty($config['apiKey'])) {
                $status['api_keys_configured'][] = 'cline-config';
            }
        }
        
        // Also check /var/www/.cline for www-data
        $wwwClineConfig = '/var/www/.cline/config.json';
        if (file_exists($wwwClineConfig)) {
            $status['www_cline_config_exists'] = true;
            $config = json_decode(file_get_contents($wwwClineConfig), true);
            if (!empty($config['apiKey'])) {
                $status['api_keys_configured'][] = 'www-cline-config';
            }
        }

        return $status;
    }

    public function validateTask(string $task): array
    {
        $errors = [];

        if (empty(trim($task))) {
            $errors[] = 'Task cannot be empty';
        }

        if (strlen($task) > 10000) {
            $errors[] = 'Task description is too long (max 10000 characters)';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
