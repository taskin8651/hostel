@extends('layouts.admin')

@section('page-title', trans('cruds.role.title'))

@section('content')

<div class="admin-page-head">
    <div>
        <h2 class="admin-page-title">{{ trans('cruds.role.title') }}</h2>
        <p class="admin-page-subtitle">
            Manage roles and their assigned permissions
        </p>
    </div>

    @can('role_create')
        <a href="{{ route('admin.roles.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i>
            {{ trans('global.add') }} {{ trans('cruds.role.title_singular') }}
        </a>
    @endcan
</div>

<div class="stats-grid">
    <div class="stat-card">
        <p class="stat-label">Total Roles</p>
        <p class="stat-value">{{ $roles->count() }}</p>
    </div>

    <div class="stat-card">
        <p class="stat-label">Total Permissions</p>
        <p class="stat-value">
            {{ $roles->sum(function($role) { return $role->permissions->count(); }) }}
        </p>
    </div>

    <div class="stat-card">
        <p class="stat-label">Users Assigned</p>
        <p class="stat-value">
            {{ $roles->sum(function($role) { return optional($role->users)->count() ?? 0; }) }}
        </p>
    </div>

    <div class="stat-card">
        <p class="stat-label">Avg Permissions</p>
        <p class="stat-value">
            {{ $roles->count() > 0 ? round($roles->avg(function($role) { return $role->permissions->count(); }), 1) : 0 }}
        </p>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header">
        <p class="page-card-title">All Roles</p>

        <span class="page-card-note">
            <i class="fas fa-info-circle"></i>
            Select rows to use bulk actions
        </span>
    </div>

    <div class="page-card-table">
        <table class="min-w-full datatable datatable-Role">
            <thead>
                <tr>
                    <th style="width:40px;"></th>
                    <th>{{ trans('cruds.role.fields.id') }}</th>
                    <th>{{ trans('cruds.role.fields.title') }}</th>
                    <th>{{ trans('cruds.role.fields.permissions') }}</th>
                    <th style="text-align:right;">{{ trans('global.actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @foreach($roles as $role)
                    <tr data-entry-id="{{ $role->id }}">
                        <td></td>

                        <td>
                            <span class="id-text">#{{ $role->id }}</span>
                        </td>

                        <td>
                            <div class="inline-flex-center">
                                @php
                                    $colors = ['#4F46E5','#0EA5E9','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899','#14B8A6'];
                                    $color  = $colors[$role->id % count($colors)];
                                @endphp

                                <div class="avatar-circle" style="background: {{ $color }};">
                                    <i class="fas fa-shield-alt"></i>
                                </div>

                                <div>
                                    <p class="table-main-text">{{ $role->title }}</p>
                                    <p class="table-sub-text">Role</p>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="tag-wrap">
                                @forelse($role->permissions as $permission)
                                    <span class="role-tag">{{ $permission->title }}</span>
                                @empty
                                    <span style="font-size:12px; color:#94A3B8;">—</span>
                                @endforelse
                            </div>
                        </td>

                        <td>
                            <div class="action-row">
                                @can('role_show')
                                    <a href="{{ route('admin.roles.show', $role->id) }}" class="btn-outline">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>
                                @endcan

                                @can('role_edit')
                                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn-outline btn-outline-edit">
                                        <i class="fas fa-pencil-alt"></i>
                                        Edit
                                    </a>
                                @endcan

                                @can('role_delete')
                                    <form action="{{ route('admin.roles.destroy', $role->id) }}"
                                          method="POST"
                                          style="display:inline;"
                                          onsubmit="return confirm('{{ trans('global.areYouSure') }}')">
                                        @method('DELETE')
                                        @csrf

                                        <button type="submit" class="btn-outline btn-outline-danger">
                                            <i class="fas fa-trash-alt"></i>
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>

@endsection

@section('scripts')
@parent
<script>
$(function () {
    initAdminDataTable('.datatable-Role', {
        canDelete: @can('role_delete') true @else false @endcan,
        massDeleteUrl: "{{ route('admin.roles.massDestroy') }}",
        deleteText: "{{ trans('global.datatables.delete') }}",
        zeroSelectedText: "{{ trans('global.datatables.zero_selected') }}",
        confirmText: "{{ trans('global.areYouSure') }}",
        searchPlaceholder: 'Search roles...',
        infoText: 'Showing _START_–_END_ of _TOTAL_ roles'
    });
});
</script>
@endsection