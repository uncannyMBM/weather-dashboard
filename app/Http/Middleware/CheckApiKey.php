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
        if (!$request->has('api_key') || !$request->has('user_name')) {
            if ($request->ajax()) {
                return response()->json('Unauthenticated', 401);
            } else {
                abort(401);
            }
        }

        $user = User::where(['email' => $request->user_name, 'api_token' => $request->api_key])->first();
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
