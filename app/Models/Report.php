<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_id',
        'pdf_path',
        'pdf_url',
        'generated_at',
        'sent_to',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
            'sent_to' => 'array',
        ];
    }

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function getFullUrl(): string
    {
        return config('filesystems.disks.yandex.url') . '/' . $this->pdf_path;
    }
}