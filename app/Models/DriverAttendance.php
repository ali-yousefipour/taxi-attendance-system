<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverAttendance extends Model
{
    protected $fillable = [
        'driver_id','date','check_in','check_out','debt','note'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
