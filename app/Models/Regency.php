<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Regency extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'province_id',
        'name',
        'type',
    ];

    /**
     * Relationship to Province.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Relationship to users (Siswa).
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Accessor to display formatted name with label (Kabupaten / Kota).
     */
    public function getFormattedNameAttribute(): string
    {
        return $this->name;
    }
}
