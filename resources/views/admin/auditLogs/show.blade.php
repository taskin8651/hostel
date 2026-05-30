@extends('layouts.admin')
@section('page-title', trans('global.show') . ' ' . trans('cruds.auditLog.title_singular'))

@section('styles')
<style>
.detail-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #E2E8F0;
    overflow: hidden;
}
.detail-row {
    display: flex; gap: 12px; padding: 14px 0;
    border-bottom: 1px solid #F1F5F9;
    align-items: flex-start;
}
.detail-row:last-child { border-bottom: none; }
.detail-label {
    width: 160px; flex-shrink: 0;
    font-size: 12px; font-weight: 700; color: #94A3B8;
    text-transform: uppercase; letter-spacing: .05em;
    padding-top: 2px;
}
.detail-value { font-size: 14px; color: #1E293B; font-weight: 500; }
.btn-primary {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 9px 18px; border-radius: 10px;
    background: var(--accent); color: #fff;
    font-size: 13px; font-weight: 600; text-decoration: none; border: none;
    cursor: pointer; transition: opacity .2s;
}
.btn-primary:hover { opacity:.88; color:#fff; }
.btn-outline {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 18px; border-radius: 10px;
    background: #fff; color: #475569;
    font-size: 13px; font-weight: 600; text-decoration: none;
    border: 1.5px solid #E2E8F0; transition: background .15s;
}
.btn-outline:hover { background: #F8FAFC; color: #374151; }
.stat-mini {
    background: #F8FAFC; border-radius: 10px; border: 1px solid #F1F5F9;
    padding: 12px 16px; text-align: center;
}
</style>
@endsection

@section('content')

{{-- ── HEADER ── --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
    <div>
        <a href="{{ route('admin.audit-logs.index') }}" style="font-size:13px; color:var(--accent); text-decoration:none; font-weight:600; display:inline-flex; align-items:center; gap:5px; margin-bottom:6px;">
            ← {{ trans('global.back_to_list') }}
        </a>
        <h2 style="font-size:22px; font-weight:700; color:#0F172A; margin:0;">Audit Log Details</h2>
        <p style="font-size:13px; color:#64748B; margin:4px 0 0;">Full details for this system activity</p>
    </div>

    <div style="display:flex; gap:8px;">
        <a href="{{ route('admin.audit-logs.index') }}" class="btn-outline">{{ trans('global.back_to_list') }}</a>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 2fr; gap:20px; align-items:start;">

    {{-- ── LEFT: Log Card ── --}}
    <div>
        <div class="detail-card" style="margin-bottom:16px;">
            <div style="padding:28px 24px; text-align:center; background:linear-gradient(135deg, var(--accent-light) 0%, #fff 60%); border-bottom:1px solid #F1F5F9;">
                @php $colors = ['#4F46E5','#0EA5E9','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899','#14B8A6']; @endphp
                <div style="width:72px; height:72px; border-radius:18px; background:{{ $colors[$auditLog->id % count($colors)] }}; color:#fff; display:flex; align-items:center; justify-content:center; font-size:26px; font-weight:700; margin:0 auto 14px;">
                    <i class="fas fa-history"></i>
                </div>
                <p style="font-size:17px; font-weight:700; color:#0F172A; margin:0 0 4px;">{{ ucfirst($auditLog->description) }}</p>
                <p style="font-size:13px; color:#64748B; margin:0 0 12px;">Audit Log</p>
                <span style="display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:20px; background:#DCFCE7; color:#15803D; font-size:12px; font-weight:600;">
                    <i class="fas fa-check-circle" style="font-size:11px;"></i> Recorded
                </span>
            </div>

            <div style="padding:16px 20px;">
                <div style="display:grid; grid-template-columns:1fr; gap:10px;">
                    <div class="stat-mini">
                        <p style="font-size:11px; color:#94A3B8; margin:0 0 4px;">Log ID</p>
                        <p style="font-size:16px; font-weight:700; color:#0F172A; margin:0;">#{{ $auditLog->id }}</p>
                    </div>
                    <div class="stat-mini">
                        <p style="font-size:11px; color:#94A3B8; margin:0 0 4px;">Recorded</p>
                        <p style="font-size:14px; font-weight:700; color:#0F172A; margin:0;">{{ optional($auditLog->created_at)->format('d M Y') ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="detail-card" style="padding:16px;">
            <p style="font-size:12px; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:.05em; margin:0 0 12px;">Quick Actions</p>
            <div style="display:flex; flex-direction:column; gap:8px;">
                <a href="{{ route('admin.audit-logs.index') }}"
                   style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:9px; background:#F8FAFC; color:#475569; text-decoration:none; font-size:13px; font-weight:600; border:1px solid #F1F5F9; transition:background .15s;"
                   onmouseover="this.style.background='#F1F5F9'" onmouseout="this.style.background='#F8FAFC'">
                    <i class="fas fa-list" style="width:16px; text-align:center;"></i> All Audit Logs
                </a>
            </div>
        </div>
    </div>

    {{-- ── RIGHT: Details ── --}}
    <div>
        {{-- Log Details --}}
        <div class="detail-card" style="margin-bottom:16px;">
            <div style="padding:16px 22px; border-bottom:1px solid #F1F5F9; display:flex; align-items:center; gap:10px;">
                <div style="width:32px; height:32px; border-radius:8px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center; font-size:13px;">
                    <i class="fas fa-info-circle"></i>
                </div>
                <p style="font-size:14px; font-weight:700; color:#0F172A; margin:0;">Log Details</p>
            </div>
            <div style="padding:0 22px;">

                <div class="detail-row">
                    <span class="detail-label">{{ trans('cruds.auditLog.fields.id') }}</span>
                    <span class="detail-value" style="font-family:monospace; background:#F8FAFC; padding:3px 8px; border-radius:6px; font-size:13px;">#{{ $auditLog->id }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">{{ trans('cruds.auditLog.fields.description') }}</span>
                    <span class="detail-value">{{ ucfirst($auditLog->description) }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">{{ trans('cruds.auditLog.fields.subject_id') }}</span>
                    <span class="detail-value">{{ $auditLog->subject_id }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">{{ trans('cruds.auditLog.fields.subject_type') }}</span>
                    <span class="detail-value">{{ $auditLog->subject_type }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">{{ trans('cruds.auditLog.fields.user_id') }}</span>
                    <div>
                        <span class="detail-value">{{ $auditLog->user?->name ?? 'System' }}</span>
                        @if($auditLog->user)
                            <span style="font-size:11px; color:#94A3B8; margin-left:8px;">(ID: {{ $auditLog->user_id }})</span>
                        @endif
                    </div>
                </div>

                <div class="detail-row">
                    <span class="detail-label">{{ trans('cruds.auditLog.fields.host') }}</span>
                    <span class="detail-value" style="font-family:monospace; background:#F8FAFC; padding:3px 8px; border-radius:6px; font-size:13px;">{{ $auditLog->host }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">{{ trans('cruds.auditLog.fields.created_at') }}</span>
                    <span class="detail-value">{{ $auditLog->created_at->format('d M Y, H:i:s') }}</span>
                </div>

            </div>
        </div>

        {{-- Properties --}}
        <div class="detail-card">
            <div style="padding:16px 22px; border-bottom:1px solid #F1F5F9; display:flex; align-items:center; gap:10px;">
                <div style="width:32px; height:32px; border-radius:8px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center; font-size:13px;">
                    <i class="fas fa-code"></i>
                </div>
                <p style="font-size:14px; font-weight:700; color:#0F172A; margin:0;">{{ trans('cruds.auditLog.fields.properties') }}</p>
            </div>
            <div style="padding:18px 22px;">
                <pre style="background:#F8FAFC; border:1px solid #F1F5F9; border-radius:10px; padding:16px; font-size:12px; color:#374151; overflow-x:auto; margin:0;">{{ json_encode($auditLog->properties, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    </div>

</div>

@endsection
