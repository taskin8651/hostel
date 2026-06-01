<?php

namespace App\Models\Hostel;

use App\Models\User;

class Staff extends BaseHostelModel
{
    protected $table = 'hostel_staff';

    protected $casts = [
        'joining_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function payments()
    {
        return $this->hasMany(StaffPayment::class, 'staff_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function works()
    {
        return $this->hasMany(StaffWork::class, 'staff_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'staff_id');
    }
}
