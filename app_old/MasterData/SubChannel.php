<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SubChannel extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_scnl';
    private $currentUser;
    protected $connection = '';

    public function __construct()
    {
        if (Auth::user() != null) {
            $this->currentUser = Auth::user();
            $this->connection = Auth::user()->country()->cont_conn;
        }
    }
    public function headings(): array
    {
        return ['sub_channel_name', 'sub_channel_code', 'channel_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;
        $channel_id = Channel::on($this->connection)->where(['chnl_code' => $value->channel_code])->first();

        $subChannel = new SubChannel();
        $subChannel->setConnection($this->connection);
        $subChannel->scnl_name = $value->sub_channel_name;
        $subChannel->scnl_code = $value->sub_channel_code;
        $subChannel->chnl_id = $channel_id->id;
        $subChannel->lfcl_id = 1;
        $subChannel->cont_id = $this->currentUser->country()->id;
        $subChannel->aemp_iusr = $this->currentUser->employee()->id;
        $subChannel->aemp_eusr = $this->currentUser->employee()->id;
        $subChannel->save();

    }

    public function channel()
    {
        return Channel::on($this->connection)->find($this->chnl_id);
    }

}
