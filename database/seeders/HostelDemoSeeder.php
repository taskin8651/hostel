<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HostelDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $branchA = $this->branch('Main Campus', 'MAIN', 'Near City Center');
        $branchB = $this->branch('North Wing', 'NORTH', 'North Block Road');

        $room101 = $this->room($branchA, '101', '1', 'double', 2, 'occupied');
        $room102 = $this->room($branchA, '102', '1', 'triple', 3, 'available');
        $room201 = $this->room($branchB, '201', '2', 'single', 1, 'occupied');

        $bed101A = $this->bed($room101, '1', 'occupied');
        $this->bed($room101, '2', 'vacant');
        $this->bed($room102, '1', 'vacant');
        $this->bed($room102, '2', 'vacant');
        $this->bed($room102, '3', 'vacant');
        $bed201A = $this->bed($room201, '1', 'occupied');

        $chair = $this->accessory($branchA, $room101, 'Study Chair', 2, 'good');
        $table = $this->accessory($branchA, $room101, 'Study Table', 2, 'good');
        $bucket = $this->accessory($branchB, $room201, 'Bucket', 1, 'good');

        $rahul = $this->student([
            'registration_no' => 'HMS-001',
            'name' => 'Rahul Sharma',
            'mobile' => '9000000001',
            'alternate_mobile' => '9000000091',
            'email' => 'rahul@example.com',
            'branch_id' => $branchA,
            'room_id' => $room101,
            'course' => 'BCA',
            'batch' => '2026',
            'institute_name' => 'City College',
        ]);

        $priya = $this->student([
            'registration_no' => 'HMS-002',
            'name' => 'Priya Verma',
            'mobile' => '9000000003',
            'alternate_mobile' => '9000000093',
            'email' => 'priya@example.com',
            'branch_id' => $branchB,
            'room_id' => $room201,
            'course' => 'MBA',
            'batch' => '2025',
            'institute_name' => 'Management Institute',
        ]);

        $this->studentAccessories($rahul, [$chair, $table]);
        $this->studentAccessories($priya, [$bucket]);

        $this->allocation($rahul, $branchA, $room101, $bed101A, 'active');
        $this->allocation($priya, $branchB, $room201, $bed201A, 'active');

        $warden = $this->staff('Anil Kumar', '9000000011', 'anil.staff@example.com', 'Warden', 25000);
        $cook = $this->staff('Sunita Devi', '9000000012', 'sunita.staff@example.com', 'Cook', 18000);

        $this->staffAttendance($warden, now()->toDateString(), 'present');
        $this->staffAttendance($cook, now()->toDateString(), 'present');

        $this->studentAttendance($rahul, 'out', now()->subHours(3));
        $this->studentAttendance($rahul, 'in', now()->subHours(1));
        $this->studentAttendance($priya, 'out', now()->subMinutes(45));

        $this->document('student', $rahul, null, 'aadhaar_front', 'Rahul Aadhaar Front');
        $this->document('student', $rahul, null, 'id_card_front', 'Rahul ID Card');
        $this->document('staff', null, $warden, 'id_card_front', 'Anil ID Card');

        $this->fee($rahul, 5000, 1000, 2000);
        $this->fee($priya, 6500, 1500, 2500);
        $this->feePayment($rahul, 'June 2026', 5000, 0, 'cash');
        $this->feePayment($priya, 'June 2026', 4000, 2500, 'upi');

        $this->complaint($rahul, $room101, 'Fan not working', 'maintenance', 'pending');
        $this->leave('student', $rahul, null, now()->addDays(2)->toDateString(), now()->addDays(3)->toDateString(), 'pending');
        $this->visitor('Mahesh Sharma', '9000000002', $rahul, $room101);

        $this->foodMenus();

        $this->staffPayment($warden, 'June 2026', 30, 28, 23000, 2000);
        $this->staffPayment($cook, 'June 2026', 30, 30, 18000, 0);
        $this->staffWork($warden, 'Night round supervision', 'working');
        $this->staffWork($cook, 'Prepare weekly menu', 'completed');

        $this->expense($branchA, 'electricity', 'Electricity Bill', 8500, 'bank');
        $this->expense($branchB, 'food', 'Vegetable Purchase', 3200, 'cash');
        $this->income('Mess Guest Charge', 1500, 'cash');
        $this->bill('Electricity', 'EB-2026-06', 8500, 'unpaid');

        $this->notice('Hostel Timing Notice', 'Hostel gate closes at 10 PM.', 'all');
        $this->event('Hostel Orientation', 'Common Hall', now()->addDays(7)->toDateString());
    }

    private function branch(string $name, string $code, string $address): int
    {
        DB::table('hostel_branches')->updateOrInsert(
            ['code' => $code],
            ['name' => $name, 'address' => $address, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]
        );

        return (int) DB::table('hostel_branches')->where('code', $code)->value('id');
    }

    private function room(int $branchId, string $number, string $floor, string $type, int $beds, string $status): int
    {
        DB::table('hostel_rooms')->updateOrInsert(
            ['room_number' => $number],
            ['branch_id' => $branchId, 'floor_number' => $floor, 'room_type' => $type, 'total_beds' => $beds, 'status' => $status, 'created_at' => now(), 'updated_at' => now()]
        );

        return (int) DB::table('hostel_rooms')->where('room_number', $number)->value('id');
    }

    private function bed(int $roomId, string $number, string $status): int
    {
        DB::table('hostel_beds')->updateOrInsert(
            ['room_id' => $roomId, 'bed_number' => $number],
            ['status' => $status, 'created_at' => now(), 'updated_at' => now()]
        );

        return (int) DB::table('hostel_beds')->where('room_id', $roomId)->where('bed_number', $number)->value('id');
    }

    private function accessory(int $branchId, int $roomId, string $name, int $quantity, string $status): int
    {
        DB::table('hostel_accessories')->updateOrInsert(
            ['room_id' => $roomId, 'name' => $name],
            ['branch_id' => $branchId, 'quantity' => $quantity, 'status' => $status, 'remark' => 'Demo accessory', 'created_at' => now(), 'updated_at' => now()]
        );

        return (int) DB::table('hostel_accessories')->where('room_id', $roomId)->where('name', $name)->value('id');
    }

    private function student(array $data): int
    {
        $userId = $this->user($data['name'], $data['email'], 'Student');

        DB::table('hostel_students')->updateOrInsert(
            ['registration_no' => $data['registration_no']],
            [
                'user_id' => $userId,
                'default_password' => 'password',
                'branch_id' => $data['branch_id'],
                'room_id' => $data['room_id'],
                'name' => $data['name'],
                'mobile' => $data['mobile'],
                'alternate_mobile' => $data['alternate_mobile'],
                'email' => $data['email'],
                'dob' => '2002-05-15',
                'blood_group' => 'o_positive',
                'aadhaar_number' => '123456789012',
                'institute_name' => $data['institute_name'],
                'batch' => $data['batch'],
                'address' => 'Demo address',
                'guardian_name' => 'Demo Guardian',
                'guardian_mobile' => '9000000999',
                'course' => $data['course'],
                'joining_date' => now()->subMonth()->toDateString(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return (int) DB::table('hostel_students')->where('registration_no', $data['registration_no'])->value('id');
    }

    private function user(string $name, string $email, string $roleTitle): int
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => 'password']
        );

        $role = Role::firstOrCreate(['title' => $roleTitle]);
        $user->roles()->syncWithoutDetaching([$role->id]);

        return (int) $user->id;
    }

    private function studentAccessories(int $studentId, array $accessoryIds): void
    {
        DB::table('hostel_student_accessory')->where('student_id', $studentId)->delete();

        foreach ($accessoryIds as $accessoryId) {
            DB::table('hostel_student_accessory')->insert([
                'student_id' => $studentId,
                'accessory_id' => $accessoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function allocation(int $studentId, int $branchId, int $roomId, int $bedId, string $status): void
    {
        DB::table('hostel_room_allocations')->updateOrInsert(
            ['student_id' => $studentId, 'status' => $status],
            ['branch_id' => $branchId, 'room_id' => $roomId, 'bed_id' => $bedId, 'allocation_date' => now()->subMonth()->toDateString(), 'shift_date' => null, 'vacate_date' => null, 'remark' => 'Demo allocation', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    private function staff(string $name, string $mobile, string $email, string $designation, int $salary): int
    {
        $userId = $this->user($name, $email, 'Staff');

        DB::table('hostel_staff')->updateOrInsert(
            ['email' => $email],
            ['user_id' => $userId, 'name' => $name, 'mobile' => $mobile, 'designation' => $designation, 'address' => 'Staff demo address', 'joining_date' => now()->subMonths(6)->toDateString(), 'salary' => $salary, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]
        );

        return (int) DB::table('hostel_staff')->where('email', $email)->value('id');
    }

    private function staffAttendance(int $staffId, string $date, string $status): int
    {
        DB::table('hostel_staff_attendance')->updateOrInsert(
            ['staff_id' => $staffId, 'attendance_date' => $date],
            ['status' => $status, 'remark' => 'Demo attendance', 'created_at' => now(), 'updated_at' => now()]
        );

        return (int) DB::table('hostel_staff_attendance')->where('staff_id', $staffId)->where('attendance_date', $date)->value('id');
    }

    private function studentAttendance(int $studentId, string $movement, $dateTime): void
    {
        DB::table('hostel_student_attendance')->updateOrInsert(
            ['student_id' => $studentId, 'attendance_datetime' => $dateTime->format('Y-m-d H:i:s'), 'movement_type' => $movement],
            ['attendance_date' => $dateTime->toDateString(), 'image' => null, 'remark' => 'Demo gate log', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    private function document(string $type, ?int $studentId, ?int $staffId, string $documentType, string $name): void
    {
        DB::table('hostel_documents')->updateOrInsert(
            ['person_type' => $type, 'student_id' => $studentId, 'staff_id' => $staffId, 'document_type' => $documentType],
            ['document_name' => $name, 'document_file' => null, 'remark' => 'Demo document placeholder', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    private function fee(int $studentId, int $monthly, int $admission, int $security): void
    {
        DB::table('hostel_fees')->updateOrInsert(
            ['student_id' => $studentId],
            ['monthly_fee' => $monthly, 'admission_fee' => $admission, 'security_deposit' => $security, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    private function feePayment(int $studentId, string $month, int $paid, int $due, string $mode): void
    {
        DB::table('hostel_fee_payments')->updateOrInsert(
            ['student_id' => $studentId, 'month' => $month],
            ['receipt_number' => 'RCPT-DEMO-' . $studentId, 'paid_amount' => $paid, 'due_amount' => $due, 'payment_date' => now()->toDateString(), 'payment_mode' => $mode, 'attachment' => null, 'remark' => 'Demo fee payment', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    private function complaint(int $studentId, int $roomId, string $title, string $category, string $status): void
    {
        DB::table('hostel_complaints')->updateOrInsert(
            ['title' => $title, 'student_id' => $studentId],
            ['category' => $category, 'room_id' => $roomId, 'complaint_date' => now()->toDateString(), 'status' => $status, 'resolution_remark' => null, 'created_at' => now(), 'updated_at' => now()]
        );
    }

    private function leave(string $personType, ?int $studentId, ?int $staffId, string $from, string $to, string $status): void
    {
        DB::table('hostel_leaves')->updateOrInsert(
            ['person_type' => $personType, 'student_id' => $studentId, 'staff_id' => $staffId, 'from_date' => $from],
            ['leave_type' => 'Personal', 'to_date' => $to, 'reason' => 'Demo leave', 'status' => $status, 'created_at' => now(), 'updated_at' => now()]
        );
    }

    private function visitor(string $name, string $mobile, int $studentId, int $roomId): void
    {
        DB::table('hostel_visitors')->updateOrInsert(
            ['mobile' => $mobile, 'visit_date' => now()->toDateString()],
            ['name' => $name, 'relation' => 'Guardian', 'purpose' => 'Meeting', 'student_id' => $studentId, 'room_id' => $roomId, 'in_time' => '11:00', 'out_time' => '12:00', 'id_proof' => 'Aadhaar ending 9012', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    private function foodMenus(): void
    {
        $menus = [
            'monday' => ['Poha', 'Tea and Toast', 'Dal Rice', 'Samosa', 'Roti Sabzi'],
            'tuesday' => ['Upma', 'Paratha', 'Rajma Rice', 'Biscuits', 'Paneer Roti'],
            'wednesday' => ['Sprouts', 'Idli', 'Kadhi Rice', 'Fruit', 'Dal Roti'],
            'thursday' => ['Corn', 'Aloo Paratha', 'Chole Rice', 'Tea', 'Veg Pulao'],
            'friday' => ['Sandwich', 'Dosa', 'Dal Khichdi', 'Pakora', 'Roti Curry'],
            'saturday' => ['Fruit Bowl', 'Poori Sabzi', 'Veg Biryani', 'Tea', 'Dal Fry'],
            'sunday' => ['Juice', 'Bread Omelette', 'Special Thali', 'Cake', 'Light Dinner'],
        ];

        foreach ($menus as $day => $items) {
            DB::table('hostel_food_menus')->updateOrInsert(
                ['day' => $day],
                ['morning_snacks' => $items[0], 'breakfast' => $items[1], 'lunch' => $items[2], 'evening_snacks' => $items[3], 'dinner' => $items[4], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    private function staffPayment(int $staffId, string $month, int $days, int $present, int $paid, int $due): void
    {
        $attendanceId = DB::table('hostel_staff_attendance')->where('staff_id', $staffId)->latest('id')->value('id');

        DB::table('hostel_staff_payments')->updateOrInsert(
            ['staff_id' => $staffId, 'salary_month' => $month],
            ['staff_attendance_id' => $attendanceId, 'attendance_days' => $days, 'present_days' => $present, 'paid_amount' => $paid, 'due_amount' => $due, 'payment_mode' => 'bank', 'payment_date' => now()->toDateString(), 'remark' => 'Demo staff payment', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    private function staffWork(int $staffId, string $title, string $status): void
    {
        DB::table('hostel_staff_works')->updateOrInsert(
            ['staff_id' => $staffId, 'title' => $title],
            ['description' => 'Demo staff work', 'assigned_date' => now()->toDateString(), 'completion_date' => $status === 'completed' ? now()->toDateString() : null, 'status' => $status, 'created_at' => now(), 'updated_at' => now()]
        );
    }

    private function expense(int $branchId, string $type, string $title, int $amount, string $mode): void
    {
        DB::table('hostel_expenses')->updateOrInsert(
            ['title' => $title, 'expense_date' => now()->toDateString()],
            ['branch_id' => $branchId, 'expense_type' => $type, 'category' => ucfirst($type), 'amount' => $amount, 'payment_mode' => $mode, 'bill_upload' => null, 'remark' => 'Demo expense', 'status' => 'paid', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    private function income(string $source, int $amount, string $mode): void
    {
        DB::table('hostel_incomes')->updateOrInsert(
            ['source' => $source, 'income_date' => now()->toDateString()],
            ['amount' => $amount, 'payment_mode' => $mode, 'remark' => 'Demo income', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    private function bill(string $type, string $number, int $amount, string $status): void
    {
        DB::table('hostel_bills')->updateOrInsert(
            ['bill_number' => $number],
            ['bill_type' => $type, 'bill_date' => now()->toDateString(), 'amount' => $amount, 'bill_file' => null, 'payment_status' => $status, 'created_at' => now(), 'updated_at' => now()]
        );
    }

    private function notice(string $title, string $description, string $audience): void
    {
        DB::table('hostel_notices')->updateOrInsert(
            ['title' => $title],
            ['description' => $description, 'notice_date' => now()->toDateString(), 'audience' => $audience, 'status' => 'published', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    private function event(string $title, string $location, string $date): void
    {
        DB::table('hostel_events')->updateOrInsert(
            ['title' => $title],
            ['event_date' => $date, 'event_time' => '17:00', 'location' => $location, 'description' => 'Demo event', 'event_image' => null, 'status' => 'published', 'created_at' => now(), 'updated_at' => now()]
        );
    }
}
