@extends('layouts.app')

@section('title', 'System Activity & Audit Logs')

@section('content')
<div class="container" style="max-width: 1400px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="text-align: left; margin: 0;">System Audit &amp; Activity Logs</h1>
            <p style="color: #6b7280; font-size: 14px; margin-top: 4px;">Real-time audit log of all administrative actions, orders, stock adjustments, and customer transactions.</p>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="table-container" style="padding: 16px 20px; margin-bottom: 24px; background: #f8fafc;">
        <form method="GET" action="{{ route('activity_logs.index') }}" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Action Name</label>
                <input type="text" name="action" value="{{ request('action') }}" placeholder="e.g. Order Placed, Stock..." style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">User / Staff Name</label>
                <input type="text" name="user_name" value="{{ request('user_name') }}" placeholder="Staff or customer name" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
            </div>

            <button type="submit" style="padding: 9px 20px; background: #2563eb; color: #fff; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">
                Filter Logs
            </button>
            <a href="{{ route('activity_logs.index') }}" style="padding: 9px 16px; background: #e2e8f0; color: #334155; border-radius: 6px; font-weight: 600; text-decoration: none;">
                Reset
            </a>
        </form>
    </div>

    <!-- Activity Log Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Timestamp</th>
                    <th>User / Actor</th>
                    <th>Action</th>
                    <th>Affected Entity</th>
                    <th>Details / Log Message</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>#{{ $log->id }}</td>
                        <td style="white-space: nowrap; font-size: 13px; color: #64748b;">
                            {{ $log->created_at->format('M d, Y h:i:s A') }}
                        </td>
                        <td>
                            <strong>{{ $log->user_name ?? ($log->user->name ?? 'System Event') }}</strong>
                            @if($log->user && $log->user->role)
                                <div style="font-size: 11px; color: #6b7280;">({{ ucfirst(str_replace('_', ' ', $log->user->role)) }})</div>
                            @endif
                        </td>
                        <td>
                            <span style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; background: #e0f2fe; color: #0369a1;">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td>
                            @if($log->model_type)
                                <span style="font-weight: 600; color: #334155;">{{ $log->model_type }} #{{ $log->model_id }}</span>
                            @else
                                <span style="color: #94a3b8;">—</span>
                            @endif
                        </td>
                        <td style="max-width: 450px; font-size: 13px; color: #1e293b;">
                            {{ $log->details }}
                        </td>
                        <td>
                            <code>{{ $log->ip_address ?? '127.0.0.1' }}</code>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-message">No activity logs recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection
