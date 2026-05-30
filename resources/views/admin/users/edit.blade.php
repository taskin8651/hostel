@extends('layouts.admin')

@section('page-title', trans('global.edit') . ' ' . trans('cruds.user.title_singular'))

@section('content')

@php
    $colors = ['#4F46E5','#0EA5E9','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899','#14B8A6'];
@endphp

<div class="admin-page-head">
    <div>
        <a href="{{ route('admin.users.index') }}" class="admin-back-link">
            ← {{ trans('global.back_to_list') }}
        </a>

        <h2 class="admin-page-title">
            {{ trans('global.edit') }} {{ trans('cruds.user.title_singular') }}
        </h2>

        <p class="admin-page-subtitle">
            Update account details and role assignments
        </p>
    </div>

    <div class="identity-card">
        <div class="identity-avatar" style="background: {{ $colors[$user->id % count($colors)] }};">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>

        <div>
            <p class="identity-title">{{ $user->name }}</p>
            <p class="identity-subtitle">ID #{{ $user->id }}</p>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.users.update', $user->id) }}">
    @method('PUT')
    @csrf

    <div class="admin-form-grid">

        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-icon">
                    <i class="fas fa-user-edit"></i>
                </div>

                <div>
                    <p class="form-card-title">User Information</p>
                    <p class="form-card-subtitle">Update account credentials</p>
                </div>
            </div>

            <div class="form-card-body">

                <div class="field-group">
                    <label class="field-label" for="name">
                        {{ trans('cruds.user.fields.name') }} <span class="req">*</span>
                    </label>

                    <div class="input-icon-wrap">
                        <i class="fas fa-user icon"></i>

                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $user->name) }}"
                               required
                               class="field-input {{ $errors->has('name') ? 'error' : '' }}">
                    </div>

                    @if($errors->has('name'))
                        <p class="field-error">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>

                <div class="field-group">
                    <label class="field-label" for="email">
                        {{ trans('cruds.user.fields.email') }} <span class="req">*</span>
                    </label>

                    <div class="input-icon-wrap">
                        <i class="fas fa-envelope icon"></i>

                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email', $user->email) }}"
                               required
                               class="field-input {{ $errors->has('email') ? 'error' : '' }}">
                    </div>

                    @if($errors->has('email'))
                        <p class="field-error">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $errors->first('email') }}
                        </p>
                    @endif
                </div>

                <div class="field-group">
                    <label class="field-label" for="password">
                        {{ trans('cruds.user.fields.password') }}
                        <span class="field-hint">(optional)</span>
                    </label>

                    <div class="input-icon-wrap has-eye">
                        <i class="fas fa-lock icon"></i>

                        <input type="password"
                               name="password"
                               id="password"
                               placeholder="Leave blank to keep current password"
                               class="field-input {{ $errors->has('password') ? 'error' : '' }}">

                        <button type="button" class="eye-toggle" onclick="togglePass('password', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    @if($errors->has('password'))
                        <p class="field-error">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $errors->first('password') }}
                        </p>
                    @else
                        <p class="field-hint">{{ trans('cruds.user.fields.password_helper') }}</p>
                    @endif
                </div>

                <div class="form-info-box">
                    <p class="meta-label">Account Info</p>

                    <div class="meta-grid-2">
                        <div>
                            <p class="meta-small-label">Joined</p>
                            <p class="meta-value-strong">
                                {{ optional($user->created_at)->format('d M Y') ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="meta-small-label">Email Status</p>

                            @if($user->email_verified_at)
                                <p class="meta-value-strong meta-value-success">
                                    <i class="fas fa-check-circle"></i>
                                    Verified
                                </p>
                            @else
                                <p class="meta-value-strong meta-value-warning">
                                    <i class="fas fa-clock"></i>
                                    Pending
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="form-card">
            <div class="form-card-header between">
                <div class="form-card-head-left">
                    <div class="form-card-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>

                    <div>
                        <p class="form-card-title">{{ trans('cruds.user.fields.roles') }}</p>
                        <p class="form-card-subtitle">Update permissions</p>
                    </div>
                </div>

                <div class="form-mini-actions">
                    <button type="button"
                            class="btn-mini-primary"
                            data-check-all=".role-checkbox-item">
                        All
                    </button>

                    <button type="button"
                            class="btn-mini-ghost"
                            data-uncheck-all=".role-checkbox-item">
                        None
                    </button>
                </div>
            </div>

            <div class="form-card-body">

                <div class="checkbox-grid">
                    @foreach($roles as $id => $role)
                        @php
                            $isChecked = in_array($id, old('roles', [])) || $user->roles->contains($id);
                        @endphp

                        <label class="role-checkbox-item {{ $isChecked ? 'checked' : '' }}">
                            <input type="checkbox"
                                   name="roles[]"
                                   value="{{ $id }}"
                                   class="role-checkbox"
                                   {{ $isChecked ? 'checked' : '' }}>

                            <div class="check-icon"></div>

                            <span class="checkbox-text">{{ $role }}</span>
                        </label>
                    @endforeach
                </div>

                @if($errors->has('roles'))
                    <p class="field-error mt-2">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $errors->first('roles') }}
                    </p>
                @endif

                <div class="form-info-box">
                    <p>
                        <i class="fas fa-info-circle"></i>
                        Changes to roles take effect immediately after saving.
                    </p>
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

            <a href="{{ route('admin.users.index') }}" class="btn-ghost">
                {{ trans('global.cancel') }}
            </a>
        </div>

        @can('user_delete')
            <button type="submit"
                    form="delete-user-form"
                    class="btn-danger">
                <i class="fas fa-trash-alt"></i>
                Delete User
            </button>
        @endcan
    </div>
</form>

@can('user_delete')
    <form id="delete-user-form"
          action="{{ route('admin.users.destroy', $user->id) }}"
          method="POST"
          onsubmit="return confirm('{{ trans('global.areYouSure') }}')">
        @method('DELETE')
        @csrf
    </form>
@endcan

@endsection