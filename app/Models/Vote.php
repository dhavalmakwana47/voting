<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;
    protected $fillable = [
        'resolution_id',
        'member_id',
        'resolution_choice',
        'instr_comment',
        'voting_date',
        'ipaddress',
        'is_active',
        'created_by',
        'updated_by',
        'resolution_details_id'
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
}
