<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AppointmentBooking extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'available_slot_id',
        'patient_id',
        'status',
        'notes',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the available slot for this booking.
     */
    public function availableSlot()
    {
        return $this->belongsTo(AvailableSlot::class);
    }

    /**
     * Get the patient for this booking.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Scope to get bookings by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get bookings for a specific patient.
     */
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    /**
     * Scope to get bookings for a specific doctor.
     */
    public function scopeForDoctor($query, $doctorId)
    {
        return $query->whereHas('availableSlot', function ($q) use ($doctorId) {
            $q->where('doctor_id', $doctorId);
        });
    }

    /**
     * Scope to get bookings within a date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereHas('availableSlot', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate]);
        });
    }
}
