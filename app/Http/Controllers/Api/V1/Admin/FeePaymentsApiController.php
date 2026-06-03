<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel\FeePayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FeePaymentsApiController extends Controller
{
    /**
     * ALL PAYMENTS
     */
    public function index()
    {
        $payments = FeePayment::with(['student'])
            ->latest()
            ->get()
            ->map(function ($payment) {

                return $this->formatPayment($payment);
            });

        return response()->json([

            'status' => true,

            'message' => 'Fee payments fetched successfully',

            'data' => $payments
        ]);
    }

    /**
     * CREATE PAYMENT
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'user_id' => 'required|exists:users,id',

            'month' => 'required|string|max:100',

            'paid_amount' => 'required|numeric',

            'due_amount' => 'nullable|numeric',

            'payment_mode' => 'required|string|max:50',

            'remark' => 'nullable|string|max:1000',

            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

        ], [

            'user_id.required' => 'User required',

            'user_id.exists' => 'User not found',

            'month.required' => 'Month required',

            'paid_amount.required' => 'Paid amount required',

            'payment_mode.required' => 'Payment mode required',
        ]);

        // VALIDATION ERROR
        if ($validator->fails()) {

            return response()->json([

                'status' => false,

                'message' => $validator->errors()->first()

            ], 422);
        }

        // USER
        $user = User::with(['studentProfile'])->find($request->user_id);

        if (!$user) {

            return response()->json([

                'status' => false,

                'message' => 'User not found'

            ], 404);
        }

        // ONLY STUDENT
        if (!$user->studentProfile) {

            return response()->json([

                'status' => false,

                'message' => 'Student profile not found'

            ], 404);
        }

        $student = $user->studentProfile;

        // FILE UPLOAD
        $attachmentPath = null;

        if ($request->hasFile('attachment')) {

            $attachmentPath = $request->file('attachment')
                ->store('hostel/fee-payments', 'public');
        }

        // RECEIPT NUMBER
        $receiptNumber = 'RCPT-' . strtoupper(Str::random(8));

        // CREATE PAYMENT
        FeePayment::create([

            'student_id' => $student->id,

            'receipt_number' => $receiptNumber,

            'month' => $request->month,

            'paid_amount' => $request->paid_amount,

            'due_amount' => $request->due_amount ?? 0,

            'payment_date' => now(),

            'payment_mode' => strtolower($request->payment_mode),

            'attachment' => $attachmentPath,

            'remark' => $request->remark,
        ]);

        return response()->json([

            'status' => true,

            'message' => 'Fee payment submitted successfully'
        ]);
    }

    /**
     * SINGLE PAYMENT
     */
    public function show($id)
    {
        $payment = FeePayment::with(['student'])->find($id);

        if (!$payment) {

            return response()->json([

                'status' => false,

                'message' => 'Fee payment not found'

            ], 404);
        }

        return response()->json([

            'status' => true,

            'message' => 'Fee payment fetched successfully',

            'data' => $this->formatPayment($payment)
        ]);
    }

    /**
     * FORMAT PAYMENT
     */
    private function formatPayment($payment)
    {
        return [

            'id' => $payment->id,

            'receipt_number' => $payment->receipt_number,

            'month' => $payment->month,

            'paid_amount' => (float) $payment->paid_amount,

            'due_amount' => (float) $payment->due_amount,

            'payment_date' => $payment->payment_date
                ? Carbon::parse($payment->payment_date)->format('d M Y')
                : null,

            'payment_mode' => ucfirst($payment->payment_mode),

            'remark' => $payment->remark,

            'attachment' => $payment->attachment
                ? asset('storage/' . $payment->attachment)
                : null,

            // STUDENT
            'student' => [

                'id' => optional($payment->student)->id,

                'name' => optional($payment->student)->name,

                'mobile' => optional($payment->student)->mobile,
            ],
        ];
    }
}