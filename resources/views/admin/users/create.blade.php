@extends('layouts.admin')

@section('page-title', trans('global.add') . ' ' . trans('cruds.user.title_singular'))

@section('content')

<div class="admin-page-head">
    <div>
        <a href="{{ route('admin.users.index') }}" class="admin-back-link">
            ← {{ trans('global.back_to_list') }}
        </a>

        <h2 class="admin-page-title">
            {{ trans('global.add') }} {{ trans('cruds.user.title_singular') }}
        </h2>

        <p class="admin-page-subtitle">
            Fill in the details to create a new user account
        </p>
    </div>
</div>

<form method="POST" action="{{ route('admin.users.store') }}">
    @csrf

    <div class="admin-form-grid">

        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-icon">
                    <i class="fas fa-user"></i>
                </div>

                <div>
                    <p class="form-card-title">User Information</p>
                    <p class="form-card-subtitle">Basic account details</p>
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
                               value="{{ old('name') }}"
                               required
                               placeholder="Enter full name"
                               class="field-input {{ $errors->has('name') ? 'error' : '' }}">
                    </div>

                    @if($errors->has('name'))
                        <p class="field-error">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $errors->first('name') }}
                        </p>
                    @elseif(trans('cruds.user.fields.name_helper'))
                        <p class="field-hint">{{ trans('cruds.user.fields.name_helper') }}</p>
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
                               value="{{ old('email') }}"
                               required
                               placeholder="user@example.com"
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
                        {{ trans('cruds.user.fields.password') }} <span class="req">*</span>
                    </label>

                    <div class="input-icon-wrap has-eye">
                        <i class="fas fa-lock icon"></i>

                        <input type="password"
                               name="password"
                               id="password"
                               required
                               placeholder="Create a strong password"
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
                        <p class="field-hint">
                            <i class="fas fa-shield-alt"></i>
                            Min. 8 characters recommended
                        </p>
                    @endif
                </div>

                <div class="password-strength">
                    <div class="strength-bars">
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                    </div>

                    <p id="strength-text" class="strength-text"></p>
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
                    @foreach($roles as $id => $role)
                        <label class="role-checkbox-item {{ in_array($id, old('roles', [])) ? 'checked' : '' }}">
                            <input type="checkbox"
                                   name="roles[]"
                                   value="{{ $id }}"
                                   class="role-checkbox"
                                   {{ in_array($id, old('roles', [])) ? 'checked' : '' }}>

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
                        Roles control what this user can see and do in the admin panel.
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

        <a href="{{ route('admin.users.index') }}" class="btn-ghost">
            {{ trans('global.cancel') }}
        </a>
    </div>

</form>

@endsection