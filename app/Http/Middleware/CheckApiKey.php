<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->has('api_key')) {
            if ($request->ajax()) {
                return response()->json('Unauthenticated', 401);
            } else {
                abort(401);
            }
        }

        $apiKey = Crypt::decrypt($request->api_key);
        $user = User::where('api_token', $apiKey)->first();
        if (!$user) {
            if ($request->ajax()) {
                return response()->json('Unauthenticated', 401);
            } else {
                abort(401);
            }
        }

        return $next($request);
    }
}
