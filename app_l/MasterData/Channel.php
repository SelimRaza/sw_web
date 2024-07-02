<?php

namespace App\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Channel extends Model implements WithHeadings, ToModel, WithHeadingRow
{
    protected $table = 'tm_chnl';
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
        return ['channel_name', 'channel_code'];
    }

    public function model(array $row)
    {
        $value = (object)$row;

        $channel = new Channel();
        $channel->setConnection($this->connection);
        $channel->chnl_name = $value->channel_name;
        $channel->chnl_code = $value->channel_code;
        $channel->lfcl_id = 1;
        $channel->cont_id = $this->currentUser->country()->id;
        $channel->aemp_iusr = $this->currentUser->employee()->id;
        $channel->aemp_eusr = $this->currentUser->employee()->id;
        $channel->save();

    }


}
