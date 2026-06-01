<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel\Bed;
use App\Models\Hostel\Fee;
use App\Models\Hostel\Room;
use App\Models\Hostel\RoomAllocation;
use App\Models\Hostel\Student;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class HostelModuleApiController extends Controller
{
    public function index(Request $request, string $module)
    {
        $config = $this->module($module);
        $query = DB::table($config['table'])->whereNull('deleted_at')->latest('id');
        $this->applyFilters($query, $request, $config);
        $items = $query->paginate($request->integer('per_page', 25));

        return $this->ok($items);
    }

    public function store(Request $request, string $module)
    {
        $config = $this->module($module);
        $data = $this->validatedData($request, $module, $config);
        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = DB::table($config['table'])->insertGetId($data);
        $this->afterSave($module, $id, $data, true);

        return $this->ok(DB::table($config['table'])->find($id), 'Created', 201);
    }

    public function show(string $module, int $id)
    {
        $config = $this->module($module);

        return $this->ok($this->findItem($config, $id));
    }

    public function update(Request $request, string $module, int $id)
    {
        $config = $this->module($module);
        $previous = $this->findItem($config, $id);
        $data = $this->validatedData($request, $module, $config, $id, $previous);
        $data['updated_at'] = now();

        DB::table($config['table'])->where('id', $id)->update($data);
        $this->afterSave($module, $id, $data, false, $previous);

        return $this->ok($this->findItem($config, $id), 'Updated');
    }

    public function destroy(string $module, int $id)
    {
        $config = $this->module($module);
        $item = $this->findItem($config, $id);
        DB::table($config['table'])->where('id', $id)->update(['deleted_at' => now(), 'updated_at' => now()]);
        $this->afterDelete($module, $item);

        return $this->ok(null, 'Deleted');
    }

    public function updateStatus(Request $request, string $module, int $id)
    {
        $config = $this->module($module);
        abort_unless(isset($config['fields']['status']), 422, 'This module has no status field.');

        $request->validate(['status' => ['required']]);
        $this->findItem($config, $id);

        DB::table($config['table'])->where('id', $id)->update(['status' => $request->input('status'), 'updated_at' => now()]);

        return $this->ok($this->findItem($config, $id), 'Status updated');
    }

    private function module(string $module): array
    {
        $modules = config('hostel.modules');
        abort_unless(isset($modules[$module]), 404);

        return $modules[$module];
    }

    private function findItem(array $config, int $id): object
    {
        $item = DB::table($config['table'])->where('id', $id)->whereNull('deleted_at')->first();
        abort_unless($item, 404);

        return $item;
    }

    private function validatedData(Request $request, string $module, array $config, ?int $id = null, ?object $item = null): array
    {
        $rules = [];

        foreach ($config['fields'] as $name => $field) {
            $rule = [($field['required'] ?? false) ? 'required' : 'nullable'];

            if ($field['type'] === 'email') {
                $rule[] = 'email';
            } elseif ($field['type'] === 'number') {
                $rule[] = 'numeric';
            } elseif ($field['type'] === 'date') {
                $rule[] = 'date';
            } elseif ($field['type'] === 'datetime') {
                $rule[] = 'date';
            } elseif ($field['type'] === 'file') {
                $rule[] = 'file';
                $rule[] = 'max:8192';
            } elseif ($field['type'] === 'select' && isset($field['options'])) {
                $rule[] = Rule::in(array_keys($field['options']));
            }

            if ($field['unique'] ?? false) {
                $rule[] = Rule::unique($config['table'], $name)->ignore($id);
            }

            $rules[$name] = $rule;
        }

        $validated = $request->validate($rules);
        $this->validateBusinessRules($request, $module, $id);
        $data = [];

        foreach ($config['fields'] as $name => $field) {
            if ($field['type'] === 'file') {
                if ($request->hasFile($name)) {
                    $data[$name] = $request->file($name)->store('hostel/' . $module, 'public');
                } elseif ($item) {
                    $data[$name] = $item->{$name};
                }
            } elseif ($field['type'] === 'password' && $item && empty($validated[$name])) {
                $data[$name] = $item->{$name};
            } else {
                $data[$name] = $validated[$name] ?? ($field['default'] ?? null);
            }
        }

        if ($module === 'room-allocations') {
            $this->fillAllocationBranch($data);
        }

        if ($module === 'student-attendance' && empty($data['attendance_datetime'])) {
            $data['attendance_datetime'] = now();
        }

        if ($module === 'food-menus' && empty($data['day_name']) && ! empty($data['day'])) {
            $data['day_name'] = $data['day'];
        }

        return $data;
    }

    private function validateBusinessRules(Request $request, string $module, ?int $id = null): void
    {
        if ($module === 'room-allocation') {
            $module = 'room-allocations';
        }

        if ($module === 'staff-work') {
            $module = 'staff-works';
        }

        if ($module === 'room-allocations') {
            $bedId = $request->input('bed_id');
            $roomId = $request->input('room_id');
            $studentId = $request->input('student_id');
            $branchId = $request->input('branch_id');
            $status = $request->input('status', 'active');

            $activeStudentAllocation = RoomAllocation::where('student_id', $studentId)
                ->where('status', 'active')
                ->whereNull('deleted_at')
                ->when($id, fn ($query) => $query->where('id', '!=', $id))
                ->exists();

            if ($status === 'active' && $activeStudentAllocation && ! $request->input('shift_date')) {
                throw ValidationException::withMessages(['student_id' => 'This student already has an active room allocation. Add a shift date to transfer.']);
            }

            if ($branchId && $roomId) {
                $room = Room::whereKey($roomId)->first();
                if ($room && $room->branch_id && (int) $room->branch_id !== (int) $branchId) {
                    throw ValidationException::withMessages(['room_id' => 'Selected room does not belong to the selected branch.']);
                }
            }

            if ($bedId) {
                $bed = Bed::whereKey($bedId)->first();

                if (! $bed) {
                    throw ValidationException::withMessages(['bed_id' => 'Selected bed was not found.']);
                }

                if ((int) $bed->room_id !== (int) $roomId) {
                    throw ValidationException::withMessages(['bed_id' => 'Selected bed does not belong to the selected room.']);
                }

                $activeAllocation = RoomAllocation::where('bed_id', $bedId)
                    ->where('status', 'active')
                    ->when($id, fn ($query) => $query->where('id', '!=', $id))
                    ->exists();

                if ($status === 'active' && $activeAllocation) {
                    throw ValidationException::withMessages(['bed_id' => 'This bed is already allocated to another active student.']);
                }
            }
        }

        if (in_array($module, ['students', 'rooms'], true)) {
            $branchId = $request->input('branch_id');
            $roomId = $request->input('room_id');

            if ($branchId && $roomId) {
                $room = Room::whereKey($roomId)->first();
                if ($room && $room->branch_id && (int) $room->branch_id !== (int) $branchId) {
                    throw ValidationException::withMessages(['room_id' => 'Selected room does not belong to the selected branch.']);
                }
            }
        }

        if ($module === 'food-menus') {
            $day = $request->input('day');
            $exists = DB::table('hostel_food_menus')
                ->where('day', $day)
                ->whereNull('deleted_at')
                ->when($id, fn ($query) => $query->where('id', '!=', $id))
                ->exists();

            if ($day && $exists) {
                throw ValidationException::withMessages(['day' => 'Menu for this day already exists.']);
            }
        }

        if ($module === 'staff-payments' && $request->input('staff_attendance_id')) {
            $attendance = DB::table('hostel_staff_attendance')->where('id', $request->input('staff_attendance_id'))->whereNull('deleted_at')->first();
            if (! $attendance) {
                throw ValidationException::withMessages(['staff_attendance_id' => 'Selected staff attendance was not found.']);
            }

            if ((int) $attendance->staff_id !== (int) $request->input('staff_id')) {
                throw ValidationException::withMessages(['staff_attendance_id' => 'Selected attendance does not belong to the selected staff member.']);
            }
        }

        if ($module === 'student-attendance') {
            $exists = DB::table('hostel_student_attendance')
                ->where('student_id', $request->input('student_id'))
                ->whereDate('attendance_date', $request->input('attendance_date'))
                ->whereNull('deleted_at')
                ->when($id, fn ($query) => $query->where('id', '!=', $id))
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages(['attendance_date' => 'Attendance for this student is already marked for the selected date.']);
            }
        }

        if ($module === 'staff-attendance') {
            $exists = DB::table('hostel_staff_attendance')
                ->where('staff_id', $request->input('staff_id'))
                ->whereDate('attendance_date', $request->input('attendance_date'))
                ->whereNull('deleted_at')
                ->when($id, fn ($query) => $query->where('id', '!=', $id))
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages(['attendance_date' => 'Attendance for this staff member is already marked for the selected date.']);
            }
        }

        if ($module === 'leaves') {
            if ($request->input('person_type') === 'student' && ! $request->input('student_id')) {
                throw ValidationException::withMessages(['student_id' => 'Student is required for student leave.']);
            }

            if ($request->input('person_type') === 'staff' && ! $request->input('staff_id')) {
                throw ValidationException::withMessages(['staff_id' => 'Staff is required for staff leave.']);
            }

            if ($request->input('from_date') && $request->input('to_date') && $request->input('to_date') < $request->input('from_date')) {
                throw ValidationException::withMessages(['to_date' => 'To date must be after or equal to from date.']);
            }
        }
    }

    private function ok(mixed $data = null, string $message = 'Success', int $status = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    private function applyFilters($query, Request $request, array $config): void
    {
        foreach ($config['fields'] as $name => $field) {
            $value = $request->query($name);
            if ($value === null || $value === '') {
                continue;
            }

            if (in_array($field['type'], ['select', 'date', 'datetime'], true)) {
                $query->where($name, $value);
            } else {
                $query->where($name, 'like', '%' . $value . '%');
            }
        }
    }

    private function afterSave(string $module, int $id, array $data, bool $created, ?object $previous = null): void
    {
        if ($module === 'rooms') {
            $this->syncRoomBeds($id, (int) ($data['total_beds'] ?? 0));
            $this->refreshRoomStatus($id);
            return;
        }

        if ($module === 'students') {
            $this->syncStudentRoomAllocation($id, $data);
            $this->syncLoginUser($module, $id);
            return;
        }

        if ($module === 'beds' && ! empty($data['room_id'])) {
            $this->refreshRoomStatus((int) $data['room_id']);
            return;
        }

        if ($module === 'room-allocations') {
            $this->syncAllocation($id, $previous);
            return;
        }

        if ($module === 'fee-payments') {
            $this->syncFeePayment($id, $data);
            return;
        }

        if (in_array($module, ['staff'], true)) {
            $this->syncLoginUser($module, $id);
        }
    }

    private function fillAllocationBranch(array &$data): void
    {
        if (! empty($data['branch_id'])) {
            return;
        }

        if (! empty($data['student_id'])) {
            $student = Student::find($data['student_id']);
            if ($student?->branch_id) {
                $data['branch_id'] = $student->branch_id;
                return;
            }
        }

        if (! empty($data['room_id'])) {
            $room = Room::find($data['room_id']);
            if ($room?->branch_id) {
                $data['branch_id'] = $room->branch_id;
            }
        }
    }

    private function syncStudentRoomAllocation(int $studentId, array $data): void
    {
        if (empty($data['room_id'])) {
            return;
        }

        $student = Student::find($studentId);
        $room = Room::find($data['room_id']);
        if (! $student || ! $room) {
            return;
        }

        $branchId = $data['branch_id'] ?: $room->branch_id;
        $allocation = RoomAllocation::where('student_id', $studentId)
            ->where('status', 'active')
            ->latest('id')
            ->first();

        if ($allocation && (int) $allocation->room_id === (int) $room->id) {
            $allocation->update(['branch_id' => $branchId, 'updated_at' => now()]);
            return;
        }

        if ($allocation) {
            $allocation->update([
                'status' => 'changed',
                'shift_date' => now()->toDateString(),
                'vacate_date' => now()->toDateString(),
                'updated_at' => now(),
            ]);
        }

        $newAllocation = RoomAllocation::create([
            'student_id' => $studentId,
            'branch_id' => $branchId,
            'room_id' => $room->id,
            'allocation_date' => $data['joining_date'] ?? now()->toDateString(),
            'status' => 'active',
        ]);

        $this->syncAllocation($newAllocation->id, $allocation);
    }

    private function afterDelete(string $module, object $item): void
    {
        if ($module === 'room-allocations') {
            if ($item->bed_id) {
                Bed::whereKey($item->bed_id)->update(['status' => 'vacant']);
            }
            if ($item->room_id) {
                $this->refreshRoomStatus((int) $item->room_id);
            }
        }

        if ($module === 'beds' && $item->room_id) {
            $this->refreshRoomStatus((int) $item->room_id);
        }

        if ($module === 'fee-payments') {
            DB::table('hostel_incomes')
                ->where('source', 'Student Fee')
                ->where('remark', 'like', '%fee_payment_id:' . $item->id . '%')
                ->update(['deleted_at' => now(), 'updated_at' => now()]);
        }
    }

    private function syncRoomBeds(int $roomId, int $totalBeds): void
    {
        for ($i = 1; $i <= $totalBeds; $i++) {
            Bed::firstOrCreate(
                ['room_id' => $roomId, 'bed_number' => (string) $i],
                ['status' => 'vacant']
            );
        }
    }

    private function syncAllocation(int $allocationId, ?object $previous = null): void
    {
        $allocation = RoomAllocation::find($allocationId);
        if (! $allocation) {
            return;
        }

        if ($previous && $previous->bed_id && (int) $previous->bed_id !== (int) $allocation->bed_id) {
            Bed::whereKey($previous->bed_id)->update(['status' => 'vacant']);
        }

        if ($allocation->status === 'active') {
            RoomAllocation::where('student_id', $allocation->student_id)
                ->where('id', '!=', $allocation->id)
                ->where('status', 'active')
                ->update(['status' => 'changed', 'shift_date' => $allocation->shift_date ?: now()->toDateString(), 'vacate_date' => now()->toDateString(), 'updated_at' => now()]);

            if ($allocation->bed_id) {
                Bed::whereKey($allocation->bed_id)->update(['status' => 'occupied']);
            }
        } elseif ($allocation->bed_id) {
            Bed::whereKey($allocation->bed_id)->update(['status' => 'vacant']);
        }

        if ($allocation->room_id) {
            $this->refreshRoomStatus((int) $allocation->room_id);
        }

        if ($previous && $previous->room_id && (int) $previous->room_id !== (int) $allocation->room_id) {
            $this->refreshRoomStatus((int) $previous->room_id);
        }
    }

    private function refreshRoomStatus(int $roomId): void
    {
        $room = Room::find($roomId);
        if (! $room) {
            return;
        }

        $occupied = Bed::where('room_id', $roomId)->where('status', 'occupied')->count();
        $room->update(['status' => $occupied > 0 ? 'occupied' : 'available']);
    }

    private function syncFeePayment(int $paymentId, array $data): void
    {
        $updates = [];

        if (empty($data['receipt_number'])) {
            $updates['receipt_number'] = 'RCPT-' . now()->format('Ymd') . '-' . str_pad((string) $paymentId, 5, '0', STR_PAD_LEFT);
        }

        if (isset($data['student_id']) && ((float) ($data['due_amount'] ?? 0)) <= 0) {
            $fee = Fee::where('student_id', $data['student_id'])->latest('id')->first();
            if ($fee) {
                $updates['due_amount'] = max((float) $fee->monthly_fee - (float) ($data['paid_amount'] ?? 0), 0);
            }
        }

        if ($updates) {
            $updates['updated_at'] = now();
            DB::table('hostel_fee_payments')->where('id', $paymentId)->update($updates);
        }

        $payment = DB::table('hostel_fee_payments')->where('id', $paymentId)->first();
        if ($payment && (float) $payment->paid_amount > 0) {
            $receipt = $payment->receipt_number ?: ($updates['receipt_number'] ?? ('#' . $paymentId));
            $remark = 'Auto income from fee receipt ' . $receipt . ' [fee_payment_id:' . $paymentId . ']';
            $existing = DB::table('hostel_incomes')
                ->where('source', 'Student Fee')
                ->where('remark', 'like', '%fee_payment_id:' . $paymentId . '%')
                ->whereNull('deleted_at')
                ->first();

            $income = [
                'source' => 'Student Fee',
                'amount' => $payment->paid_amount,
                'income_date' => $payment->payment_date ?: now()->toDateString(),
                'payment_mode' => $payment->payment_mode,
                'remark' => $remark,
                'updated_at' => now(),
            ];

            if ($existing) {
                DB::table('hostel_incomes')->where('id', $existing->id)->update($income);
            } else {
                $income['created_at'] = now();
                DB::table('hostel_incomes')->insert($income);
            }
        }
    }

    private function syncLoginUser(string $module, int $recordId): void
    {
        $table = $module === 'students' ? 'hostel_students' : 'hostel_staff';
        $roleTitle = $module === 'students' ? 'Student' : 'Staff';
        $record = DB::table($table)->where('id', $recordId)->first();

        if (! $record || empty($record->email)) {
            return;
        }

        $role = Role::firstOrCreate(['title' => $roleTitle]);
        $user = $record->user_id ? User::find($record->user_id) : null;

        if (! $user) {
            $user = User::where('email', $record->email)->first();
        }

        $defaultPassword = property_exists($record, 'default_password') ? $record->default_password : null;
        $password = $defaultPassword ?: ($record->mobile ?: 'password');

        if (! $user) {
            $user = User::create([
                'name' => $record->name,
                'email' => $record->email,
                'password' => $password,
            ]);
        } else {
            $updates = [
                'name' => $record->name,
                'email' => $record->email,
            ];

            if ($defaultPassword) {
                $updates['password'] = $defaultPassword;
            }

            $user->update($updates);
        }

        $user->roles()->sync([$role->id]);

        if ((int) $record->user_id !== (int) $user->id) {
            DB::table($table)->where('id', $recordId)->update([
                'user_id' => $user->id,
                'updated_at' => now(),
            ]);
        }
    }
}
