@extends('layouts.admin')

@section('page-title', 'Fee Receipt')

@section('content')

<div class="admin-page-head no-print">
    <div>
        <a href="{{ route('admin.fee-payments.index') }}" class="admin-back-link">
            &larr; {{ trans('global.back_to_list') }}
        </a>
        <h2 class="admin-page-title">Fee Receipt</h2>
        <p class="admin-page-subtitle">Printable payment receipt for student fee collection.</p>
    </div>
    <button type="button" class="btn-primary" onclick="window.print()">
        <i class="fas fa-print"></i>
        Print Receipt
    </button>
</div>

<div class="page-card" style="max-width:820px; margin:0 auto;">
    <div style="padding:28px;">
        <div style="display:flex; justify-content:space-between; gap:20px; border-bottom:1px solid #E5E7EB; padding-bottom:18px;">
            <div>
                <h1 style="font-size:24px; font-weight:800; margin:0; color:#111827;">{{ trans('panel.site_title') }}</h1>
                <p style="font-size:13px; color:#64748B; margin:4px 0 0;">Hostel Fee Receipt</p>
            </div>
            <div style="text-align:right;">
                <p style="font-size:12px; color:#64748B; margin:0;">Receipt No</p>
                <p style="font-size:18px; font-weight:800; color:#111827; margin:2px 0 0;">{{ $payment->receipt_number ?? ('#'.$payment->id) }}</p>
                <p style="font-size:12px; color:#64748B; margin:6px 0 0;">{{ $payment->payment_date ?? now()->toDateString() }}</p>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-top:22px;">
            <div>
                <p class="detail-label">Student</p>
                <p class="detail-value" style="font-size:16px; font-weight:700;">{{ $student->name ?? '-' }}</p>
                <p style="font-size:13px; color:#64748B; margin:4px 0;">Reg No: {{ $student->registration_no ?? '-' }}</p>
                <p style="font-size:13px; color:#64748B; margin:0;">Mobile: {{ $student->mobile ?? '-' }}</p>
            </div>
            <div>
                <p class="detail-label">Room Details</p>
                <p class="detail-value" style="font-size:16px; font-weight:700;">{{ $room ? 'Room '.$room->room_number : '-' }}</p>
                <p style="font-size:13px; color:#64748B; margin:4px 0;">Bed: {{ $bed->bed_number ?? '-' }}</p>
                <p style="font-size:13px; color:#64748B; margin:0;">Month: {{ $payment->month ?? '-' }}</p>
            </div>
        </div>

        <table style="width:100%; border-collapse:collapse; margin-top:26px;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:12px; background:#F8FAFC; border:1px solid #E5E7EB;">Description</th>
                    <th style="text-align:right; padding:12px; background:#F8FAFC; border:1px solid #E5E7EB;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:12px; border:1px solid #E5E7EB;">Paid Amount</td>
                    <td style="padding:12px; border:1px solid #E5E7EB; text-align:right;">{{ number_format((float) $payment->paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding:12px; border:1px solid #E5E7EB;">Due Amount</td>
                    <td style="padding:12px; border:1px solid #E5E7EB; text-align:right;">{{ number_format((float) $payment->due_amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding:12px; border:1px solid #E5E7EB;">Payment Mode</td>
                    <td style="padding:12px; border:1px solid #E5E7EB; text-align:right;">{{ ucfirst($payment->payment_mode ?? '-') }}</td>
                </tr>
            </tbody>
        </table>

        <div style="display:flex; justify-content:space-between; gap:24px; margin-top:42px;">
            <div>
                <p style="font-size:12px; color:#64748B;">Remark</p>
                <p style="font-size:13px; color:#111827;">{{ $payment->remark ?? '-' }}</p>
            </div>
            <div style="text-align:center; min-width:180px;">
                <div style="border-top:1px solid #111827; padding-top:8px; font-size:13px; font-weight:700;">Authorized Signature</div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, #sidebar, #main-header { display:none !important; }
    .admin-main, .admin-content { margin:0 !important; padding:0 !important; }
    body { background:#fff !important; }
    .page-card { border:none !important; box-shadow:none !important; }
}
</style>

@endsection
