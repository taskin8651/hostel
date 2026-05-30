@extends('layouts.admin')

@section('page-title', trans('cruds.permission.title'))

@section('content')

<div class="admin-page-head">
    <div>
        <h2 class="admin-page-title">{{ trans('cruds.permission.title') }}</h2>
        <p class="admin-page-subtitle">
            Manage all application permissions
        </p>
    </div>

    @can('permission_create')
        <a href="{{ route('admin.permissions.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i>
            {{ trans('global.add') }} {{ trans('cruds.permission.title_singular') }}
        </a>
    @endcan
</div>

<div class="stats-grid">
    <div class="stat-card">
        <p class="stat-label">Total Permissions</p>
        <p class="stat-value">{{ $permissions->count() }}</p>
    </div>

    <div class="stat-card">
        <p class="stat-label">Added Today</p>
        <p class="stat-value">{{ $permissions->where('created_at', '>=', now()->startOfDay())->count() }}</p>
    </div>

    <div class="stat-card">
        <p class="stat-label">Added This Week</p>
        <p class="stat-value">{{ $permissions->where('created_at', '>=', now()->subDays(7))->count() }}</p>
    </div>

    <div class="stat-card">
        <p class="stat-label">Bulk Actions</p>
        <p class="stat-value">{{ $permissions->count() ? 'Enabled' : 'None' }}</p>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header">
        <p class="page-card-title">All Permissions</p>

        <span class="page-card-note">
            <i class="fas fa-info-circle"></i>
            Select rows to use bulk actions
        </span>
    </div>

    <div class="page-card-table">
        <table class="min-w-full datatable datatable-Permission">
            <thead>
                <tr>
                    <th style="width:40px;"></th>
                    <th>{{ trans('cruds.permission.fields.id') }}</th>
                    <th>{{ trans('cruds.permission.fields.title') }}</th>
                    <th style="text-align:right;">{{ trans('global.actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @foreach($permissions as $permission)
                    <tr data-entry-id="{{ $permission->id }}">
                        <td></td>

                        <td>
                            <span class="id-text">#{{ $permission->id }}</span>
                        </td>

                        <td style="color:#475569;">
                            {{ $permission->title }}
                        </td>

                        <td>
                            <div class="action-row">
                                @can('permission_show')
                                    <a href="{{ route('admin.permissions.show', $permission->id) }}" class="btn-outline">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>
                                @endcan

                                @can('permission_edit')
                                    <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="btn-outline btn-outline-edit">
                                        <i class="fas fa-pencil-alt"></i>
                                        Edit
                                    </a>
                                @endcan

                                @can('permission_delete')
                                    <form action="{{ route('admin.permissions.destroy', $permission->id) }}"
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
    initAdminDataTable('.datatable-Permission', {
        canDelete: @can('permission_delete') true @else false @endcan,
        massDeleteUrl: "{{ route('admin.permissions.massDestroy') }}",
        deleteText: "{{ trans('global.datatables.delete') }}",
        zeroSelectedText: "{{ trans('global.datatables.zero_selected') }}",
        confirmText: "{{ trans('global.areYouSure') }}",
        searchPlaceholder: 'Search permissions...',
        infoText: 'Showing _START_–_END_ of _TOTAL_ permissions'
    });
});
</script>
@endsection