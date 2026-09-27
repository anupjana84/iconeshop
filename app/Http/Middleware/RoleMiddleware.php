<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check authentication
        if (!Auth::check()) {
            Log::warning('Unauthenticated access attempt', [
                'url' => $request->fullUrl(),
                'ip' => $request->ip()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login first.'
                ], 401);
            }

            return redirect()->route('login_page')->with('error', 'Please login first.');
        }

        $user = Auth::user();
        $userRole = $user->role ?? 'user';
        $userRole = $userRole === 'subdealer' ? 'seller' : $userRole;

        // Check role permission
        if (!in_array($userRole, $roles)) {
            Log::warning('Unauthorized access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $userRole,
                'required_roles' => $roles,
                'url' => $request->fullUrl()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You do not have permission to access this resource.',
                    'your_role' => $userRole,
                    'required_roles' => $roles
                ], 403);
            }

            if ($userRole === 'user') {
                return redirect()->route('userdashboard')->with('fail', 'You do not have permission to access that section.');
            } elseif ($userRole === 'salesman') {
                return redirect()->route('salesmandashboard')->with('fail', 'You do not have permission to access that section.');
            } elseif (in_array($userRole, ['seller', 'subdealer'])) {
                return redirect()->route('sellerdashboard')->with('fail', 'You do not have permission to access that section.');
            } elseif (in_array($userRole, ['admin', 'manager'])) {
                return redirect()->route('dashboard')->with('fail', 'You do not have permission to access that section.');
            }

            return redirect()->route('home')->with('fail', 'You are not authorized to access this page.');
        }

        return $next($request);
    }
}