<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    protected $fillable = ['name','code','description'];

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }
}
