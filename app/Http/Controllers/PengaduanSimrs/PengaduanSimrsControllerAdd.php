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
    public function detailTugas12( Request $req ){
        $detail = PengaduanSimrs::find($req['id']);
        return response()->json( $detail );
    }

    public function detailTugas( Request $req ){
        $detail = DB::table("pgd_pengaduan_t as pdg")
        ->leftjoin("pegawai_m as pg","pg.id","=", "pdg.assignto")
        ->select("pdg.*", "pg.namapegawai")
        ->where("pdg.id", "=", $req['id'])
        ->first();

        return response()->json( $detail );
    }

    public function getpetugas(){
        $data = DB::table("pegawai_m as pg")
        ->join( 'users as us', 'us.id_pegawai', 'pg.id' )
        ->select("pg.*", "us.role")
        ->get();

        return response()->json( $data );
    }

 

 

   
    

}
