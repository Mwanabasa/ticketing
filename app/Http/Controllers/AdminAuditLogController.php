<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::query()->with('user')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('q')) {
            $needle = $request->string('q')->trim();
            $query->where(function ($q) use ($needle): void {
                $q->where('model_type', 'like', '%'.$needle.'%')
                    ->orWhere('ip_address', 'like', '%'.$needle.'%');
                if (ctype_digit((string) $needle)) {
                    $q->orWhere('model_id', (int) $needle);
                }
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        $actions = AuditLog::query()->distinct()->orderBy('action')->pluck('action');

        return view('admin.audit-logs.index', compact('logs', 'actions'));
    }
}
