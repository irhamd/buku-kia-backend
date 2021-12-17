<?php

namespace App\Model\PengaduanSimrs;

use Illuminate\Database\Eloquent\Model;

class PengaduanSimrs extends Model
{
    protected static function boot()
    {
        parent::boot();
        PengaduanSimrs::updating(function($model) {
            $model->modified_by = \Auth::id();
        });
        PengaduanSimrs::creating(function($model) {
            $model->modified_by = \Auth::id();
        });
    }

    protected $table ="pgd_pengaduan_t";
    public $timestamps = true;
    public $guarded =[];
}