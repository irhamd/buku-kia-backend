<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Rujukan extends Model
{
    protected static function boot()
    {
        parent::boot();
        Rujukan::updating(function($model) {
            $model->modfied_by = \Auth::id();
        });
    }

 

   
 


    protected $table ="pasienrujuk_t";
    public $timestamps = true;
    public $guarded =[];   
}
