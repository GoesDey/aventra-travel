<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAgent
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->role !== 'agent' || $request->user()->status !== 'active') {
            abort(403, __('Access denied. This page is restricted to active Partner Agents.'));
        }

        return $next($request);
    }
}
