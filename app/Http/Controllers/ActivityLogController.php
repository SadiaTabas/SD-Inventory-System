<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('action')) {
            $query->where('action', 'like', "%{$request->action}%");
        }

        if ($request->filled('user_name')) {
            $query->where('user_name', 'like', "%{$request->user_name}%");
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('activity_logs.index', compact('logs'));
    }
}
