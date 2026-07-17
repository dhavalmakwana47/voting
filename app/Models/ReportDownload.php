<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportDownload extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'resolution_id',
        'report_type',
        'report_name',
        'status',
        'progress',
        'file_path',
        'error_message',
    ];

    /**
     * Get the user who requested the download.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the resolution (report context).
     */
    public function resolution()
    {
        return $this->belongsTo(Resolution::class);
    }
}
