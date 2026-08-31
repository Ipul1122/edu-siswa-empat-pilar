<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'pillar',
        'title',
        'description',
        'duration_minutes',
        'type',
    ];

    /**
     * Relationship to questions.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Relationship to attempts.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
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
            'twk_kedinasan' => 'Simulasi TWK Kedinasan',
            default => ucfirst(str_replace('_', ' ', $this->pillar)),
        };
    }
}
