<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptinonVotingDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'resolution_id',
        'resolution_details_id',
        'label',
        'image'
    ];
    public function option_votes()
    {
        return $this->hasMany(OptinonVoting::class, 'option_id');
    }
    
    
}
