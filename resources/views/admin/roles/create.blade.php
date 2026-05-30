@extends('layouts.admin')

@section('page-title', trans('global.add') . ' ' . trans('cruds.role.title_singular'))

@section('content')

<div class="admin-page-head">
    <div>
        <a href="{{ route('admin.roles.index') }}" class="admin-back-link">
            ← {{ trans('global.back_to_list') }}
        </a>

        <h2 class="admin-page-title">
            {{ trans('global.add') }} {{ trans('cruds.role.title_singular') }}
        </h2>

        <p class="admin-page-subtitle">
            Create a new role and assign permissions
        </p>
    </div>
</div>

<form method="POST" action="{{ route('admin.roles.store') }}">
    @csrf

    <div class="admin-form-grid">

        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>

                <div>
                    <p class="form-card-title">Role Information</p>
                    <p class="form-card-subtitle">Basic role details</p>
                </div>
            </div>

            <div class="form-card-body">

                <div class="field-group">
                    <label class="field-label" for="title">
                        {{ trans('cruds.role.fields.title') }} <span class="req">*</span>
                    </label>

                    <div class="input-icon-wrap">
                        <i class="fas fa-tag icon"></i>

                        <input type="text"
                               name="title"
                               id="title"
                               value="{{ old('title') }}"
                               required
                               placeholder="Enter role title"
                               class="field-input {{ $errors->has('title') ? 'error' : '' }}">
                    </div>

                    @if($errors->has('title'))
                        <p class="field-error">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $errors->first('title') }}
                        </p>
                    @elseif(trans('cruds.role.fields.title_helper'))
                        <p class="field-hint">{{ trans('cruds.role.fields.title_helper') }}</p>
                    @endif
                </div>

            </div>
        </div>

        <div class="form-card">
            <div class="form-card-header between">
                <div class="form-card-head-left">
                    <div class="form-card-icon">
                        <i class="fas fa-key"></i>
                    </div>

                    <div>
                        <p class="form-card-title">{{ trans('cruds.role.fields.permissions') }}</p>
                        <p class="form-card-subtitle">Assign access permissions</p>
                    </div>
                </div>

                <div class="form-mini-actions">
                    <button type="button" class="btn-mini-primary" data-check-all=".role-checkbox-item">
                        All
                    </button>

                    <button type="button" class="btn-mini-ghost" data-uncheck-all=".role-checkbox-item">
                        None
                    </button>
                </div>
            </div>

            <div class="form-card-body">

                <div class="checkbox-grid">
                    @foreach($permissions as $id => $permission)
                        <label class="role-checkbox-item {{ in_array($id, old('permissions', [])) ? 'checked' : '' }}">
                            <input type="checkbox"
                                   name="permissions[]"
                                   value="{{ $id }}"
                                   class="role-checkbox"
                                   {{ in_array($id, old('permissions', [])) ? 'checked' : '' }}>

                            <div class="check-icon"></div>

                            <span class="checkbox-text">{{ $permission }}</span>
                        </label>
                    @endforeach
                </div>

                @if($errors->has('permissions'))
                    <p class="field-error mt-2">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $errors->first('permissions') }}
                    </p>
                @endif

                <div class="form-info-box">
                    <p>
                        <i class="fas fa-info-circle"></i>
                        Permissions control what this role can access in the system.
                    </p>
                </div>

            </div>
        </div>

    </div>

    <div class="form-actions">
        <button type="submit" class="btn-primary">
            <i class="fas fa-check"></i>
            {{ trans('global.save') }}
        </button>

        <a href="{{ route('admin.roles.index') }}" class="btn-ghost">
            {{ trans('global.cancel') }}
        </a>
    </div>

</form>

@endsection