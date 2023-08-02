<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 8/4/2020
 * Time: 3:47 PM
 */

namespace App\Http\Middleware;

use Closure;

class VerifyAPIKey
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $api_keys = array('f06ff43be382');
        if ($request->header('ApiKey')) {
            $api_key = $request->header('ApiKey');
            if (in_array($api_key, $api_keys)) {
                return $next($request);
            } else {
                return 'API key is not valid';
            }
        }
        return 'Authorization failed';
    }
}