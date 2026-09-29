<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ZoomSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'pillar',
        'zoom_link',
        'meeting_id',
        'passcode',
        'capacity',
        'start_time',
        'end_time',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'is_active' => 'boolean',
            'capacity' => 'integer',
        ];
    }

    /**
     * Relationship to participants (Students / Users who joined).
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'zoom_participants')
            ->withPivot(['id', 'joined_at', 'notes'])
            ->withTimestamps();
    }

    /**
     * Relationship to participant attendance records.
     */
    public function participantRecords(): HasMany
    {
        return $this->hasMany(ZoomParticipant::class);
    }

    /**
     * Admin creator of this Zoom Session.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if a specific student/user has joined this session.
     */
    public function hasUserJoined(int $userId): bool
    {
        if ($this->relationLoaded('participants')) {
            return $this->participants->contains('id', $userId);
        }

        return $this->participants()->where('users.id', $userId)->exists();
    }

    /**
     * Helper accessor for joined count.
     */
    public function getJoinedCountAttribute(): int
    {
        return $this->participants_count ?? $this->participants()->count();
    }

    /**
     * Helper accessor for remaining seats.
     */
    public function getRemainingSeatsAttribute(): int
    {
        return max(0, $this->capacity - $this->joined_count);
    }

    /**
     * Check if the session is fully booked.
     */
    public function getIsFullAttribute(): bool
    {
        return $this->joined_count >= $this->capacity;
    }

    /**
     * Helper accessor for percentage filled.
     */
    public function getFillPercentageAttribute(): int
    {
        if ($this->capacity <= 0) {
            return 100;
        }

        return (int) min(100, round(($this->joined_count / $this->capacity) * 100));
    }

    /**
     * Formatted pillar name.
     */
    public function getFormattedPillarAttribute(): string
    {
        return match ($this->pillar) {
            'pancasila' => 'Pancasila',
            'uud_1945' => 'UUD NRI 1945',
            'nkri' => 'NKRI & Wawasan Nusantara',
            'bhinneka_tunggal_ika' => 'Bhinneka Tunggal Ika',
            'twk_kedinasan' => 'Simulasi TWK Kedinasan',
            default => 'Umum / Semua Pilar',
        };
    }

    /**
     * Helper status badge for sessions.
     */
    public function getStatusLabelAttribute(): array
    {
        if (!$this->is_active) {
            return ['label' => 'Nonaktif', 'class' => 'badge-danger'];
        }

        $now = now();
        if ($now->lt($this->start_time)) {
            return ['label' => 'Terjadwal', 'class' => 'badge-info'];
        }

        if ($this->end_time && $now->gt($this->end_time)) {
            return ['label' => 'Selesai', 'class' => 'badge-secondary'];
        }

        return ['label' => 'Berlangsung', 'class' => 'badge-success'];
    }
}
