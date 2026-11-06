<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProblemReport extends Model
{
    use SoftDeletes;

    public const CATEGORIES = [
        'Road or sidewalk',
        'Street lighting',
        'Drainage or flooding',
        'Waste or sanitation',
        'Public safety',
        'Other',
    ];

    public const STATUSES = ['open', 'in_progress', 'resolved', 'rejected'];

    protected $fillable = [
        'user_id', 'reporter_name', 'reporter_email', 'reporter_phone', 'category',
        'subject', 'description', 'location', 'status', 'staff_notes', 'processed_by', 'processed_at',
    ];

    protected function casts(): array
    {
        return ['processed_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
