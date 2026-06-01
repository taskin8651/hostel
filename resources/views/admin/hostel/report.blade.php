@extends('layouts.admin')

@section('page-title', $reportConfig['title'])

@section('content')

<div class="admin-page-head">
    <div>
        <h2 class="admin-page-title">{{ $reportConfig['title'] }}</h2>
        <p class="admin-page-subtitle">Filter, review and export report-ready records.</p>
    </div>
    <a href="{{ route('admin.hostel.modules.index', $module) }}" class="btn-outline">
        <i class="fas fa-table"></i>
        Open {{ $config['title'] }}
    </a>
</div>

<form method="GET" class="page-card mb-3">
    <div class="page-card-header">
        <p class="page-card-title">Report Filters</p>
        <button type="submit" class="btn-outline"><i class="fas fa-filter"></i> Apply</button>
    </div>
    <div class="form-card-body" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:12px;">
        @foreach($config['fields'] as $name => $field)
            @if(in_array($field['type'], ['text', 'email', 'select', 'date'], true))
                <div class="field-group" style="margin:0;">
                    <label class="field-label" for="filter_{{ $name }}">{{ $field['label'] }}</label>
                    @if($field['type'] === 'select')
                        <select name="{{ $name }}" id="filter_{{ $name }}" class="field-input">
                            <option value="">{{ trans('global.all') }}</option>
                            @foreach($options[$name] ?? [] as $value => $label)
                                <option value="{{ $value }}" {{ (string) request($name) === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="{{ $field['type'] === 'date' ? 'date' : 'text' }}" name="{{ $name }}" id="filter_{{ $name }}" value="{{ request($name) }}" class="field-input">
                    @endif
                </div>
            @endif
        @endforeach
    </div>
</form>

@php
    $amountTotal = collect(['amount', 'paid_amount', 'due_amount', 'monthly_fee', 'salary'])->filter(fn ($field) => isset($config['fields'][$field]));
    $statusField = collect(['status', 'payment_status'])->first(fn ($field) => isset($config['fields'][$field]));
@endphp

<div class="stats-grid">
    <div class="stat-card">
        <p class="stat-label">Total Records</p>
        <p class="stat-value">{{ $items->count() }}</p>
    </div>
    @foreach($amountTotal as $field)
        <div class="stat-card">
            <p class="stat-label">{{ $config['fields'][$field]['label'] }}</p>
            <p class="stat-value">{{ number_format((float) $items->sum($field), 2) }}</p>
        </div>
    @endforeach
    @if($statusField)
        <div class="stat-card">
            <p class="stat-label">Status Types</p>
            <p class="stat-value">{{ $items->pluck($statusField)->filter()->unique()->count() }}</p>
        </div>
    @endif
    @if($module === 'rooms')
        <div class="stat-card">
            <p class="stat-label">Occupied Rooms</p>
            <p class="stat-value">{{ $items->where('status', 'occupied')->count() }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Available Rooms</p>
            <p class="stat-value">{{ $items->where('status', 'available')->count() }}</p>
        </div>
    @endif
</div>

@if($statusField)
    <div class="page-card mb-3">
        <div class="page-card-header">
            <p class="page-card-title">Status Breakdown</p>
            <span class="page-card-note"><i class="fas fa-chart-pie"></i> Current filtered records</span>
        </div>
        <div class="form-card-body" style="display:flex; flex-wrap:wrap; gap:10px;">
            @foreach($items->groupBy($statusField) as $status => $rows)
                <span class="status-pill {{ in_array($status, ['active','available','vacant','present','paid','published','approved','resolved','completed'], true) ? 'success' : 'warning' }}">
                    {{ $options[$statusField][$status] ?? ucfirst(str_replace('_', ' ', $status ?: 'blank')) }}: {{ $rows->count() }}
                </span>
            @endforeach
        </div>
    </div>
@endif

<div class="page-card">
    <div class="page-card-header">
        <p class="page-card-title">{{ $items->count() }} Records</p>
        <span class="page-card-note"><i class="fas fa-download"></i> Use table buttons for CSV, Excel or print.</span>
    </div>
    <div class="page-card-table">
        <table class="min-w-full datatable datatable-report">
            <thead>
                <tr>
                    <th>ID</th>
                    @foreach($config['quick'] as $fieldName)
                        <th>{{ $config['fields'][$fieldName]['label'] ?? ucfirst(str_replace('_', ' ', $fieldName)) }}</th>
                    @endforeach
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>#{{ $item->id }}</td>
                        @foreach($config['quick'] as $fieldName)
                            @php
                                $field = $config['fields'][$fieldName] ?? ['type' => 'text'];
                                $value = $item->{$fieldName} ?? null;
                            @endphp
                            <td>{{ \App\Http\Controllers\Admin\HostelModuleController::displayValue($field, $value, $options[$fieldName] ?? []) }}</td>
                        @endforeach
                        <td>{{ $item->created_at ?? '-' }}</td>
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
    $('.datatable-report').DataTable({
        dom: 'lBfrtip',
        buttons: ['copy', 'csv', 'excel', 'print'],
        pageLength: 25
    });
});
</script>
@endsection
