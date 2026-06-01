<?php

namespace App\Models\Hostel;

class StaffPayment extends BaseHostelModel
{
    protected $table = 'hostel_staff_payments';

    protected $casts = [
        'payment_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function attendance()
    {
        return $this->belongsTo(StaffAttendance::class, 'staff_attendance_id');
    }
}
