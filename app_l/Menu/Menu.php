<?php

namespace App\Menu;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'tm_wmnu';
    /*protected $connection= 'local';
    public function __construct()
    {
        $this->currentUser = Auth::user();
        if (Auth::user()!=null){
            $this->connection=Auth::user()->country()->cont_conn;
        }
    }*/

    public function get_user_submenu(){
       return $sub_menus = UserMenu::where(['tl_wsmu.users_id' => Auth::user()->id, 'tl_wsmu.cont_id' => Auth::user()->country()->id])->join('tm_wsmn','tm_wsmn.id','tl_wsmu.wsmn_id')->where('tl_wsmu.wsmu_vsbl', 1)->where('tm_wsmn.wmnu_id', $this->id)->orderBy('tm_wsmn.wsmn_oseq', 'ASC')->get();
    }
}
