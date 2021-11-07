<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    protected $table ="pasien_m";
    public $timestamps = true;
    public $guarded =[];
}
