<?php

namespace App\Models\Hostel;

class StaffAttendance extends BaseHostelModel
{
    protected $table = 'hostel_staff_attendance';

    protected $casts = [
        'attendance_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
