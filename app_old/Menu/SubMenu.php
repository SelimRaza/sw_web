<?php

namespace App\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class SubMenu extends Model
{
    protected $table = 'tm_wsmn';
    /*protected $connection= 'local';
    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection=Auth::user()->country()->cont_conn;
    }*/
  /*  public function submenu()
    {
       // return $this->hasMany(SubMenu::class);
        return $this->belongsTo(SubMenu::class);
    }*/
    public function Menu() {

            return Menu::where('id',$this->menu_id)->first();

        }

    //
}
