<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 5/12/2019
 * Time: 10:09 AM
 */

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
class CheckStatus
{

    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if(Auth::check() && Auth::user()->lfcl_id != '1'){
            Auth::logout();
            return redirect('/login')->with('message', 'You are Inactive User');
        }
        return $response;
    }
}