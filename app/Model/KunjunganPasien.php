<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class KunjunganPasien extends Model
{
    protected $table ="kunjungan_t";
    public $timestamps = true;
    public $guarded =[];
}