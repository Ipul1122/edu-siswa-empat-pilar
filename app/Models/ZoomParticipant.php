<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZoomParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'zoom_session_id',
        'user_id',
        'joined_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
        ];
    }

    /**
     * Relationship to the Zoom Session.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(ZoomSession::class, 'zoom_session_id');
    }

    /**
     * Relationship to the User (Student).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
