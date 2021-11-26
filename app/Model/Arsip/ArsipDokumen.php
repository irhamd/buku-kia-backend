<?php

namespace App\Model\Arsip;

use Illuminate\Database\Eloquent\Model;

class ArsipDokumen extends Model
{

    protected static function boot()
    {
        parent::boot();
        ArsipDokumen::updating(function($model) {
            $model->id_user = \Auth::id();
        });
        ArsipDokumen::creating(function($model) {
            $model->id_user = \Auth::id();
        });
    }


    protected $table ="arsip_dokumen_t";
    public $timestamps = true;
    public $guarded =[];


}