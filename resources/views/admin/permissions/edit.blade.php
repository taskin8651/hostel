@extends('layouts.admin')

@section('page-title', trans('global.edit') . ' ' . trans('cruds.permission.title_singular'))

@section('content')

@php
    $colors = ['#4F46E5','#0EA5E9','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899','#14B8A6'];
@endphp

<div class="admin-page-head">
    <div>
        <a href="{{ route('admin.permissions.index') }}" class="admin-back-link">
            ← {{ trans('global.back_to_list') }}
        </a>

        <h2 class="admin-page-title">
            {{ trans('global.edit') }} {{ trans('cruds.permission.title_singular') }}
        </h2>

        <p class="admin-page-subtitle">
            Update permission details
        </p>
    </div>

    <div class="identity-card">
        <div class="identity-avatar" style="background: {{ $colors[$permission->id % count($colors)] }};">
            <i class="fas fa-key"></i>
        </div>

        <div>
            <p class="identity-title">{{ $permission->title }}</p>
            <p class="identity-subtitle">ID #{{ $permission->id }}</p>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.permissions.update', $permission->id) }}">
    @method('PUT')
    @csrf

    <div class="admin-form-wrap-sm">

        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-icon">
                    <i class="fas fa-edit"></i>
                </div>

                <div>
                    <p class="form-card-title">Permission Information</p>
                    <p class="form-card-subtitle">Update access control details</p>
                </div>
            </div>

            <div class="form-card-body">

                <div class="field-group">
                    <label class="field-label" for="title">
                        {{ trans('cruds.permission.fields.title') }} <span class="req">*</span>
                    </label>

                    <div class="input-icon-wrap">
                        <i class="fas fa-tag icon"></i>

                        <input type="text"
                               name="title"
                               id="title"
                               value="{{ old('title', $permission->title) }}"
                               required
                               class="field-input {{ $errors->has('title') ? 'error' : '' }}">
                    </div>

                    @if($errors->has('title'))
                        <p class="field-error">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $errors->first('title') }}
                        </p>
                    @elseif(trans('cruds.permission.fields.title_helper'))
                        <p class="field-hint">{{ trans('cruds.permission.fields.title_helper') }}</p>
                    @endif
                </div>

                <div class="form-info-box">
                    <p class="meta-label">Permission Info</p>

                    <div class="meta-grid-2">
                        <div>
                            <p class="meta-small-label">Created</p>
                            <p class="meta-value-strong">
                                {{ optional($permission->created_at)->format('d M Y') ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="meta-small-label">Last Updated</p>
                            <p class="meta-value-strong">
                                {{ optional($permission->updated_at)->format('d M Y') ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <div class="form-actions-between">
        <div class="form-actions-left">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i>
                {{ trans('global.save') }}
            </button>

            <a href="{{ route('admin.permissions.index') }}" class="btn-ghost">
                {{ trans('global.cancel') }}
            </a>
        </div>

        @can('permission_delete')
            <button type="submit"
                    form="delete-permission-form"
                    class="btn-danger">
                <i class="fas fa-trash-alt"></i>
                Delete Permission
            </button>
        @endcan
    </div>
</form>

@can('permission_delete')
    <form id="delete-permission-form"
          action="{{ route('admin.permissions.destroy', $permission->id) }}"
          method="POST"
          onsubmit="return confirm('{{ trans('global.areYouSure') }}')">
        @method('DELETE')
        @csrf
    </form>
@endcan

@endsection