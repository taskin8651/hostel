@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')

<div class="admin-page-head">
    <div>
        <h2 class="admin-page-title">Hostel Dashboard</h2>
        <p class="admin-page-subtitle">Welcome back, {{ auth()->user()->name }}. Here is the current hostel snapshot.</p>
    </div>
    <span class="page-card-note">
        <i class="fas fa-clock"></i>
        {{ now()->format('D, d M Y') }}
    </span>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <p class="stat-label">Total Students</p>
        <p class="stat-value">{{ $stats['students'] ?? 0 }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Total Rooms</p>
        <p class="stat-value">{{ $stats['rooms'] ?? 0 }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Available Beds</p>
        <p class="stat-value">{{ $availableBeds }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Active Complaints</p>
        <p class="stat-value">{{ $activeComplaints }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Total Staff</p>
        <p class="stat-value">{{ $stats['staff'] ?? 0 }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Today Attendance</p>
        <p class="stat-value">{{ $todayAttendance }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Pending Fees</p>
        <p class="stat-value">{{ number_format($pendingFees, 2) }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Monthly Expense</p>
        <p class="stat-value">{{ number_format($monthlyExpense, 2) }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Monthly Income</p>
        <p class="stat-value">{{ number_format($monthlyIncome, 2) }}</p>
    </div>
</div>

<div class="page-card mt-3">
    <div class="page-card-header">
        <p class="page-card-title">Upcoming Events & Notices</p>
        <span class="page-card-note"><i class="fas fa-bullhorn"></i> Published updates</span>
    </div>
    <div class="form-card-body" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div>
            <p class="meta-label">Upcoming Events</p>
            @forelse($upcomingEvents as $event)
                <div class="detail-row">
                    <span class="detail-label">{{ $event->event_date ?? '-' }}</span>
                    <span class="detail-value">{{ $event->title }}</span>
                </div>
            @empty
                <p class="field-hint">No upcoming events.</p>
            @endforelse
        </div>
        <div>
            <p class="meta-label">Recent Notices</p>
            @forelse($recentNotices as $notice)
                <div class="detail-row">
                    <span class="detail-label">{{ $notice->notice_date ?? '-' }}</span>
                    <span class="detail-value">{{ $notice->title }}</span>
                </div>
            @empty
                <p class="field-hint">No published notices.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header">
        <p class="page-card-title">Quick Links</p>
        <span class="page-card-note"><i class="fas fa-bolt"></i> Daily hostel operations</span>
    </div>
    <div class="form-card-body" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:12px;">
        @foreach($modules as $key => $module)
            @can($key . '_access')
                <a href="{{ route('admin.' . $key . '.index') }}" class="quick-link">
                    <i class="{{ $module['icon'] }}"></i>
                    {{ $module['title'] }}
                </a>
            @endcan
        @endforeach
    </div>
</div>

<div class="page-card mt-3">
    <div class="page-card-header">
        <p class="page-card-title">Reports</p>
        <span class="page-card-note"><i class="fas fa-file-export"></i> Export-ready report pages</span>
    </div>
    <div class="form-card-body" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:12px;">
        @foreach(config('hostel.reports') as $key => $report)
            <a href="{{ route('admin.hostel.reports.show', $key) }}" class="quick-link">
                <i class="fas fa-chart-line"></i>
                {{ $report['title'] }}
            </a>
        @endforeach
    </div>
</div>

@endsection
