<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_id',
        'name',
        'notes',
        'sort_order',
        'elements',
    ];

    protected function casts(): array
    {
        return [
            'elements' => 'array',
        ];
    }

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function hasPhotos(): bool
    {
        return $this->photos()->count() > 0;
    }

    public function getPhotosCount(): int
    {
        return $this->photos()->count();
    }
}