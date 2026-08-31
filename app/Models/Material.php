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
            'twk_kedinasan' => 'Simulasi TWK Kedinasan',
            default => ucfirst(str_replace('_', ' ', $this->pillar)),
        };
    }

    /**
     * Determine if the video is a local uploaded file or direct video file.
     */
    public function getIsDirectVideoAttribute(): bool
    {
        if (!$this->video_url) {
            return false;
        }

        $url = strtolower($this->video_url);
        return str_starts_with($this->video_url, 'storage/') 
            || str_ends_with($url, '.mp4') 
            || str_ends_with($url, '.webm') 
            || str_ends_with($url, '.mov') 
            || str_ends_with($url, '.ogg');
    }

    /**
     * Determine if the video is from YouTube.
     */
    public function getIsYoutubeVideoAttribute(): bool
    {
        if (!$this->video_url) {
            return false;
        }

        return str_contains($this->video_url, 'youtube.com') || str_contains($this->video_url, 'youtu.be');
    }

    /**
     * Get accessible video URL for playback.
     */
    public function getPlayableVideoUrlAttribute(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        if (str_starts_with($this->video_url, 'storage/')) {
            return asset($this->video_url);
        }

        return $this->video_url;
    }

    /**
     * Helper to get YouTube embed URL.
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (!$this->video_url || $this->is_direct_video) {
            return null;
        }

        // Regular expression to parse YouTube ID from various YouTube URL formats
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
        if (preg_match($pattern, $this->video_url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        return null;
    }
}
