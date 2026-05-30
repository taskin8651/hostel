@extends('layouts.admin')

@section('page-title', trans('global.show') . ' ' . trans('cruds.permission.title_singular'))

@section('content')

@php
    $colors = ['#4F46E5','#0EA5E9','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899','#14B8A6'];
    $color = $colors[$permission->id % count($colors)];
@endphp

<div class="admin-page-head">
    <div>
        <a href="{{ route('admin.permissions.index') }}" class="admin-back-link">
            ← {{ trans('global.back_to_list') }}
        </a>

        <h2 class="admin-page-title">Permission Details</h2>

        <p class="admin-page-subtitle">
            Full details for this permission
        </p>
    </div>

    <div class="show-actions">
        @can('permission_edit')
            <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="btn-primary">
                <i class="fas fa-pencil-alt"></i>
                Edit Permission
            </a>
        @endcan

        @can('permission_delete')
            <form action="{{ route('admin.permissions.destroy', $permission->id) }}"
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
                    <i class="fas fa-key"></i>
                </div>

                <p class="profile-title">{{ $permission->title }}</p>
                <p class="profile-subtitle">Permission</p>

                <span class="status-pill success">
                    <i class="fas fa-check-circle"></i>
                    Active
                </span>
            </div>

            <div class="detail-section-pad-sm">
                <div class="d-grid gap-2">
                    <div class="stat-mini">
                        <p class="stat-mini-label">Permission ID</p>
                        <p class="stat-mini-value">#{{ $permission->id }}</p>
                    </div>

                    <div class="stat-mini">
                        <p class="stat-mini-label">Created</p>
                        <p class="stat-mini-value-sm">
                            {{ optional($permission->created_at)->format('d M Y') ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card detail-card-pad">
            <p class="quick-title">Quick Actions</p>

            <div class="quick-list">
                @can('permission_edit')
                    <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="quick-link primary">
                        <i class="fas fa-edit"></i>
                        Edit Permission
                    </a>
                @endcan

                <a href="{{ route('admin.permissions.index') }}" class="quick-link">
                    <i class="fas fa-list"></i>
                    All Permissions
                </a>

                @can('permission_create')
                    <a href="{{ route('admin.permissions.create') }}" class="quick-link">
                        <i class="fas fa-plus"></i>
                        Add New Permission
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <div>
        <div class="detail-card">
            <div class="detail-section-head">
                <div class="detail-section-icon">
                    <i class="fas fa-info-circle"></i>
                </div>

                <p class="detail-section-title">Permission Details</p>
            </div>

            <div class="detail-section-body">
                <div class="detail-row">
                    <span class="detail-label">{{ trans('cruds.permission.fields.id') }}</span>
                    <span class="detail-value code-pill">#{{ $permission->id }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">{{ trans('cruds.permission.fields.title') }}</span>
                    <span class="detail-value">{{ $permission->title }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Created At</span>
                    <span class="detail-value">
                        {{ optional($permission->created_at)->format('d M Y, H:i') ?? '-' }}
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Updated At</span>
                    <span class="detail-value">
                        {{ optional($permission->updated_at)->format('d M Y, H:i') ?? '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection