<?php

namespace App\Http\Middleware;

use App\MasterData\Country;
use App\MasterData\Employee;
use Closure;
use Illuminate\Contracts\Auth\Guard;

class TimeZone
{

    protected $user;

    public function __construct(Guard $auth)
    {
        $this->user = $auth->user();
    }

    public function handle($request, Closure $next)
    {
        $this->setTimeZone($request);
        return $this->addTimeZoneCookie($request, $next($request));
    }

    public function setTimeZone($request)
    {
        if ($this->user) {
            return date_default_timezone_set($this->user->country()->cont_tzon);
        }
        /*    if ($request->emp_id) {
                $employee = Employee::findorfail($request->emp_id);
                return date_default_timezone_set($employee->country()->cont_tzon);
            }*/
        if (isset($request->country_id)) {
            $country = (new Country())->country($request->country_id);
            if ($country){
                return date_default_timezone_set($country->cont_tzon);
            }
        }

        $timeZone = $request->cookie('Asia/Dhaka');

        if ($timeZone) {
            return date_default_timezone_set($timeZone);
        }
        return;
    }

    public function addTimeZoneCookie($request, $response)
    {
        if (!$request->cookie('cont_tzon') && !is_null($this->user)) {
            // return $response->withCookie(cookie('time_zone', $this->user->timezone, 120));
        }
        return $response;
    }
}
