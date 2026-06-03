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

    protected $fillable = [

        'student_id',

        'receipt_number',

        'month',

        'paid_amount',

        'due_amount',

        'payment_date',

        'payment_mode',

        'attachment',

        'remark',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
