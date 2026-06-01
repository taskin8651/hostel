<?php

namespace App\Models\Hostel;

class Room extends BaseHostelModel
{
    protected $table = 'hostel_rooms';

    public function beds()
    {
        return $this->hasMany(Bed::class, 'room_id');
    }

    public function allocations()
    {
        return $this->hasMany(RoomAllocation::class, 'room_id');
    }

    public function getOccupiedBedsAttribute(): int
    {
        return $this->beds()->where('status', 'occupied')->count();
    }

    public function getAvailableBedsAttribute(): int
    {
        return max((int) $this->total_beds - $this->occupied_beds, 0);
    }
}
