<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        // if user subscribed and route not equal plans continue
        // else if user not subscribed and route equal plans continue
        // else return back
        if (($user->subscribed && !Route::is('plans')) || (!$user->subscribed && Route::is('plans')))
            return $next($request);
        else
            return redirect(route('dashboard'));

    }
}
