<?php

namespace App\Models\Hostel;

class Leave extends BaseHostelModel
{
    protected $table = 'hostel_leaves';

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [

        'person_type',

        'user_id',

        'student_id',

        'staff_id',

        'leave_type',

        'from_date',

        'to_date',

        'reason',

        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
