<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Keluhan extends Model
{
    protected static function boot()
    {
        parent::boot();
        Keluhan::updating(function($model) {
            $model->modfied_by = \Auth::id();
        });
        Keluhan::creating(function($model) {
            $model->modfied_by = \Auth::id();
        });
    }

    protected $table ="keluhan_m";
    public $timestamps = true;
    public $guarded =[];   
}
