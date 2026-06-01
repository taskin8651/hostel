<?php

namespace App\Models\Hostel;

class StudentAttendance extends BaseHostelModel
{
    protected $table = 'hostel_student_attendance';

    protected $casts = [
        'attendance_date' => 'date',
        'attendance_datetime' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
