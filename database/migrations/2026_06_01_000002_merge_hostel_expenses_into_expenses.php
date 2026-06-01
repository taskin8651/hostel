<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('hostel_expenses')) {
            return;
        }

        Schema::table('hostel_expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('hostel_expenses', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->index();
            }

            if (! Schema::hasColumn('hostel_expenses', 'expense_type')) {
                $table->string('expense_type')->nullable()->default('general');
            }

            if (! Schema::hasColumn('hostel_expenses', 'status')) {
                $table->string('status')->nullable()->default('paid');
            }
        });

        if (! Schema::hasTable('hostel_hostel_expenses')) {
            return;
        }

        $existingMigrated = DB::table('hostel_expenses')
            ->where('remark', 'like', '%[old_hostel_expense_id:%')
            ->exists();

        if ($existingMigrated) {
            return;
        }

        DB::table('hostel_hostel_expenses')
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->chunkById(100, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('hostel_expenses')->insert([
                        'branch_id' => $row->branch_id ?? null,
                        'expense_type' => $row->expense_type ?: 'general',
                        'title' => ucfirst((string) ($row->expense_type ?: 'hostel')) . ' Expense',
                        'category' => 'Hostel Expense',
                        'amount' => $row->amount ?? 0,
                        'expense_date' => $row->expense_date ?? null,
                        'payment_mode' => $row->payment_mode ?? null,
                        'bill_upload' => $row->bill_upload ?? null,
                        'remark' => trim((string) ($row->remark ?? '') . ' [old_hostel_expense_id:' . $row->id . ']'),
                        'status' => 'paid',
                        'created_at' => $row->created_at ?? now(),
                        'updated_at' => now(),
                        'deleted_at' => null,
                    ]);
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasTable('hostel_expenses')) {
            DB::table('hostel_expenses')
                ->where('remark', 'like', '%[old_hostel_expense_id:%')
                ->delete();
        }
    }
};
