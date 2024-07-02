<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\MasterData\Employee;
class HelperController extends Controller
{
   
    public function sayHello(){
        return "Kalo Ruper jala re sokhi";
       // return view('report.hello');
    }
   
}
