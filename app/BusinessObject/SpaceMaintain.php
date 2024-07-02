<?php

namespace App\BusinessObject;

use App\MasterData\Zone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SpaceMaintain extends Model implements WithHeadings, FromCollection
{
    protected $table = 'tm_spcm';
    protected $connection= '';
    protected $currentUser= '';

    protected $fillable = ['spcm_name', 'spcm_exdt'];

    public function __construct()
    {
        $this->currentUser = Auth::user();
        $this->connection = Auth::user()->country()->cont_conn;
    }


    const VALUE = 1;
    const FOC = 2;


    public function headings(): array
    {
        return ['zone_code', 'zone_name', 'quantity'];
    }

    public function collection()
    {
        return collect(Zone::on($this->connection)->where('lfcl_id', 1)->get(['zone_code', 'zone_name']));
    }

    public function showcases()
    {
        return $this->setConnection($this->connection)->hasMany(SpaceMaintainShowcase::class, 'spcm_id', 'id');
    }


    public function freeItems()
    {
        return $this->setConnection($this->connection)->hasMany(SpaceMaintainFreeItem::class, 'spcm_id', 'id');
    }


    public function freeAmounts()
    {
        return $this->setConnection($this->connection)->hasMany(SpaceMaintainFreeAmount::class, 'spcm_id', 'id');
    }



    public function zones()
    {
        return $this->setConnection($this->connection)->hasMany(SpaceZone::class, 'spcm_id', 'id');
    }



    public function sites()
    {
        return $this->setConnection($this->connection)->hasMany(SpaceSite::class, 'spcm_id', 'id');
    }

    public function sitesWithStatus()
    {
        return $this->setConnection($this->connection)->hasMany(SpaceSite::class, 'spcm_id', 'id')
            ->whereIn('lfcl_id', [1,2]);
    }



    public function foc()
    {
        return $this->setConnection($this->connection)->belongsTo(ItemMaster::class, 'spft_id', 'id');
    }



    public function saleGroup()
    {
        return $this->setConnection($this->connection)->belongsTo(SalesGroup::class, 'spcm_slgp', 'id');
    }
}
