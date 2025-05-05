<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RoleMiddleware
{
public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in!');
        }

        // Normalize role names for comparison
        $userRole = strtolower(Auth::user()->role->label);
        $allowedRoles = array_map('strtolower', $roles);

        // Check if user has any of the allowed roles
        if (!in_array($userRole, $allowedRoles)) {
            return redirect()->route('fab_chain.index')->with('error', 'You are not authorized to access this page!');
        }

        return $next($request);
    }
}