<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptinonVoting extends Model
{
    use HasFactory;
    protected $fillable = [
        'resolution_id',
        'resolution_details_id',
        'member_id',
        'option_id',
        'voting_date',
        'ipaddress',
        'instr_comment'
    ];
    public function resolution(){
        return $this->belongsTo(Resolution::class, 'resolution_id', 'id');
    }
    public function resolution_details(){
        return $this->belongsTo(ResolutionDetail::class, 'resolution_details_id', 'id');
    }

    public function member(){
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    public function selected_option(){
        return $this->belongsTo(OptinonVotingDetail::class, 'option_id', 'id');
    }
}
