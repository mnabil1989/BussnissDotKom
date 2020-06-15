<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndustrialZones extends Model
{
    public $timestamps = false;
    

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function factories()
    {
        return $this->hasMany(User::class)->where('type','factory');
    }

    public function companies()
    {
        return $this->hasMany(User::class)->where('type','company');
    }
}
