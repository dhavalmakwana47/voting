<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCompanyMap extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'company_id',
        'add_by',
        'update_by',
        'is_active',
    ];
}
