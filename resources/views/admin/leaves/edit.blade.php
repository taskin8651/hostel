@extends('layouts.admin')

@section('page-title', ($item ? trans('global.edit') : trans('global.add')) . ' ' . $config['singular'])

@section('content')

@php
    $routeBase = 'admin.' . $module . '.';
    $hasDedicatedRoutes = \Illuminate\Support\Facades\Route::has($routeBase . 'index');
    $indexUrl = $hasDedicatedRoutes ? route($routeBase . 'index') : route('admin.hostel.modules.index', $module);
    $storeUrl = $hasDedicatedRoutes ? route($routeBase . 'store') : route('admin.hostel.modules.store', $module);
    $updateUrl = $hasDedicatedRoutes ? route($routeBase . 'update', $item->id ?? 0) : ($item ? route('admin.hostel.modules.update', [$module, $item->id]) : '#');
@endphp

<div class="admin-page-head">
    <div>
        <a href="{{ $indexUrl }}" class="admin-back-link">
            &larr; {{ trans('global.back_to_list') }}
        </a>
        <h2 class="admin-page-title">{{ $item ? trans('global.edit') : trans('global.add') }} {{ $config['singular'] }}</h2>
        <p class="admin-page-subtitle">Fill the details below and save the record.</p>
    </div>
</div>

<form method="POST" action="{{ $item ? $updateUrl : $storeUrl }}" enctype="multipart/form-data">
    @if($item)
        @method('PUT')
    @endif
    @csrf

    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-icon">
                <i class="{{ $config['icon'] }}"></i>
            </div>
            <div>
                <p class="form-card-title">{{ $config['singular'] }} Information</p>
                <p class="form-card-subtitle">Fields marked with * are required</p>
            </div>
        </div>

        <div class="form-card-body" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:16px;">
            @foreach($config['fields'] as $name => $field)
                @php
                    $oldValue = old($name, $item->{$name} ?? ($field['default'] ?? ''));
                    $isTextarea = $field['type'] === 'textarea';
                @endphp
                <div class="field-group" style="{{ $isTextarea ? 'grid-column:1 / -1;' : '' }}">
                    <label class="field-label" for="{{ $name }}">
                        {{ $field['label'] }}
                        @if($field['required'] ?? false)
                            <span class="req">*</span>
                        @endif
                    </label>

                    @if($field['type'] === 'textarea')
                        <textarea name="{{ $name }}" id="{{ $name }}" rows="4" class="field-input {{ $errors->has($name) ? 'error' : '' }}">{{ $oldValue }}</textarea>
                    @elseif($field['type'] === 'select')
                        <select name="{{ $name }}" id="{{ $name }}" class="field-input {{ $errors->has($name) ? 'error' : '' }}">
                            <option value="">{{ trans('global.pleaseSelect') }}</option>
                            @foreach($options[$name] ?? [] as $value => $label)
                                <option value="{{ $value }}" {{ (string) $oldValue === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    @elseif($field['type'] === 'file')
                        <input type="file" name="{{ $name }}" id="{{ $name }}" class="field-input {{ $errors->has($name) ? 'error' : '' }}">
                        @if($item && $item->{$name})
                            <p class="field-hint">
                                Current file:
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($item->{$name}) }}" target="_blank">View</a>
                            </p>
                        @endif
                    @else
                        <input type="{{ in_array($field['type'], ['email','number','date','time'], true) ? $field['type'] : 'text' }}"
                               name="{{ $name }}"
                               id="{{ $name }}"
                               value="{{ $oldValue }}"
                               class="field-input {{ $errors->has($name) ? 'error' : '' }}">
                    @endif

                    @if($errors->has($name))
                        <p class="field-error">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $errors->first($name) }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-primary">
            <i class="fas fa-save"></i>
            {{ trans('global.save') }}
        </button>
        <a href="{{ $indexUrl }}" class="btn-ghost">{{ trans('global.cancel') }}</a>
    </div>
</form>

@endsection
