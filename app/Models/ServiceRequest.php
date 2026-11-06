<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use SoftDeletes;

    public const STATUSES = ['pending', 'approved', 'rejected', 'issued'];

    public const CERTIFICATE_TYPES = [
        'Barangay Clearance',
        'Certificate of Residency',
        'Certificate of Indigency',
        'Business Clearance',
    ];

    protected $fillable = [
        'resident_id', 'requester_id', 'requester_name', 'requester_email', 'requester_phone', 'requester_address',
        'request_type', 'purpose', 'status', 'staff_notes', 'processed_by', 'processed_at',
    ];

    protected function casts(): array
    {
        return ['processed_at' => 'datetime'];
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class)->withTrashed();
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getResidentDisplayNameAttribute(): string
    {
        return $this->resident?->full_name ?: ($this->requester_name ?: 'Resident details pending');
    }

    public function isCertificate(): bool
    {
        return in_array($this->request_type, self::CERTIFICATE_TYPES, true);
    }
}
