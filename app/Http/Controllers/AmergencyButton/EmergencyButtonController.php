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

class EmergencyButtonController extends Controller
{
  
    public function getDataPasienEB(Request $req)
    {
        $data = DB::table('eb_pasien_m as ps');

        if(isset($req->nama)){
            $data = $data->whereRaw("LOWER(ps.nama) like '%".$req->nama."%'");
        }        
        
        if(isset($req->alamat)){
            $data = $data->whereRaw(" LOWER(ps.alamat) like '%".$req->alamat."%'");
        }
        if(isset($req->phone)){
            $data = $data->whereRaw(" LOWER(ps.phone) like '%".$req->phone."%'");
        }
        $data = $data->limit(100)->orderBy("ps.nama")->get();
        
        return response()->json($data);
    }


 


    public function savePasienNewEB( Request $req )
    {
        try {
            $newId = MasterController::Random();
           
            if($req['status'] != "cl" ) { 

                $aktif = true;

                if(isset($req->block) && isset($req->block) !=""){
                   $aktif = false;
                }

                $save = PasienEB::firstOrNew(['phone' =>  $req['phone']]);
                $save->id = $newId;
                $save->aktif = $aktif;
                $save->nama = $req['nama'];
                $save->alamat = $req['alamat'];
                $save->jeniskelamin = $req['jeniskelamin'];
                $save->lokasiterakhir = $req['lokasiterakhir'];
                $save->uid = $req['uid'];
                $save->phone = $req['phone'];
            
                $save->save();
            }
            $save = RiwayatPasienEB::firstOrNew(['uid' =>  $req['uid']]);

            $save->id = $newId;
            $save->aktif = "1";
            $save->uid = $req['uid'];
            $save->phone = $req['phone'];
            $save->lat = $req['lat'];
            $save->long = $req['long'];
            $save->status = $req['status'];
            $save->alasan = $req['alasan'];
            $tgltime = date('Y-m-d H:i:s');

            if($req['status'] == "cm" ) { $save->waktu_commit = $tgltime;}
            if($req['status'] == "rj" ) { $save->waktu_reject = $tgltime;}

            $save->isambulance = $req['isambulance'];
           
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
    public function getWaktuTekan( Request $req )
    {
        try {
            $newId = MasterController::Random();
           
            $save = RiwayatPasienEB::firstOrNew(['uid' =>  $req['uid']]);

            $save->id = $newId;
            $save->aktif = "1";
            $save->uid = $req['uid'];
            $save->phone = $req['phone'];
            $save->status = "cl";
            $save->waktu_tekan = $req->waktutekan;
            $tgltime = date('Y-m-d H:i:s');
            $save->waktu_fu = $tgltime; 
           
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

    public function getDataDashboard(Request $req)
    {
        $data =  RiwayatPasienEB::select('status', DB::raw('count(status) as total'))
                    ->groupBy('status')
                    ->where("aktif", "1")
                    ->whereIn("status", ["cm", "rj"])
                    ->get();

        $ambulance = RiwayatPasienEB::where("isambulance","1")->count();
        $pasienanulir = PasienEB::where("aktif", "0")->count();
        
        return response()->json(
            [ 
                "data"=> $data, 
                "ambulance"=> $ambulance,
                "pasienanulir"=> $pasienanulir,
        ]);
    }

   
    public function getRiwayatLengkapPasien(Request $req)
    {
        $data = DB::table("eb_riwayatpasien_t as ebr")
            ->select(
                "ebr.created_at", "ebr.id", "ebr.uid", "ebr.lat", "ebr.long", "ebr.phone", "ebr.isambulance", "ebr.id_user", "ebr.waktu_tekan", "ebr.waktu_fu","ebr.waktu_commit",
                "ps.nama", "ps.alamat", "ps.jeniskelamin", "ps.id_kecamatan",
                "st.status", "st.kode")
            ->leftJoin("eb_pasien_m as ps","ps.phone","=","ebr.phone")
            ->join("eb_status_m as st","st.kode","=","ebr.status")
            ->where("ebr.aktif", "1")
            ->whereIn("ebr.status", ["cm", "rj"])
            ->orderBy("ebr.created_at","desc")
            ->limit(150);

            
        if(isset($req->tglAwal) && $req->tglAwal !=""){
            $data = $data->where("ebr.waktu_tekan",">=",$req->tglAwal);
        }
            
        if(isset($req->tglAkhir) && $req->tglAkhir !=""){
            $data = $data->where("ebr.waktu_tekan","<=",$req->tglAkhir);
        }

        if(isset($req->nama) && $req->nama !=""){
            $data = $data->where("ps.nama","like","%$req->nama%");
        }

        if(isset($req->tolak) && $req->tolak =="true" ){
            $data = $data->Where("st.kode","rj");
        }

        if(isset($req->ambu) && $req->ambu =="true"){
            $data = $data->Where("ebr.isambulance","1");
        }

        
        if(isset($req->phone) && $req->phone !=""){
            $phone = (int)$req->phone;
            $data = $data->where("ebr.phone","like","%$phone%");
        }

        $data = $data->get();

        return response()->json(
            [ 
                "data"=> $data
        ]);
    }
    public function cekDataPasienEB(Request $req)
    {
        $data = PasienEB::where("phone","like", "%$req->phone%")->first();

        $riwayat = DB::table("eb_riwayatpasien_t as ebr")
        ->select(
            "ebr.created_at", "ebr.id", "ebr.uid", "ebr.lat", "ebr.long", "ebr.phone", "ebr.isambulance", "ebr.id_user", "ebr.waktu_tekan", "ebr.waktu_fu","ebr.waktu_commit",
            "ps.nama", "ps.alamat", "ps.jeniskelamin", "ps.id_kecamatan",
            "st.status", "st.kode")
        ->leftJoin("eb_pasien_m as ps","ps.phone","=","ebr.phone")
        ->join("eb_status_m as st","st.kode","=","ebr.status")
        ->where("ebr.aktif", "1")
        ->where("ebr.phone","like", "%$req->phone%")
        ->whereIn("ebr.status", ["cm", "rj"])
        ->orderBy("ebr.created_at","desc")
        ->get();

        return response()->json([
            "data" => $data,
            "riwayat" => $riwayat,
        ]);
    }

   
    

}
