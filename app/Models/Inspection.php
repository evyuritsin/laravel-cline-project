<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address',
        'latitude',
        'longitude',
        'type',
        'status',
        'inspection_date',
        'notes',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'inspection_date' => 'date',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class)->orderBy('sort_order');
    }

    public function report(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isMoveIn(): bool
    {
        return $this->type === 'move_in';
    }

    public function isMoveOut(): bool
    {
        return $this->type === 'move_out';
    }

    public function getTotalPhotosCount(): int
    {
        return $this->rooms()->with('photos')->get()->sum(fn($room) => $room->photos->count());
    }

    public function getTypeLabel(): string
    {
        return $this->isMoveIn() ? 'Заселение' : 'Выезд';
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'draft' => 'Черновик',
            'completed' => 'Завершён',
            'sent' => 'Отправлен',
            default => 'Неизвестно',
        };
    }
}