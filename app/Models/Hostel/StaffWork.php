<?php

namespace App\Models\Hostel;

class StaffWork extends BaseHostelModel
{
    protected $table = 'hostel_staff_works';

    protected $casts = [
        'assigned_date' => 'date',
        'completion_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
