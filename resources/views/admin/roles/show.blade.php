@extends('layouts.admin')

@section('page-title', trans('global.show') . ' ' . trans('cruds.role.title'))

@section('content')

@php
    $colors = ['#4F46E5','#0EA5E9','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899','#14B8A6'];
    $color  = $colors[$role->id % count($colors)];
@endphp

<div class="admin-page-head">
    <div>
        <a href="{{ route('admin.roles.index') }}" class="admin-back-link">
            ← {{ trans('global.back_to_list') }}
        </a>

        <h2 class="admin-page-title">
            {{ trans('global.show') }} {{ trans('cruds.role.title') }}
        </h2>

        <p class="admin-page-subtitle">
            View role details and assigned permissions
        </p>
    </div>

    @can('role_edit')
        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn-primary">
            <i class="fas fa-pencil-alt"></i>
            {{ trans('global.edit') }}
        </a>
    @endcan
</div>

<div class="show-grid-role">

    <div class="profile-card-role">
        <div class="text-center mb-4">
            <div class="profile-avatar-md" style="background: {{ $color }};">
                <i class="fas fa-shield-alt"></i>
            </div>

            <h3 class="profile-title-lg">{{ $role->title }}</h3>
            <p class="profile-subtitle mb-0">Role</p>
        </div>

        <div class="role-stats-grid">
            <div class="text-center">
                <p class="stat-mini-value">{{ $role->permissions->count() }}</p>
                <p class="detail-label-sm mb-0">Permissions</p>
            </div>

            <div class="text-center">
                <p class="stat-mini-value">{{ optional($role->users)->count() ?? 0 }}</p>
                <p class="detail-label-sm mb-0">Users</p>
            </div>
        </div>

        <div class="role-info-box">
            <p>
                <i class="fas fa-info-circle"></i>
                This role defines access permissions for assigned users in the system.
            </p>
        </div>
    </div>

    <div class="detail-card">

        <div class="detail-section-pad border-bottom">
            <h4 class="detail-section-title mb-3">Basic Information</h4>

            <div class="detail-row-icon">
                <div class="detail-icon">
                    <i class="fas fa-hashtag"></i>
                </div>

                <div class="detail-content">
                    <div class="detail-label-sm">Role ID</div>
                    <div class="detail-value-dark">#{{ $role->id }}</div>
                </div>
            </div>

            <div class="detail-row-icon">
                <div class="detail-icon">
                    <i class="fas fa-tag"></i>
                </div>

                <div class="detail-content">
                    <div class="detail-label-sm">Role Title</div>
                    <div class="detail-value-dark">{{ $role->title }}</div>
                </div>
            </div>

            <div class="detail-row-icon">
                <div class="detail-icon">
                    <i class="fas fa-calendar"></i>
                </div>

                <div class="detail-content">
                    <div class="detail-label-sm">Created</div>
                    <div class="detail-value-dark">
                        {{ optional($role->created_at)->format('M j, Y \a\t g:i A') }}
                    </div>
                </div>
            </div>

            <div class="detail-row-icon">
                <div class="detail-icon">
                    <i class="fas fa-clock"></i>
                </div>

                <div class="detail-content">
                    <div class="detail-label-sm">Last Updated</div>
                    <div class="detail-value-dark">
                        {{ optional($role->updated_at)->format('M j, Y \a\t g:i A') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-section-pad border-bottom">
            <h4 class="detail-section-title mb-3">Assigned Permissions</h4>

            @if($role->permissions->count())
                <div class="d-flex flex-wrap">
                    @foreach($role->permissions as $permission)
                        <span class="permission-tag">{{ $permission->title }}</span>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-key"></i>
                    <p>No permissions assigned to this role</p>
                </div>
            @endif
        </div>

        <div class="detail-section-pad">
            <h4 class="detail-section-title mb-3">
                Assigned Users ({{ optional($role->users)->count() ?? 0 }})
            </h4>

            @if(optional($role->users)->count())
                <div class="d-grid gap-3" style="grid-template-columns: repeat(auto-fill, minmax(200px,1fr));">
                    @foreach($role->users as $user)
                        <div class="assigned-user-card">
                            <div class="user-avatar" style="background: {{ $color }};">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>

                            <div>
                                <p class="table-main-text">{{ $user->name }}</p>
                                <p class="table-sub-text">{{ $user->email }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <p>No users assigned to this role</p>
                </div>
            @endif
        </div>

    </div>

</div>

@endsection