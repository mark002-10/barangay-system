<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resident extends Model
{
    use HasFactory, SoftDeletes;

    public const GENDERS = ['Female', 'Male', 'Non-binary', 'Prefer not to say'];
    public const CIVIL_STATUSES = ['Single', 'Married', 'Widowed', 'Separated'];

    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'suffix', 'birth_date',
        'gender', 'civil_status', 'purok', 'address', 'phone', 'is_active',
    ];

    protected function casts(): array
    {
        return ['birth_date' => 'date', 'is_active' => 'boolean'];
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function getFullNameAttribute(): string
    {
        return collect([$this->first_name, $this->middle_name, $this->last_name, $this->suffix])
            ->filter()
            ->join(' ');
    }
}
