<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Physiotherapist extends Model
{
    use HasFactory;
    //    protected $fillable = [
    //        'user_id', 'license_number', 'hospital_or_clinic', 'specialty',
    //        'phone', 'city', 'country', 'credential_document_path',
    //        'profile_photo_path', 'vetting_status', 'rejection_reason',
    //        'vetted_at', 'activation_code',
    //    ];

    protected $guarded = [];

    protected $casts = [
        'vetted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function isVetted(): bool
    {
        return $this->vetting_status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->vetting_status === 'pending';
    }

    // Call this after approval to generate the PT's unique onboarding code
    public function generateActivationCode(): string
    {
        $code = strtoupper(Str::random(8));
        $this->update(['activation_code' => $code]);

        return $code;
    }

    public function exercisePlans()
    {
        return $this->hasMany(ExercisePlan::class);
    }
}
