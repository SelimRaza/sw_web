<?php

namespace App\Menu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class UserMenu extends Model
{
    protected $table = 'tl_wsmu';
  /*  protected $connection= 'local';
    public function __construct()
    {
        $this->connection=Auth::user()->country()->cont_conn;
    }*/
    public function submenu() {
        return SubMenu::where('id',$this->sub_menu_id)->first();


//        return $this->hasMany('App\Menu\SubMenu', 'id',
//            'sub_menu_id');
    }


}
