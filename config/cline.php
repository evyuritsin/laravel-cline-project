<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cline CLI Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the Cline CLI integration.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Timeout
    |--------------------------------------------------------------------------
    |
    | The default timeout for Cline CLI execution in seconds.
    | This can be overridden per-request.
    |
    */
    'default_timeout' => env('CLINE_TIMEOUT', 300),

    /*
    |--------------------------------------------------------------------------
    | Maximum Execution Time
    |--------------------------------------------------------------------------
    |
    | The maximum allowed execution time for any single Cline CLI command.
    | This is a hard limit to prevent runaway processes.
    |
    */
    'max_execution_time' => env('CLINE_MAX_EXECUTION_TIME', 600),

    /*
    |--------------------------------------------------------------------------
    | Default Provider
    |--------------------------------------------------------------------------
    |
    | The default AI provider to use when none is specified.
    | Options: anthropic, openrouter, openai
    |
    */
    'default_provider' => env('CLINE_DEFAULT_PROVIDER', 'anthropic'),

    /*
    |--------------------------------------------------------------------------
    | Working Directory
    |--------------------------------------------------------------------------
    |
    | The default working directory for Cline CLI execution.
    | Usually this is the Laravel project root.
    |
    */
    'working_directory' => env('CLINE_WORKING_DIRECTORY', base_path()),

    /*
    |--------------------------------------------------------------------------
    | Store Execution History
    |--------------------------------------------------------------------------
    |
    | Whether to store execution history in the database.
    | Set to false to disable history logging.
    |
    */
    'store_history' => env('CLINE_STORE_HISTORY', true),

    /*
    |--------------------------------------------------------------------------
    | History Retention Days
    |--------------------------------------------------------------------------
    |
    | Number of days to keep execution history records.
    | Records older than this will be cleaned up by the scheduler.
    |
    */
    'history_retention_days' => env('CLINE_HISTORY_RETENTION_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Provider Models
    |--------------------------------------------------------------------------
    |
    | Default models to use for each provider.
    |
    */
    'models' => [
        'anthropic' => env('CLINE_ANTHROPIC_MODEL', 'claude-3-5-sonnet-20241022'),
        'openrouter' => env('CLINE_OPENROUTER_MODEL', 'anthropic/claude-3.5-sonnet'),
        'openai' => env('CLINE_OPENAI_MODEL', 'gpt-4-turbo'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security-related configuration for Cline CLI execution.
    |
    */
    'security' => [
        // Maximum length of task description
        'max_task_length' => 10000,

        // Allowed working directories (empty = all allowed)
        'allowed_directories' => [],

        // Patterns to block in task descriptions
        'blocked_patterns' => [
            '/rm\s+-rf/',
            '/sudo\s+/',
            '/chmod\s+777/',
            '/>\s*\/dev\//',
            '/mkfs/',
            '/dd\s+if=/',
        ],
    ],
];
