<?php

namespace App\Http\Controllers\AmergencyButton;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Pasien;
use App\Model\KehamilanSaatIni;
use App\Model\EB\PasienEB;
use App\Model\EB\PasienEmergency;
use App\Model\EB\RiwayatPasienEB;
use DB;
use App\Http\Controllers\MasterController;

class EmergencyButtonControllerDua extends Controller
{
  
    public function getDataPasienEB(Request $req)
    {
        $data =  RiwayatPasienEB::where("aktif", "1")
                    ->where("phone","like","%$req->phone%")
                    ->orderBy("created_at","desc")
                    ->get();
        return response()->json($data);
    }

 

   
    

}
