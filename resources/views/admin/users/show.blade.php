@extends('layouts.admin')

@section('page-title', trans('global.show') . ' ' . trans('cruds.user.title_singular'))

@section('content')

@php
    $colors = ['#4F46E5','#0EA5E9','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899','#14B8A6'];
    $color = $colors[$user->id % count($colors)];
    $uniquePermissions = $user->roles->flatMap->permissions->unique('id');
@endphp

<div class="admin-page-head">
    <div>
        <a href="{{ route('admin.users.index') }}" class="admin-back-link">
            ← {{ trans('global.back_to_list') }}
        </a>

        <h2 class="admin-page-title">User Profile</h2>

        <p class="admin-page-subtitle">
            Full details for this user account
        </p>
    </div>

    <div class="show-actions">
        @can('user_edit')
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-primary">
                <i class="fas fa-pencil-alt"></i>
                Edit User
            </a>
        @endcan

        @can('user_delete')
            <form action="{{ route('admin.users.destroy', $user->id) }}"
                  method="POST"
                  onsubmit="return confirm('{{ trans('global.areYouSure') }}')">
                @method('DELETE')
                @csrf

                <button type="submit" class="btn-danger">
                    <i class="fas fa-trash-alt"></i>
                    Delete
                </button>
            </form>
        @endcan
    </div>
</div>

<div class="show-grid">

    <div>
        <div class="detail-card mb-3">
            <div class="profile-hero">
                <div class="profile-avatar-lg" style="background: {{ $color }};">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>

                <p class="profile-title">{{ $user->name }}</p>
                <p class="profile-subtitle">{{ $user->email }}</p>

                @if($user->email_verified_at)
                    <span class="status-pill success">
                        <i class="fas fa-check-circle"></i>
                        Verified
                    </span>
                @else
                    <span class="status-pill warning">
                        <i class="fas fa-clock"></i>
                        Unverified
                    </span>
                @endif
            </div>

            <div class="detail-section-pad-sm">
                <div class="d-grid gap-2" style="grid-template-columns: 1fr 1fr;">
                    <div class="stat-mini">
                        <p class="stat-mini-label">User ID</p>
                        <p class="stat-mini-value">#{{ $user->id }}</p>
                    </div>

                    <div class="stat-mini">
                        <p class="stat-mini-label">Roles</p>
                        <p class="stat-mini-value">{{ $user->roles->count() }}</p>
                    </div>

                    <div class="stat-mini" style="grid-column: span 2;">
                        <p class="stat-mini-label">Member Since</p>
                        <p class="stat-mini-value-sm">
                            {{ optional($user->created_at)->format('d M Y') ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card detail-card-pad">
            <p class="quick-title">Quick Actions</p>

            <div class="quick-list">
                @can('user_edit')
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="quick-link primary">
                        <i class="fas fa-user-edit"></i>
                        Edit Profile
                    </a>
                @endcan

                <a href="{{ route('admin.users.index') }}" class="quick-link">
                    <i class="fas fa-list"></i>
                    All Users
                </a>

                @can('user_create')
                    <a href="{{ route('admin.users.create') }}" class="quick-link">
                        <i class="fas fa-user-plus"></i>
                        Add New User
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <div>
        <div class="detail-card mb-3">
            <div class="detail-section-head">
                <div class="detail-section-icon">
                    <i class="fas fa-id-card"></i>
                </div>

                <p class="detail-section-title">Account Details</p>
            </div>

            <div class="detail-section-body">
                <div class="detail-row">
                    <span class="detail-label">{{ trans('cruds.user.fields.id') }}</span>
                    <span class="detail-value code-pill">#{{ $user->id }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">{{ trans('cruds.user.fields.name') }}</span>
                    <span class="detail-value">{{ $user->name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">{{ trans('cruds.user.fields.email') }}</span>

                    <div>
                        <span class="detail-value">{{ $user->email }}</span>
                        <a href="mailto:{{ $user->email }}" class="send-mail-link">
                            Send Email
                        </a>
                    </div>
                </div>

                <div class="detail-row">
                    <span class="detail-label">{{ trans('cruds.user.fields.email_verified_at') }}</span>

                    @if($user->email_verified_at)
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <span class="detail-value">
                                {{ $user->email_verified_at->format('d M Y, H:i') }}
                            </span>
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-exclamation-circle text-warning"></i>
                            <span class="detail-value" style="color:#92400E;">Not verified</span>
                        </div>
                    @endif
                </div>

                <div class="detail-row">
                    <span class="detail-label">Created At</span>
                    <span class="detail-value">
                        {{ optional($user->created_at)->format('d M Y, H:i') ?? '-' }}
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Updated At</span>
                    <span class="detail-value">
                        {{ optional($user->updated_at)->format('d M Y, H:i') ?? '-' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-section-head between">
                <div class="d-flex align-items-center gap-2">
                    <div class="detail-section-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>

                    <p class="detail-section-title">{{ trans('cruds.user.fields.roles') }}</p>
                </div>

                <span class="status-pill success">
                    {{ $user->roles->count() }} assigned
                </span>
            </div>

            <div class="detail-section-pad-sm">
                @if($user->roles->count())
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @foreach($user->roles as $role)
                            <span class="role-tag">
                                <i class="fas fa-circle" style="font-size:6px; margin-right:5px;"></i>
                                {{ $role->title }}
                            </span>
                        @endforeach
                    </div>

                    @if($uniquePermissions->count())
                        <div class="permission-summary">
                            <p class="permission-summary-title">Permissions via roles</p>

                            <div class="d-flex flex-wrap gap-1">
                                @foreach($uniquePermissions->take(12) as $perm)
                                    <span class="mini-permission">{{ $perm->title }}</span>
                                @endforeach

                                @if($uniquePermissions->count() > 12)
                                    <span class="mini-permission more">
                                        +{{ $uniquePermissions->count() - 12 }} more
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                @else
                    <div class="assign-empty">
                        <div class="assign-empty-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>

                        <p class="assign-empty-title">No roles assigned</p>
                        <p class="assign-empty-text">This user has no roles yet.</p>

                        @can('user_edit')
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-primary mt-3">
                                <i class="fas fa-plus"></i>
                                Assign Roles
                            </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection