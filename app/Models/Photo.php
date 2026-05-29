<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'filename',
        'storage_path',
        'latitude',
        'longitude',
        'taken_at',
        'description',
        'file_size',
        'mime_type',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'taken_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function getFullUrl(): string
    {
        return config('filesystems.disks.yandex.url') . '/' . $this->storage_path;
    }
}