<?php

namespace App\Models\Hostel;

class Branch extends BaseHostelModel
{
    protected $table = 'hostel_branches';

    public function rooms()
    {
        return $this->hasMany(Room::class, 'branch_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'branch_id');
    }

    public function accessories()
    {
        return $this->hasMany(Accessory::class, 'branch_id');
    }

    public function allocations()
    {
        return $this->hasMany(RoomAllocation::class, 'branch_id');
    }
}
