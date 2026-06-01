<?php

namespace App\Models\Hostel;

use App\Models\User;

class Student extends BaseHostelModel
{
    protected $table = 'hostel_students';

    protected $casts = [
        'dob' => 'date',
        'joining_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function allocations()
    {
        return $this->hasMany(RoomAllocation::class, 'student_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function activeAllocation()
    {
        return $this->hasOne(RoomAllocation::class, 'student_id')->where('status', 'active')->latestOfMany();
    }

    public function feeSetup()
    {
        return $this->hasOne(Fee::class, 'student_id')->latestOfMany();
    }

    public function feePayments()
    {
        return $this->hasMany(FeePayment::class, 'student_id');
    }
}
