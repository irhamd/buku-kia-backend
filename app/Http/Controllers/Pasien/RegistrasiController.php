<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Registrasi;
use App\Model\KehamilanSaatIni;
use App\Model\KunjunganPasien;
use DB;
use App\Http\Controllers\MasterController;


class RegistrasiController extends Controller
{
   

    public function saveRegistrasiPasien( Request $req )
    {
        try {

            $tgl = date('Y-m-d');

            $cekAda = Registrasi::select("id_pasien")
            ->where("id_pasien", $req['id_pasien'])
            ->where("tanggal","like","%$tgl%")
            ->count();

            if($cekAda > 0 ) {
                return response()->json([
                    "msg"=> "Pasien sudah registrasi hari ini ...!",
                    "sts" =>"0"
                ]);
            } 

            if( $req['id'] ==""){
                $newId = MasterController::Random();
                $s = new Registrasi();
                $s->id = $newId;
                $s->aktif ="1";

            }else{
                $s = Registrasi::find($req['id']);
            }

            $last = Registrasi::max("nokunjungan") + 1;
            $kunj = Registrasi::select("kunjunganke")->where("id_pasien", $req['id_pasien'])->orderBy("kunjunganke" ,"desc")->first();
            
            $s->nokunjungan = $last;
            $s->kunjunganke = $kunj ? $kunj->kunjunganke + 1 : 1;
            $s->id_pasien = $req['id_pasien'];
            $s->tanggal = date("Y-m-d H:i:s");
            $s->statuskeluar = "Pulang";
            $s->id_pegawai = \Auth::user()->id_pegawai;
            $s->id_unitkerja = \Auth::user()->id_unitkerja;
            
            $s->save();

            $status = $s ? 1:0;
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

    public function daftarRegistrasiPasien(Request $req)
    {
        $tglAkhir = $req['tglAkhir'];
        $tglAwal = $req['tglAwal'];
        
        $data = DB::table('pasienregistrasi_t as pr')
        ->join("pasien_m  as ps","ps.id","pr.id_pasien")
        ->select("pr.id as id_pr", "pr.aktif" , "pr.tanggal", "pr.nokunjungan", "pr.id_pasien", "pr.kunjunganke", "pr.statuskeluar", 
            "ps.nama", "ps.alamat", "ps.foto", "ps.nobuku", "ps.nik", "ps.tgllahir")
        ->where("pr.aktif","1")
        ->where("ps.aktif","1");
        // ->whereRaw("pr.tanggal between '$tglAwal' and '$tglAkhir'");
        
        if(isset($req->nama)){
            $data = $data->whereRaw("LOWER(ps.nama) like '%".$req->nama."%'");
        }        
        
        if(isset($req->nobuku) && $req->nobuku != ""){
            $data = $data->whereRaw(" LOWER(ps.nobuku) like '%".$req->nobuku."%'");
        }

        $data = $data->limit(30)->orderBy("pr.tanggal")->get();
        
        return response()->json($data);
    }


 

   
    

}
