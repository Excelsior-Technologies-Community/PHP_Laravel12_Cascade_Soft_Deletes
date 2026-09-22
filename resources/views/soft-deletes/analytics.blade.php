@extends('layouts.app')

@section('content')

<div class="page-header">

    <div>
        <h2>📊 Soft Delete Analytics</h2>

        <p style="color:#64748b;">
            Monitor active, deleted, restored and cascade-deleted records.
        </p>
    </div>

    <a href="{{ route('soft-deletes.index') }}"
       class="btn btn-primary">

        🗑️ Deleted Records

    </a>

</div>


{{-- Category Statistics --}}

<h3>📁 Category Statistics</h3>

<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-title">
            Active Categories
        </div>

        <div class="stat-number">
            {{ $activeCategories }}
        </div>
    </div>


    <div class="stat-card">
        <div class="stat-title">
            Deleted Categories
        </div>

        <div class="stat-number">
            {{ $deletedCategories }}
        </div>
    </div>


    <div class="stat-card">
        <div class="stat-title">
            Total Categories
        </div>

        <div class="stat-number">
            {{ $totalCategories }}
        </div>
    </div>


    <div class="stat-card">
        <div class="stat-title">
            Restored Categories
        </div>

        <div class="stat-number">
            {{ $restoredCategories }}
        </div>
    </div>

</div>


{{-- Product Statistics --}}

<h3 style="margin-top:30px;">
    📦 Product Statistics
</h3>

<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-title">
            Active Products
        </div>

        <div class="stat-number">
            {{ $activeProducts }}
        </div>
    </div>


    <div class="stat-card">
        <div class="stat-title">
            Deleted Products
        </div>

        <div class="stat-number">
            {{ $deletedProducts }}
        </div>
    </div>


    <div class="stat-card">
        <div class="stat-title">
            Cascade Deleted Products
        </div>

        <div class="stat-number">
            {{ $cascadeDeletedProducts }}
        </div>
    </div>


    <div class="stat-card">
        <div class="stat-title">
            Direct Deleted Products
        </div>

        <div class="stat-number">
            {{ $directDeletedProducts }}
        </div>
    </div>


    <div class="stat-card">
        <div class="stat-title">
            Total Products
        </div>

        <div class="stat-number">
            {{ $totalProducts }}
        </div>
    </div>


    <div class="stat-card">
        <div class="stat-title">
            Restored Products
        </div>

        <div class="stat-number">
            {{ $restoredProducts }}
        </div>
    </div>

</div>


{{-- Recent History --}}

<div class="card" style="margin-top:30px;">

    <div class="section-header">

        <h3>
            🕒 Recent Soft Delete History
        </h3>

        <span class="badge">
            Latest 15 Events
        </span>

    </div>


    @if($recentHistory->count())

        <table class="table">

            <thead>

                <tr>
                    <th>Entity</th>
                    <th>ID</th>
                    <th>Action</th>
                    <th>Source</th>
                    <th>Event Time</th>
                </tr>

            </thead>

            <tbody>

            @foreach($recentHistory as $history)

                <tr>

                    <td>
                        {{ ucfirst($history->entity_type) }}
                    </td>

                    <td>
                        #{{ $history->entity_id }}
                    </td>

                    <td>

                        @if($history->action === 'deleted')

                            <span class="history-deleted">
                                🗑 Deleted
                            </span>

                        @else

                            <span class="history-restored">
                                ♻️ Restored
                            </span>

                        @endif

                    </td>

                    <td>

                        @if($history->deletion_source === 'cascade')

                            <span class="badge badge-cascade">
                                🔄 Cascade
                            </span>

                        @elseif($history->deletion_source === 'direct')

                            <span class="badge badge-direct">
                                🗑 Direct
                            </span>

                        @else

                            —

                        @endif

                    </td>

                    <td>
                        {{ $history->event_at?->format('d M Y h:i A') }}
                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

    @else

        <div class="empty-state">

            <h3>📭 No History Available</h3>

            <p>
                Soft delete activity will appear here.
            </p>

        </div>

    @endif

</div>

@endsection