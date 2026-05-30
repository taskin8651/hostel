@extends('layouts.admin')
@section('page-title', trans('cruds.auditLog.title'))

@section('styles')
<style>
.page-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #E2E8F0;
    overflow: hidden;
}
.btn-primary {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 18px; border-radius: 10px;
    background: var(--accent); color: #fff;
    font-size: 13px; font-weight: 600; text-decoration: none;
    border: none; cursor: pointer; transition: opacity .2s;
}
.btn-primary:hover { opacity: .88; color: #fff; }
.btn-outline {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 12px; border-radius: 8px;
    font-size: 12px; font-weight: 600; text-decoration: none;
    border: 1.5px solid; cursor: pointer; transition: background .15s;
}
.avatar-circle {
    width: 36px; height: 36px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; color: #fff;
    background: var(--accent); flex-shrink: 0;
}
.role-tag {
    display: inline-flex; align-items: center;
    padding: 3px 9px; border-radius: 6px;
    font-size: 11px; font-weight: 600;
    background: var(--accent-light); color: var(--accent);
    border: 1px solid;
    border-color: color-mix(in srgb, var(--accent) 20%, transparent);
}
.status-dot {
    width: 7px; height: 7px; border-radius: 50%; display: inline-block;
}
/* DataTable overrides */
table.dataTable thead th {
    background: #F8FAFC !important; color: #64748B !important;
    font-size: 11px !important; font-weight: 700 !important;
    text-transform: uppercase; letter-spacing: .06em;
    padding: 12px 16px !important; border-bottom: 1px solid #E2E8F0 !important;
    white-space: nowrap;
}
table.dataTable tbody td { padding: 13px 16px !important; border-bottom: 1px solid #F1F5F9 !important; font-size: 13.5px; }
table.dataTable tbody tr:hover td { background: #F8FAFC !important; }
table.dataTable tbody tr:last-child td { border-bottom: none !important; }
.dataTables_wrapper .dataTables_filter input {
    padding: 7px 12px 7px 34px !important; border-radius: 9px !important;
    border: 1px solid #E2E8F0 !important; font-size: 13px !important;
    background: #F8FAFC url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%239CA3AF' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E") no-repeat 10px center !important;
    outline: none !important;
}
.dataTables_wrapper .dataTables_filter input:focus { border-color: var(--accent) !important; background-color: #fff !important; }
.dataTables_wrapper .dataTables_filter label { font-size: 13px; color: #64748B; }
.dataTables_wrapper .dataTables_length select {
    padding: 6px 10px; border-radius: 8px; border: 1px solid #E2E8F0;
    font-size: 13px; background: #F8FAFC; color: #374151; outline: none;
}
.dataTables_wrapper .dataTables_info { font-size: 12px; color: #94A3B8; padding-top: 10px; }
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 7px !important; font-size: 12px !important; padding: 5px 10px !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: var(--accent) !important; color: #fff !important; border-color: var(--accent) !important;
}
.dt-buttons .btn { border-radius: 8px !important; font-size: 12px !important; padding: 6px 12px !important; }
.btn-danger { background: #FFF1F2 !important; color: #BE123C !important; border: 1.5px solid #FECDD3 !important; }
</style>
@endsection

@section('content')

{{-- ── PAGE HEADER ── --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 style="font-size:22px; font-weight:700; color:#0F172A; margin:0;">
            {{ trans('cruds.auditLog.title') }}
        </h2>
        <p style="font-size:13px; color:#64748B; margin:4px 0 0;">
            Track and monitor system activities and changes
        </p>
    </div>
</div>

{{-- ── STATS ROW ── --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px,1fr)); gap:14px; margin-bottom:24px;">
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:12px; padding:16px 18px;">
        <p style="font-size:11px; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:.05em; margin:0 0 6px;">Total Logs</p>
        <p style="font-size:24px; font-weight:700; color:#0F172A; margin:0;">{{ $auditLogs->total() }}</p>
    </div>
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:12px; padding:16px 18px;">
        <p style="font-size:11px; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:.05em; margin:0 0 6px;">Today</p>
        <p style="font-size:24px; font-weight:700; color:#0F172A; margin:0;">{{ $auditLogs->where('created_at', '>=', today())->count() }}</p>
    </div>
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:12px; padding:16px 18px;">
        <p style="font-size:11px; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:.05em; margin:0 0 6px;">Users</p>
        <p style="font-size:24px; font-weight:700; color:#0F172A; margin:0;">{{ $auditLogs->pluck('user_id')->unique()->filter()->count() }}</p>
    </div>
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:12px; padding:16px 18px;">
        <p style="font-size:11px; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:.05em; margin:0 0 6px;">Models</p>
        <p style="font-size:24px; font-weight:700; color:#0F172A; margin:0;">{{ $auditLogs->pluck('subject_type')->unique()->count() }}</p>
    </div>
</div>

{{-- ── TABLE CARD ── --}}
<div class="page-card">
    <div style="padding:16px 20px; border-bottom:1px solid #F1F5F9; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <p style="font-size:14px; font-weight:700; color:#0F172A; margin:0;">Audit Logs</p>
        <span style="font-size:12px; color:#94A3B8;">
            <i class="fas fa-info-circle" style="margin-right:4px;"></i>
            System activity history
        </span>
    </div>

    <div style="overflow-x:auto; padding:4px 10px;">
        <table class="min-w-full datatable datatable-AuditLog" style="width:100%;">
            <thead>
                <tr>
                    <th style="width:40px;"></th>
                    <th>{{ trans('cruds.auditLog.fields.id') }}</th>
                    <th>Action</th>
                    <th>Subject</th>
                    <th>Type</th>
                    <th>User</th>
                    <th>IP Address</th>
                    <th>Time</th>
                    <th style="text-align:right;">{{ trans('global.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($auditLogs as $auditLog)
                <tr data-entry-id="{{ $auditLog->id }}">
                    <td></td>

                    <td>
                        <span style="font-size:12px; font-weight:700; color:#94A3B8;">#{{ $auditLog->id }}</span>
                    </td>

                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            @php
                                $actionColors = [
                                    'created' => '#10B981',
                                    'updated' => '#3B82F6',
                                    'deleted' => '#EF4444',
                                    'viewed' => '#8B5CF6',
                                    'logged_in' => '#059669',
                                    'logged_out' => '#DC2626'
                                ];
                                $action = strtolower($auditLog->description);
                                $color = $actionColors[$action] ?? '#6B7280';
                            @endphp
                            <div style="width:8px; height:8px; border-radius:50%; background:{{ $color }};"></div>
                            <span style="font-size:13.5px; font-weight:600; color:#0F172A;">{{ ucfirst($auditLog->description) }}</span>
                        </div>
                    </td>

                    <td style="color:#475569;">{{ $auditLog->subject_id }}</td>

                    <td>
                        <span style="font-size:12px; font-weight:600; color:#64748B; background:#F1F5F9; padding:3px 8px; border-radius:6px;">
                            {{ class_basename($auditLog->subject_type) }}
                        </span>
                    </td>

                    <td>
                        @if($auditLog->user)
                            <div style="display:flex; align-items:center; gap:8px;">
                                @php
                                    $colors = ['#4F46E5','#0EA5E9','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899','#14B8A6'];
                                    $color  = $colors[$auditLog->user->id % count($colors)];
                                @endphp
                                <div class="avatar-circle" style="background:{{ $color }}; width:28px; height:28px; font-size:11px;">
                                    {{ strtoupper(substr($auditLog->user->name, 0, 1)) }}
                                </div>
                                <span style="font-size:13px; color:#374151;">{{ $auditLog->user->name }}</span>
                            </div>
                        @else
                            <span style="font-size:13px; color:#94A3B8;">System</span>
                        @endif
                    </td>

                    <td>
                        <span style="font-family:monospace; font-size:12px; color:#64748B; background:#F8FAFC; padding:3px 6px; border-radius:4px;">
                            {{ $auditLog->host }}
                        </span>
                    </td>

                    <td style="color:#64748B; font-size:12px;">
                        {{ $auditLog->created_at->format('d M Y, H:i') }}
                    </td>

                    <td>
                        <div style="display:flex; align-items:center; justify-content:flex-end; gap:6px;">
                            @can('audit_log_show')
                            <a href="{{ route('admin.audit-logs.show', $auditLog->id) }}"
                               class="btn-outline"
                               style="border-color:#E2E8F0; color:#475569;"
                               onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                                <i class="fas fa-eye" style="font-size:11px;"></i> View
                            </a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
@parent
<script>
$(function () {
    $('.datatable-AuditLog:not(.ajaxTable)').DataTable({
        order: [[1, 'desc']],
        pageLength: 25,
        scrollX: true,
        language: {
            search: '',
            searchPlaceholder: 'Search audit logs...',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_–_END_ of _TOTAL_ logs',
        }
    });
});
</script>
@endsection
