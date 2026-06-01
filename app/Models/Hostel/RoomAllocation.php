<?php

namespace App\Models\Hostel;

class RoomAllocation extends BaseHostelModel
{
    protected $table = 'hostel_room_allocations';

    protected $casts = [
        'allocation_date' => 'date',
        'shift_date' => 'date',
        'vacate_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function bed()
    {
        return $this->belongsTo(Bed::class, 'bed_id');
    }
}
