<?php

namespace App\Model\Arsip;

use Illuminate\Database\Eloquent\Model;

class ArsipBerkas extends Model
{

    protected static function boot()
    {
        parent::boot();
        ArsipBerkas::updating(function($model) {
            $model->id_user = \Auth::id();
        });
        ArsipBerkas::creating(function($model) {
            $model->id_user = \Auth::id();
        });
    }


    protected $table ="arsip_arsipberkasproyek_t";
    public $timestamps = true;
    public $guarded =[];


}