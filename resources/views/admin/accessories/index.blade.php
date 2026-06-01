@extends('layouts.admin')

@section('page-title', $config['title'])

@section('content')

@php
    $tableClass = 'datatable-' . str_replace('-', '_', $module);
    $routeBase = 'admin.' . $module . '.';
    $hasDedicatedRoutes = \Illuminate\Support\Facades\Route::has($routeBase . 'index');
    $indexUrl = $hasDedicatedRoutes ? route($routeBase . 'index') : route('admin.hostel.modules.index', $module);
    $createUrl = $hasDedicatedRoutes ? route($routeBase . 'create') : route('admin.hostel.modules.create', $module);
    $massDestroyUrl = $hasDedicatedRoutes ? route($routeBase . 'massDestroy') : route('admin.hostel.modules.massDestroy', $module);
@endphp

<div class="admin-page-head">
    <div>
        <h2 class="admin-page-title">{{ $config['title'] }}</h2>
        <p class="admin-page-subtitle">Manage {{ strtolower($config['title']) }} records for hostel operations.</p>
    </div>

    @can($module . '_create')
        <a href="{{ $createUrl }}" class="btn-primary">
            <i class="fas fa-plus"></i>
            {{ trans('global.add') }} {{ $config['singular'] }}
        </a>
    @endcan
</div>

<form method="GET" class="page-card mb-3">
    <div class="page-card-header">
        <p class="page-card-title">Filters</p>
        <button type="submit" class="btn-outline">
            <i class="fas fa-filter"></i>
            Apply
        </button>
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

<div class="stats-grid">
    <div class="stat-card">
        <p class="stat-label">Total Records</p>
        <p class="stat-value">{{ $items->count() }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Added Today</p>
        <p class="stat-value">{{ $items->where('created_at', '>=', now()->startOfDay())->count() }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Updated Today</p>
        <p class="stat-value">{{ $items->where('updated_at', '>=', now()->startOfDay())->count() }}</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Module</p>
        <p class="stat-value" style="font-size:18px;">{{ $config['singular'] }}</p>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header">
        <p class="page-card-title">All {{ $config['title'] }}</p>
        <span class="page-card-note"><i class="fas fa-info-circle"></i> Export buttons are available in table tools</span>
    </div>

    <div class="page-card-table">
        <table class="min-w-full datatable {{ $tableClass }}">
            <thead>
                <tr>
                    <th style="width:40px;"></th>
                    <th>ID</th>
                    @foreach($config['quick'] as $fieldName)
                        <th>{{ $config['fields'][$fieldName]['label'] ?? ucfirst(str_replace('_', ' ', $fieldName)) }}</th>
                    @endforeach
                    <th style="text-align:right;">{{ trans('global.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr data-entry-id="{{ $item->id }}">
                        <td></td>
                        <td><span class="id-text">#{{ $item->id }}</span></td>
                        @foreach($config['quick'] as $fieldName)
                            @php
                                $field = $config['fields'][$fieldName] ?? ['type' => 'text'];
                                $value = $item->{$fieldName} ?? null;
                                $display = \App\Http\Controllers\Admin\HostelModuleController::displayValue($field, $value, $options[$fieldName] ?? []);
                            @endphp
                            <td>
                                @if(($field['type'] ?? null) === 'file' && $value)
                                    <a href="{{ $display }}" target="_blank" class="btn-outline">View file</a>
                                @elseif(($field['label'] ?? '') === 'Status' || str_contains($fieldName, 'status'))
                                    <span class="status-pill {{ in_array($value, ['active','available','vacant','present','paid','published','approved','resolved','completed'], true) ? 'success' : 'warning' }}">
                                        {{ $display }}
                                    </span>
                                @else
                                    <span class="table-main-text">{{ $display }}</span>
                                @endif
                            </td>
                        @endforeach
                        <td>
                            <div class="action-row">
                                @can($module . '_show')
                                    <a href="{{ $hasDedicatedRoutes ? route($routeBase . 'show', $item->id) : route('admin.hostel.modules.show', [$module, $item->id]) }}" class="btn-outline">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>
                                @endcan
                                @can($module . '_edit')
                                    <a href="{{ $hasDedicatedRoutes ? route($routeBase . 'edit', $item->id) : route('admin.hostel.modules.edit', [$module, $item->id]) }}" class="btn-outline btn-outline-edit">
                                        <i class="fas fa-pencil-alt"></i>
                                        Edit
                                    </a>
                                @endcan
                                @if($module === 'fee-payments')
                                    @can('fee-payments_show')
                                        <a href="{{ route('admin.hostel.fee-payments.receipt', $item->id) }}" class="btn-outline">
                                            <i class="fas fa-receipt"></i>
                                            Receipt
                                        </a>
                                    @endcan
                                @endif
                                @can($module . '_delete')
                                    <form action="{{ $hasDedicatedRoutes ? route($routeBase . 'destroy', $item->id) : route('admin.hostel.modules.destroy', [$module, $item->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('{{ trans('global.areYouSure') }}')">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit" class="btn-outline btn-outline-danger">
                                            <i class="fas fa-trash-alt"></i>
                                            Delete
                                        </button>
                                    </form>
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
    initAdminDataTable('.{{ $tableClass }}', {
        canDelete: @can($module . '_delete') true @else false @endcan,
        massDeleteUrl: "{{ $massDestroyUrl }}",
        deleteText: "{{ trans('global.datatables.delete') }}",
        zeroSelectedText: "{{ trans('global.datatables.zero_selected') }}",
        confirmText: "{{ trans('global.areYouSure') }}",
        searchPlaceholder: 'Search {{ strtolower($config['title']) }}...',
        infoText: 'Showing _START_ to _END_ of _TOTAL_ records'
    });
});
</script>
@endsection
