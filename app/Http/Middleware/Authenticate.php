<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        $excludedPaths = [
            'login',
            'register',
        ];

        $currentPath = ltrim($request->path(), '/');

        // If user is NOT LOGGED and tries to open ANY other page than login/register -> redirect to login
        if (!Auth::check() && !in_array($currentPath, $excludedPaths)) {
            return redirect('/login');
        }

        // If user IS LOGGED and tries to open login/register pages -> redirect to home page
        if (Auth::check() && in_array($currentPath, $excludedPaths)) {
            return redirect('/dashboard'); // lub inna strona startowa
        }

        return $next($request);
    }
}