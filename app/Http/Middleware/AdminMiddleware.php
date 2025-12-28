<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
{
    // 1. Vérifie si l'utilisateur est connecté
    // 2. Vérifie si son rôle est exactement 'admin'
    if (auth()->check() && auth()->user()->role === 'admin') {
        return $next($request);
    }

    // Si ce n'est pas un admin, on le renvoie à l'accueil
    return redirect('/')->with('error', "Accès réservé aux administrateurs.");
}
}
