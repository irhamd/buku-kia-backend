<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Rujukan extends Model
{
    // protected static function boot()
    // {
    //     parent::boot();
    //     Customer::creating(function($model) {
    //         $model->status = 1;
    //     });
    // }


    // $userID;

    // public function __construct($userID){
    //     $user = \Auth::user();
    //     $this->userID = $user->getId();
    // }


    // public function saving($model)
    // {
    //     $model->modfied_by = $this->userID;
    // }

    // public function saved($model)
    // {
    //     $model->modfied_by = $this->userID;
    // }


    // public function updating($model)
    // {
    //     $model->modfied_by = $this->userID;
    // }

    // public function updated($model)
    // {
    //     $model->modfied_by = $this->userID;
    // }


    // public function creating($model)
    // {
    //     $model->created_by = $this->userID;
    // }

    // public function created($model)
    // {
    //     $model->created_by = $this->userID;
    // }


    protected $table ="pasienrujuk_t";
    public $timestamps = true;
    public $guarded =[];   
}
