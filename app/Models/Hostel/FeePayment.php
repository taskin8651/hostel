<?php

namespace App\Models\Hostel;

class FeePayment extends BaseHostelModel
{
    protected $table = 'hostel_fee_payments';

    protected $casts = [
        'payment_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
