<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResolutionDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'resolution_id',
        'add_by',
        'resolution_number',
        'description',
        'file_name',
        'option_type',
        'skip',
        'index',
        'min',
        'max'
    ];

    public function resolution()
    {
        return $this->belongsTo(Resolution::class, 'resolution_id', 'id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'resolution_details_id');
    }
    public function option_votes()
    {
        return $this->hasMAny(OptinonVoting::class, 'resolution_details_id');
    }

    public function labels(){
        return $this->hasMany(OptinonVotingDetail::class, 'resolution_details_id');

    }
}
