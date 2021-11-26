<?php

namespace App\Model\EB;

use Illuminate\Database\Eloquent\Model;

class PasienEB extends Model
{

    protected static function boot()
    {
        parent::boot();
        PasienEB::updating(function($model) {
            $model->id_user = \Auth::id();
        });
        PasienEB::creating(function($model) {
            $model->id_user = \Auth::id();
        });
    }

    protected $table ="eb_pasien_m";
    public $timestamps = true;
    public $guarded =[];


}