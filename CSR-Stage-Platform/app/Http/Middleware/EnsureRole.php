<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->role !== $role) {
            return $this->redirectByRole($request->user());
        }

        return $next($request);
    }

    private function redirectByRole($user): Response
    {
        return match ($user->role) {
            'plant' => redirect()->route('site.dashboard'),
            'corporate' => redirect()->route('corporate.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
