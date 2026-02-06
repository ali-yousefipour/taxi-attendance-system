<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverWarning extends Model
{
    protected $fillable = ['driver_id','added_by','warning','debt'];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'added_by');
    }
}
