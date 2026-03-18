<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resolution extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'user_id',
        'company_id',
        'evsn_type',
        'start_date',
        'end_date',
        'member_file',
        'meeting_date',
        'approval_status',
        'sentemail_approval',
        'sentemail_reportuser',
        'is_active',
        'is_updated',
        'is_modifiable',
        'comment_mode',
        'voting_otp',
        'meeting_details'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'resolution_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'resolution_id');
    }
    public function option_votes()
    {
        return $this->hasMany(OptinonVoting::class, 'resolution_id');
    }

    public function resolution_details()
    {
        return $this->hasMany(ResolutionDetail::class, 'resolution_id');
    }
}
