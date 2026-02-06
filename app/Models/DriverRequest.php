<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverRequest extends Model
{
    protected $fillable = [
        'line_id','name','national_id','requested_by','status'
    ];

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class,'requested_by');
    }
}
