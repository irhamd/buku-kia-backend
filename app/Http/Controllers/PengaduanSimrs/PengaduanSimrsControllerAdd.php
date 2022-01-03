<?php

namespace App\Http\Controllers\PengaduanSimrs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\PengaduanSimrs\PengaduanSimrs;
use App\Model\KehamilanSaatIni;
use App\Model\KunjunganPasien;
use App\Http\Controllers\Notification\NotifikasiController;
use DB;
use App\Http\Controllers\MasterController;

class PengaduanSimrsControllerAdd extends Controller
{
    public function detailTugas( Request $req ){
        $detail = PengaduanSimrs::find($req['id']);
        return response()->json( $detail );
    }
 

 

   
    

}
