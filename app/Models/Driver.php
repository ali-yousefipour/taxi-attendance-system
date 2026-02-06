<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'name','national_id','line_id','phone','balance'
    ];

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function attendances()
    {
        return $this->hasMany(DriverAttendance::class);
    }
}
