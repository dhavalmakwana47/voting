<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory,SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'email',
        'share',
        'phone',
        'user_name',
        'password',
        'otp',
        'voting_otp',
        'session_id',
        'approval_status',
        'email_sent',
        'reason',
        'sent_date',
        'delivery_date',
        'add_by',
        'resolution_id',
        'company_id',
        'is_active'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function resolution()
    {
        return $this->belongsTo(Resolution::class, 'resolution_id', 'id');
    }

    public function vote()
    {
        return $this->hasOne(Vote::class,  'member_id');
    }
    
    public function option_votes()
    {
        return $this->hasMany(OptinonVoting::class, 'member_id');
    }

    public function res_vote()
    {
        return $this->hasMany(Vote::class,  'member_id');
    }
}
