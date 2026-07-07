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
        'type',
        'video_url',
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

    /**
     * Helper to get YouTube embed URL.
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        // Regular expression to parse YouTube ID from various YouTube URL formats
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
        if (preg_match($pattern, $this->video_url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        // Return original if it is already an embed URL or doesn't match
        return $this->video_url;
    }
}
