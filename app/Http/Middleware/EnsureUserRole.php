<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role->value !== $role) {
            $message = 'You do not have permission to access that area.';

            if ($user->isStaff()) {
                return redirect()->route('admin.dashboard')->with('status', $message);
            }

            return redirect()->route('student.dashboard')->with('status', $message);
        }

        return $next($request);
    }
}
