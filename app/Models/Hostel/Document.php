<?php

namespace App\Models\Hostel;

class Document extends BaseHostelModel
{
    protected $table = 'hostel_documents';

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
