<?php

namespace App\Http\Controllers\AmergencyButton;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Pasien;
use App\Model\KehamilanSaatIni;
use App\Model\EB\PasienEB;
use App\Model\EB\PasienEmergency;
use DB;
use App\Http\Controllers\MasterController;

class EmergencyButtonController extends Controller
{
  
    public function getDataPasienRev(Request $req)
    {
        $data = DB::table('pasien_m as ps')->where("aktif","1");

        if(isset($req->nama)){
            $data = $data->whereRaw("LOWER(ps.nama) like '%".$req->nama."%'");
        }        
        
        if(isset($req->nobuku)){
            $data = $data->whereRaw(" LOWER(ps.nobuku) like '%".$req->nobuku."%'");
        }
        $data = $data->limit(10)->orderBy("ps.nama")->get();
        
        return response()->json($data);
    }


 


    public function savePasienNewEB( Request $req )
    {
        try {
            $newId = MasterController::Random();
           
            $save = PasienEB::firstOrNew(['phone' =>  $req['phone']]);

            $save->id = $newId;
            $save->nama = $req['nama'];
            $save->alamat = $req['alamat'];
            $save->jeniskelamin = $req['jeniskelamin'];
            $save->lokasiterakhir = $req['lokasiterakhir'];
            $save->uid = $req['uid'];
            $save->phone = $req['phone'];
           
            $save->save();

            $status = $save ? 1:0;
            $err = "";

        } catch (\Exception $e) {
            $status =0;
            $err = "[".$e->getMessage()."]";
        }
    
        return response()->json([
            "msg"=> $status ==0 ? "Gagal simpan data...".$err :"Suksess .",
            "sts" =>$status
        ]);
    }


    public function requestEmergency( Request $req )
    {
        try {
            $newId = MasterController::Random();
           
            $save = PasienEmergency::firstOrNew(['uid' =>  $req['uid']]);

            $save->id = $newId;
            $save->nama = $req['nama'];
            $save->uid = $req['uid'];
            // $save->jeniskelamin = $req['jeniskelamin'];
            $save->lat = $req['lat'];
            $save->long = $req['long'];
            $save->uid = $req['uid'];
            $save->phone = $req['phone'];
            $save->status ='rq';
           
            $save->save();

            $status = $save ? 1:0;
            $err = "";

        } catch (\Exception $e) {
            $status =0;
            $err = "[".$e->getMessage()."]";
        }
    
        return response()->json([
            "msg"=> $status ==0 ? "Gagal simpan data...".$err :"Suksess .",
            "sts" =>$status
        ]);
    }

 

   
    

}
