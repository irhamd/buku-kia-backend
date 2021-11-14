<?php

namespace App\Http\Controllers\Rujukan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Keluhan;
use App\Model\KunjunganPasien;
use DB;
use App\Http\Controllers\MasterController;

class KeluhanController extends Controller
{
     
    public function updateKeluhanRujuk(Request $req)
    {
       $cek = Keluhan::find($req['id'])
        ->update([
            // "aktif"=>$req['aktif'], 
            "isrujuk"=>$req["isrujuk"]
        ]);
        return response()->json([
            "sts"=>$cek ? 1:0,
        ]);
    }
 
     
    // public function getKeluhan(Request $req)
    // {
    //    $cek = Keluhan::all();
    //     return response()->json([
    //         "data"=>$cek ? 1:0,
    //     ]);
    // }
 


}
