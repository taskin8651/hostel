<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel\Bed;
use App\Models\Hostel\Fee;
use App\Models\Hostel\Room;
use App\Models\Hostel\RoomAllocation;
use App\Models\Hostel\Student;
use App\Models\Role;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class HostelModuleController extends Controller
{
    public function index(Request $request, string $module)
    {
        $config = $this->module($module);
        abort_if(Gate::denies($module . '_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = DB::table($config['table'])->whereNull('deleted_at')->latest('id');
        $this->applyFilters($query, $request, $config);

        $items = $query->get();
        $options = $this->optionsFor($config);

        return view($this->viewName($module, 'index'), compact('module', 'config', 'items', 'options'));
    }

    public function create(string $module)
    {
        $config = $this->module($module);
        abort_if(Gate::denies($module . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $options = $this->optionsFor($config);
        $item = null;

        return view($this->viewName($module, 'create', 'form'), compact('module', 'config', 'options', 'item'));
    }

    public function store(Request $request, string $module)
    {
        $config = $this->module($module);
        abort_if(Gate::denies($module . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $this->validatedData($request, $module, $config);
        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = DB::table($config['table'])->insertGetId($data);
        $this->afterSave($module, $id, $data, true);

        return redirect()->route('admin.hostel.modules.index', $module)->with('message', trans('global.create_success'));
    }

    public function show(string $module, int $id)
    {
        $config = $this->module($module);
        abort_if(Gate::denies($module . '_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $item = $this->findItem($config, $id);
        $options = $this->optionsFor($config);

        return view($this->viewName($module, 'show'), compact('module', 'config', 'item', 'options'));
    }

    public function edit(string $module, int $id)
    {
        $config = $this->module($module);
        abort_if(Gate::denies($module . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $item = $this->findItem($config, $id);
        $options = $this->optionsFor($config);

        return view($this->viewName($module, 'edit', 'form'), compact('module', 'config', 'options', 'item'));
    }

    public function update(Request $request, string $module, int $id)
    {
        $config = $this->module($module);
        abort_if(Gate::denies($module . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $item = $this->findItem($config, $id);
        $data = $this->validatedData($request, $module, $config, $id, $item);
        $data['updated_at'] = now();

        DB::table($config['table'])->where('id', $id)->update($data);
        $this->afterSave($module, $id, $data, false, $item);

        return redirect()->route('admin.hostel.modules.index', $module)->with('message', trans('global.update_success'));
    }

    public function destroy(string $module, int $id)
    {
        $config = $this->module($module);
        abort_if(Gate::denies($module . '_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $item = $this->findItem($config, $id);
        DB::table($config['table'])->where('id', $id)->update(['deleted_at' => now(), 'updated_at' => now()]);
        $this->afterDelete($module, $item);

        return back()->with('message', trans('global.delete_success'));
    }

    public function massDestroy(Request $request, string $module)
    {
        $config = $this->module($module);
        abort_if(Gate::denies($module . '_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate(['ids' => ['required', 'array']]);
        DB::table($config['table'])->whereIn('id', $request->input('ids', []))->update(['deleted_at' => now(), 'updated_at' => now()]);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function report(Request $request, string $report)
    {
        $reports = config('hostel.reports');
        abort_unless(isset($reports[$report]), 404);

        $module = $reports[$report]['module'];
        $config = $this->module($module);
        abort_if(Gate::denies($module . '_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = DB::table($config['table'])->whereNull('deleted_at')->latest('id');
        $this->applyFilters($query, $request, $config);
        $items = $query->get();
        $options = $this->optionsFor($config);
        $reportConfig = $reports[$report];

        return view('admin.hostel.report', compact('report', 'reportConfig', 'module', 'config', 'items', 'options'));
    }

    public function receipt(int $id)
    {
        abort_if(Gate::denies('fee-payments_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $payment = DB::table('hostel_fee_payments')->where('id', $id)->whereNull('deleted_at')->first();
        abort_unless($payment, 404);

        $student = $payment->student_id
            ? DB::table('hostel_students')->where('id', $payment->student_id)->first()
            : null;

        $allocation = $payment->student_id
            ? DB::table('hostel_room_allocations')->where('student_id', $payment->student_id)->where('status', 'active')->whereNull('deleted_at')->latest('id')->first()
            : null;

        $room = $allocation?->room_id
            ? DB::table('hostel_rooms')->where('id', $allocation->room_id)->first()
            : null;

        $bed = $allocation?->bed_id
            ? DB::table('hostel_beds')->where('id', $allocation->bed_id)->first()
            : null;

        return view('admin.hostel.receipt', compact('payment', 'student', 'allocation', 'room', 'bed'));
    }

    private function module(string $module): array
    {
        $modules = config('hostel.modules');
        abort_unless(isset($modules[$module]), 404);

        return $modules[$module];
    }

    private function viewName(string $module, string $view, ?string $fallback = null): string
    {
        $path = 'admin.' . str_replace('-', '_', $module) . '.' . $view;

        if (view()->exists($path)) {
            return $path;
        }

        return 'admin.hostel.' . ($fallback ?: $view);
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
            $rule = [];
            $rule[] = (($field['required'] ?? false) && ! (($field['type'] ?? null) === 'file' && $item)) ? 'required' : 'nullable';

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

        if (in_array($module, ['student-attendance'], true) && empty($data['attendance_datetime'])) {
            $data['attendance_datetime'] = now();
        }

        if ($module === 'student-attendance' && empty($data['attendance_date']) && ! empty($data['attendance_datetime'])) {
            $data['attendance_date'] = \Illuminate\Support\Carbon::parse($data['attendance_datetime'])->toDateString();
        }

        return $data;
    }

    private function validateBusinessRules(Request $request, string $module, ?int $id = null): void
    {
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

        if ($module === 'documents') {
            if ($request->input('person_type') === 'student' && ! $request->input('student_id')) {
                throw ValidationException::withMessages(['student_id' => 'Student is required for student document.']);
            }

            if ($request->input('person_type') === 'staff' && ! $request->input('staff_id')) {
                throw ValidationException::withMessages(['staff_id' => 'Staff is required for staff document.']);
            }
        }
    }

    private function optionsFor(array $config): array
    {
        $options = [];

        foreach ($config['fields'] as $name => $field) {
            if (($field['type'] ?? null) !== 'select') {
                continue;
            }

            if (isset($field['options'])) {
                $options[$name] = $field['options'];
                continue;
            }

            if (isset($field['source'])) {
                if ($field['source'] === 'users') {
                    $options[$name] = User::orderBy('name')
                        ->get()
                        ->mapWithKeys(fn ($user) => [$user->id => $user->name . ' - ' . $user->email])
                        ->toArray();
                    continue;
                }

                $source = config('hostel.modules.' . $field['source']);
                if ($source) {
                    $rows = DB::table($source['table'])
                        ->whereNull('deleted_at')
                        ->orderBy('id')
                        ->get();

                    $options[$name] = $rows
                        ->mapWithKeys(fn ($row) => [$row->id => $this->labelForRow($field['source'], $row)])
                        ->toArray();

                    $options[$name . '_meta'] = $rows
                        ->mapWithKeys(fn ($row) => [$row->id => [
                            'branch_id' => $row->branch_id ?? null,
                            'room_id' => $row->room_id ?? null,
                            'student_branch_id' => $row->branch_id ?? null,
                            'student_room_id' => $row->room_id ?? null,
                        ]])
                        ->toArray();
                }
            }
        }

        return $options;
    }

    private function labelForRow(string $module, object $row): string
    {
        return match ($module) {
            'students' => trim(($row->registration_no ?? '') . ' - ' . ($row->name ?? 'Student #' . $row->id), ' -'),
            'rooms' => 'Room ' . ($row->room_number ?? $row->id),
            'beds' => 'Bed ' . ($row->bed_number ?? $row->id),
            'staff' => $row->name ?? 'Staff #' . $row->id,
            'branches' => $row->name ?? 'Branch #' . $row->id,
            'staff-attendance' => trim(($row->staff_id ?? 'Staff') . ' - ' . ($row->attendance_date ?? ('#' . $row->id)), ' -'),
            default => $row->title ?? $row->name ?? ('#' . $row->id),
        };
    }

    private function applyFilters($query, Request $request, array $config): void
    {
        foreach ($config['fields'] as $name => $field) {
            $value = $request->query($name);
            if ($value === null || $value === '') {
                continue;
            }

            if (in_array($field['type'], ['select', 'date'], true)) {
                $query->where($name, $value);
            } else {
                $query->where($name, 'like', '%' . $value . '%');
            }
        }
    }

    public static function displayValue(array $field, mixed $value, array $options = []): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        if (($field['type'] ?? null) === 'select') {
            return $options[$value] ?? (string) $value;
        }

        if (($field['type'] ?? null) === 'file') {
            return Storage::disk('public')->url($value);
        }

        return (string) $value;
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
            $this->syncLoginUser($module, $id, $data);
            return;
        }

        if ($module === 'beds') {
            if (! empty($data['room_id'])) {
                $this->refreshRoomStatus((int) $data['room_id']);
            }
            return;
        }

        if ($module === 'room-allocations') {
            $this->syncAllocation($id, $data, $previous);
            return;
        }

        if ($module === 'fee-payments') {
            $this->syncFeePayment($id, $data);
            return;
        }

        if (in_array($module, ['staff'], true)) {
            $this->syncLoginUser($module, $id, $data);
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
            $allocation->update([
                'branch_id' => $branchId,
                'updated_at' => now(),
            ]);
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

        $this->syncAllocation($newAllocation->id, $newAllocation->toArray(), $allocation);
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
        if ($totalBeds < 1) {
            return;
        }

        $existing = Bed::where('room_id', $roomId)->pluck('bed_number')->map(fn ($number) => (string) $number)->all();

        for ($i = 1; $i <= $totalBeds; $i++) {
            if (! in_array((string) $i, $existing, true)) {
                Bed::create([
                    'room_id' => $roomId,
                    'bed_number' => (string) $i,
                    'status' => 'vacant',
                ]);
            }
        }
    }

    private function syncAllocation(int $allocationId, array $data, ?object $previous = null): void
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
                ->update([
                    'status' => 'changed',
                    'shift_date' => $allocation->shift_date ?: now()->toDateString(),
                    'vacate_date' => now()->toDateString(),
                    'updated_at' => now(),
                ]);

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
        $status = $occupied > 0 ? 'occupied' : 'available';

        $room->update(['status' => $status]);
    }

    private function syncFeePayment(int $paymentId, array $data): void
    {
        $updates = [];

        if (empty($data['receipt_number'])) {
            $updates['receipt_number'] = 'RCPT-' . now()->format('Ymd') . '-' . str_pad((string) $paymentId, 5, '0', STR_PAD_LEFT);
        }

        if (array_key_exists('student_id', $data) && ((float) ($data['due_amount'] ?? 0)) <= 0) {
            $fee = Fee::where('student_id', $data['student_id'])->latest('id')->first();
            if ($fee) {
                $expected = (float) $fee->monthly_fee;
                $paid = (float) ($data['paid_amount'] ?? 0);
                $updates['due_amount'] = max($expected - $paid, 0);
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

    private function syncLoginUser(string $module, int $recordId, array $data): void
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
