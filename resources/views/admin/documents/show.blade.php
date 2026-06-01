@extends('layouts.admin')

@section('page-title', trans('global.show') . ' ' . $config['singular'])

@section('content')

@php
    $routeBase = 'admin.' . $module . '.';
    $hasDedicatedRoutes = \Illuminate\Support\Facades\Route::has($routeBase . 'index');
    $indexUrl = $hasDedicatedRoutes ? route($routeBase . 'index') : route('admin.hostel.modules.index', $module);
    $editUrl = $hasDedicatedRoutes ? route($routeBase . 'edit', $item->id) : route('admin.hostel.modules.edit', [$module, $item->id]);
@endphp

<div class="admin-page-head">
    <div>
        <a href="{{ $indexUrl }}" class="admin-back-link">
            &larr; {{ trans('global.back_to_list') }}
        </a>
        <h2 class="admin-page-title">{{ $config['singular'] }} Details</h2>
        <p class="admin-page-subtitle">Complete record information.</p>
    </div>

    <div class="show-actions">
        @can($module . '_edit')
            <a href="{{ $editUrl }}" class="btn-primary">
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
    </div>
</div>

<div class="detail-card">
    <div class="detail-section-head">
        <div class="detail-section-icon">
            <i class="{{ $config['icon'] }}"></i>
        </div>
        <p class="detail-section-title">{{ $config['singular'] }} #{{ $item->id }}</p>
    </div>

    <div class="detail-section-body">
        <div class="detail-row">
            <span class="detail-label">ID</span>
            <span class="detail-value code-pill">#{{ $item->id }}</span>
        </div>

        @foreach($config['fields'] as $name => $field)
            @php
                $value = $item->{$name} ?? null;
                $display = \App\Http\Controllers\Admin\HostelModuleController::displayValue($field, $value, $options[$name] ?? []);
            @endphp
            <div class="detail-row">
                <span class="detail-label">{{ $field['label'] }}</span>
                <span class="detail-value">
                    @if($field['type'] === 'file' && $value)
                        @if(preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $value))
                            <a href="{{ $display }}" target="_blank">
                                <img src="{{ $display }}" alt="{{ $field['label'] }}" style="width:120px;height:120px;object-fit:cover;border-radius:6px;border:1px solid #d1d5db;">
                            </a>
                        @else
                            <a href="{{ $display }}" target="_blank" class="btn-outline">View file</a>
                        @endif
                    @else
                        {{ $display }}
                    @endif
                </span>
            </div>
        @endforeach

        <div class="detail-row">
            <span class="detail-label">Created At</span>
            <span class="detail-value">{{ $item->created_at ?? '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Updated At</span>
            <span class="detail-value">{{ $item->updated_at ?? '-' }}</span>
        </div>
    </div>
</div>

@endsection
