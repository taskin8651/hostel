<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HostelDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $roomId = DB::table('hostel_rooms')->updateOrInsert(
            ['room_number' => '101'],
            [
                'floor_number' => '1',
                'room_type' => 'double',
                'total_beds' => 2,
                'status' => 'occupied',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $room = DB::table('hostel_rooms')->where('room_number', '101')->first();

        foreach ([1, 2] as $bedNumber) {
            DB::table('hostel_beds')->updateOrInsert(
                ['room_id' => $room->id, 'bed_number' => (string) $bedNumber],
                ['status' => $bedNumber === 1 ? 'occupied' : 'vacant', 'created_at' => $now, 'updated_at' => $now]
            );
        }

        DB::table('hostel_students')->updateOrInsert(
            ['registration_no' => 'HMS-001'],
            [
                'name' => 'Rahul Sharma',
                'mobile' => '9000000001',
                'email' => 'rahul@example.com',
                'address' => 'Demo address',
                'guardian_name' => 'Mahesh Sharma',
                'guardian_mobile' => '9000000002',
                'course' => 'BCA',
                'joining_date' => now()->toDateString(),
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $student = DB::table('hostel_students')->where('registration_no', 'HMS-001')->first();
        $bed = DB::table('hostel_beds')->where('room_id', $room->id)->where('bed_number', '1')->first();

        DB::table('hostel_room_allocations')->updateOrInsert(
            ['student_id' => $student->id, 'status' => 'active'],
            [
                'room_id' => $room->id,
                'bed_id' => $bed->id,
                'allocation_date' => now()->toDateString(),
                'vacate_date' => null,
                'remark' => 'Demo allocation',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('hostel_fees')->updateOrInsert(
            ['student_id' => $student->id],
            [
                'monthly_fee' => 5000,
                'admission_fee' => 1000,
                'security_deposit' => 2000,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('hostel_notices')->updateOrInsert(
            ['title' => 'Hostel Timing Notice'],
            [
                'description' => 'Hostel gate closes at 10 PM.',
                'notice_date' => now()->toDateString(),
                'audience' => 'all',
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('hostel_events')->updateOrInsert(
            ['title' => 'Hostel Orientation'],
            [
                'event_date' => now()->addDays(7)->toDateString(),
                'event_time' => '17:00',
                'location' => 'Common Hall',
                'description' => 'Orientation for new students.',
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}
