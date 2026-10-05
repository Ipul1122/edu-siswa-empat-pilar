<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'correct_answers',
        'total_questions',
        'duration_seconds_taken',
        'violations_count',
        'is_retest',
        'retest_reason',
        'retest_granted_by',
        'answers',
    ];

    protected $casts = [
        'answers' => 'array',
        'is_retest' => 'boolean',
    ];

    /**
     * Admin who granted retest.
     */
    public function retestGrantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'retest_granted_by');
    }

    /**
     * Relationship to User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to Quiz.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
