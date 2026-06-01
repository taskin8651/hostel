<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('hostel_branches')) {
            Schema::create('hostel_branches', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->nullable()->unique();
                $table->text('address')->nullable();
                $table->string('status')->nullable()->default('active');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        $this->table('hostel_students', function (Blueprint $table) {
            $this->string($table, 'default_password');
            $this->unsignedBigInteger($table, 'branch_id');
            $this->unsignedBigInteger($table, 'room_id');
            $this->string($table, 'alternate_mobile');
            $this->date($table, 'dob');
            $this->string($table, 'blood_group');
            $this->string($table, 'aadhaar_number');
            $this->string($table, 'institute_name');
            $this->string($table, 'batch');
            $this->string($table, 'aadhaar_front');
            $this->string($table, 'aadhaar_back');
            $this->string($table, 'id_card_front');
            $this->string($table, 'id_card_back');
        });

        $this->table('hostel_rooms', function (Blueprint $table) {
            $this->unsignedBigInteger($table, 'branch_id');
        });

        $this->table('hostel_room_allocations', function (Blueprint $table) {
            $this->unsignedBigInteger($table, 'branch_id');
            $this->date($table, 'shift_date');
        });

        $this->table('hostel_accessories', function (Blueprint $table) {
            $this->unsignedBigInteger($table, 'branch_id');
        });

        $this->table('hostel_food_menus', function (Blueprint $table) {
            $this->string($table, 'day');
            $this->text($table, 'morning_snacks');
        });

        $this->table('hostel_student_attendance', function (Blueprint $table) {
            $this->time($table, 'punch_in_time');
            $this->time($table, 'punch_out_time');
            $this->dateTime($table, 'attendance_datetime');
            $this->string($table, 'image');
        });

        $this->table('hostel_staff_payments', function (Blueprint $table) {
            $this->unsignedBigInteger($table, 'staff_attendance_id');
            $this->decimal($table, 'attendance_days');
            $this->decimal($table, 'present_days');
        });
    }

    public function down(): void
    {
        $drops = [
            'hostel_staff_payments' => ['staff_attendance_id', 'attendance_days', 'present_days'],
            'hostel_student_attendance' => ['punch_in_time', 'punch_out_time', 'attendance_datetime', 'image'],
            'hostel_food_menus' => ['day', 'morning_snacks'],
            'hostel_accessories' => ['branch_id'],
            'hostel_room_allocations' => ['branch_id', 'shift_date'],
            'hostel_rooms' => ['branch_id'],
            'hostel_students' => ['default_password', 'branch_id', 'room_id', 'alternate_mobile', 'dob', 'blood_group', 'aadhaar_number', 'institute_name', 'batch', 'aadhaar_front', 'aadhaar_back', 'id_card_front', 'id_card_back'],
        ];

        foreach ($drops as $tableName => $columns) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName, $columns) {
                foreach ($columns as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        Schema::dropIfExists('hostel_branches');
    }

    private function table(string $tableName, callable $callback): void
    {
        if (! Schema::hasTable($tableName)) {
            return;
        }

        Schema::table($tableName, $callback);
    }

    private function string(Blueprint $table, string $column): void
    {
        if (! Schema::hasColumn($table->getTable(), $column)) {
            $table->string($column)->nullable();
        }
    }

    private function unsignedBigInteger(Blueprint $table, string $column): void
    {
        if (! Schema::hasColumn($table->getTable(), $column)) {
            $table->unsignedBigInteger($column)->nullable()->index();
        }
    }

    private function date(Blueprint $table, string $column): void
    {
        if (! Schema::hasColumn($table->getTable(), $column)) {
            $table->date($column)->nullable();
        }
    }

    private function time(Blueprint $table, string $column): void
    {
        if (! Schema::hasColumn($table->getTable(), $column)) {
            $table->time($column)->nullable();
        }
    }

    private function text(Blueprint $table, string $column): void
    {
        if (! Schema::hasColumn($table->getTable(), $column)) {
            $table->text($column)->nullable();
        }
    }

    private function dateTime(Blueprint $table, string $column): void
    {
        if (! Schema::hasColumn($table->getTable(), $column)) {
            $table->dateTime($column)->nullable();
        }
    }

    private function decimal(Blueprint $table, string $column): void
    {
        if (! Schema::hasColumn($table->getTable(), $column)) {
            $table->decimal($column, 12, 2)->default(0);
        }
    }
};
