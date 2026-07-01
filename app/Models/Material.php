<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'pillar',
        'title',
        'content',
        'read_time',
    ];

    /**
     * Relationship to student progress.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(StudentProgress::class);
    }

    /**
     * Helper to get formatted pillar name.
     */
    public function getFormattedPillarAttribute(): string
    {
        return match ($this->pillar) {
            'pancasila' => 'Pancasila',
            'uud_1945' => 'UUD NRI 1945',
            'nkri' => 'Negara Kesatuan Republik Indonesia (NKRI)',
            'bhinneka_tunggal_ika' => 'Bhinneka Tunggal Ika',
            default => ucfirst(str_replace('_', ' ', $this->pillar)),
        };
    }
}
