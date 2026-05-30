@extends('layouts.admin')

@section('page-title', trans('cruds.user.title'))

@section('content')

<div class="admin-page-head">
    <div>
        <h2 class="admin-page-title">{{ trans('cruds.user.title') }}</h2>
        <p class="admin-page-subtitle">
            Manage all application users and their assigned roles
        </p>
    </div>

    @can('user_create')
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i>
            {{ trans('global.add') }} {{ trans('cruds.user.title_singular') }}
        </a>
    @endcan
</div>

<div class="stats-grid">
    <div class="stat-card">
        <p class="stat-label">Total Users</p>
        <p class="stat-value">{{ $users->count() }}</p>
    </div>

    <div class="stat-card">
        <p class="stat-label">Verified</p>
        <p class="stat-value">{{ $users->whereNotNull('email_verified_at')->count() }}</p>
    </div>

    <div class="stat-card">
        <p class="stat-label">Unverified</p>
        <p class="stat-value">{{ $users->whereNull('email_verified_at')->count() }}</p>
    </div>

    <div class="stat-card">
        <p class="stat-label">Added Today</p>
        <p class="stat-value">{{ $users->where('created_at', '>=', now()->startOfDay())->count() }}</p>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header">
        <p class="page-card-title">All Users</p>

        <span class="page-card-note">
            <i class="fas fa-info-circle"></i>
            Select rows to use bulk actions
        </span>
    </div>

    <div class="page-card-table">
        <table class="min-w-full datatable datatable-User">
            <thead>
                <tr>
                    <th style="width:40px;"></th>
                    <th>{{ trans('cruds.user.fields.id') }}</th>
                    <th>{{ trans('cruds.user.fields.name') }}</th>
                    <th>{{ trans('cruds.user.fields.email') }}</th>
                    <th>{{ trans('cruds.user.fields.email_verified_at') }}</th>
                    <th>{{ trans('cruds.user.fields.roles') }}</th>
                    <th style="text-align:right;">{{ trans('global.actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @foreach($users as $user)
                    <tr data-entry-id="{{ $user->id }}">
                        <td></td>

                        <td>
                            <span class="id-text">#{{ $user->id }}</span>
                        </td>

                        <td>
                            <div class="inline-flex-center">
                                @php
                                    $colors = ['#4F46E5','#0EA5E9','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899','#14B8A6'];
                                    $color  = $colors[$user->id % count($colors)];
                                @endphp

                                <div class="avatar-circle" style="background: {{ $color }};">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>

                                <div>
                                    <p class="table-main-text">{{ $user->name }}</p>
                                    <p class="table-sub-text">Member</p>
                                </div>
                            </div>
                        </td>

                        <td style="color:#475569;">
                            {{ $user->email }}
                        </td>

                        <td>
                            @if($user->email_verified_at)
                                <div class="d-flex align-items-center gap-2">
                                    <span class="status-dot status-success"></span>
                                    <span style="font-size:12.5px; color:#374151;">
                                        {{ $user->email_verified_at->format('d M Y') }}
                                    </span>
                                </div>
                            @else
                                <div class="d-flex align-items-center gap-2">
                                    <span class="status-dot status-warning"></span>
                                    <span style="font-size:12.5px; color:#92400E;">Pending</span>
                                </div>
                            @endif
                        </td>

                        <td>
                            <div class="tag-wrap">
                                @forelse($user->roles as $role)
                                    <span class="role-tag">{{ $role->title }}</span>
                                @empty
                                    <span style="font-size:12px; color:#94A3B8;">—</span>
                                @endforelse
                            </div>
                        </td>

                        <td>
                            <div class="action-row">
                                @can('user_show')
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn-outline">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>
                                @endcan

                                @can('user_edit')
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-outline btn-outline-edit">
                                        <i class="fas fa-pencil-alt"></i>
                                        Edit
                                    </a>
                                @endcan

                                @can('user_delete')
                                    <form action="{{ route('admin.users.destroy', $user->id) }}"
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
    initAdminDataTable('.datatable-User', {
        canDelete: @can('user_delete') true @else false @endcan,
        massDeleteUrl: "{{ route('admin.users.massDestroy') }}",
        deleteText: "{{ trans('global.datatables.delete') }}",
        zeroSelectedText: "{{ trans('global.datatables.zero_selected') }}",
        confirmText: "{{ trans('global.areYouSure') }}",
        searchPlaceholder: 'Search users...',
        infoText: 'Showing _START_–_END_ of _TOTAL_ users'
    });
});
</script>
@endsection