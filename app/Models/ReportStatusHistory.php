<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportStatusHistory extends Model
{
    protected $fillable = [
        'report_id',
        'changed_by',
        'old_status',
        'new_status'
    ];
}
