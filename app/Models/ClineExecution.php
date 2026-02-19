<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ClineExecution model for storing execution history
 *
 * @property string $id
 * @property string $task_id
 * @property string $task
 * @property bool $success
 * @property string|null $output
 * @property string|null $error
 * @property int $exit_code
 * @property float $execution_time
 * @property string|null $working_directory
 * @property string|null $provider
 * @property string|null $model
 * @property array|null $options
 */
class ClineExecution extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task_id',
        'task',
        'success',
        'output',
        'error',
        'exit_code',
        'execution_time',
        'working_directory',
        'provider',
        'model',
        'options',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'success' => 'boolean',
        'execution_time' => 'decimal:2',
        'exit_code' => 'integer',
        'options' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get successful executions
     */
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    /**
     * Get failed executions
     */
    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    /**
     * Get executions by provider
     */
    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Get recent executions
     */
    public function scopeRecent($query, int $limit = 20)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}
