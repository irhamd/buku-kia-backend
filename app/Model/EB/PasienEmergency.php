<?php

namespace App\Model\EB;

use Illuminate\Database\Eloquent\Model;

class PasienEmergency extends Model
{

    protected $table ="eb_pasienemergency_t";
    public $timestamps = true;
    public $guarded =[];
}