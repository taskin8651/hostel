<?php

namespace App\Models\Hostel;

class Fee extends BaseHostelModel
{
    protected $table = 'hostel_fees';

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
