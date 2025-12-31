<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ipaddress',
        'action',
        'member_id',
        'resolution_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
    
    public function resolution()
    {
        return $this->belongsTo(Resolution::class, 'resolution_id');
    }
}
