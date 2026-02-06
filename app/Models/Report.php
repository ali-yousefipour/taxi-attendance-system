<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'current_reviewer_id',
        'title',
        'description',
        'status'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'current_reviewer_id');
    }

    public function notes()
    {
        return $this->hasMany(ReportNote::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(ReportStatusHistory::class);
    }
}
