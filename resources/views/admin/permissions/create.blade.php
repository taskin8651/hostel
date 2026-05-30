@extends('layouts.admin')

@section('page-title', trans('global.add') . ' ' . trans('cruds.permission.title_singular'))

@section('content')

<div class="admin-page-head">
    <div>
        <a href="{{ route('admin.permissions.index') }}" class="admin-back-link">
            ← {{ trans('global.back_to_list') }}
        </a>

        <h2 class="admin-page-title">
            {{ trans('global.add') }} {{ trans('cruds.permission.title_singular') }}
        </h2>

        <p class="admin-page-subtitle">
            Create a new permission for system access control
        </p>
    </div>
</div>

<form method="POST" action="{{ route('admin.permissions.store') }}">
    @csrf

    <div class="admin-form-wrap-sm">

        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-icon">
                    <i class="fas fa-key"></i>
                </div>

                <div>
                    <p class="form-card-title">Permission Information</p>
                    <p class="form-card-subtitle">Define access control details</p>
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
                               value="{{ old('title') }}"
                               required
                               placeholder="Enter permission title"
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

            </div>
        </div>

    </div>

    <div class="form-actions">
        <button type="submit" class="btn-primary">
            <i class="fas fa-check"></i>
            {{ trans('global.save') }}
        </button>

        <a href="{{ route('admin.permissions.index') }}" class="btn-ghost">
            {{ trans('global.cancel') }}
        </a>
    </div>

</form>

@endsection