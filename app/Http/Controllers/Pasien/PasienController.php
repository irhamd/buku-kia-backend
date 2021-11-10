<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Pasien;
use App\Model\KehamilanSaatIni;
use App\Model\KunjunganPasien;
use DB;

class PasienController extends Controller
{
    public function getDataPasien(Request $req)
    {
        $data = DB::table('pasien_m as ps')->where("aktif","1")
            ->selectRaw("ps. *, 
                ( SELECT kunjunganke  from kunjungan_t WHERE id_pasien = ps.id ORDER BY kunjunganke desc limit 1),
                ( SELECT hpht  from kehamilansaatini_t WHERE id_pasien = ps.id ORDER BY created_at desc limit 1 )
            ");
            // SELECT pr.*, ps.* from pasienregistrasi_t as pr
            // join pasien_m as ps on ps.id = pr.id_pasien

        if(isset($req->nama)){
            $data = $data->whereRaw("LOWER(ps.nama) like '%".$req->nama."%'");
        }        
        
        if(isset($req->nobuku)){
            $data = $data->whereRaw(" LOWER(ps.nobuku) like '%".$req->nobuku."%'");
        }
        $data = $data->limit(30)->orderBy("ps.nama")->get();
        
        return response()->json($data);
    }
    public function getDataPasienRev(Request $req)
    {
        $data = DB::table('pasien_m as ps')->where("aktif","1");

        if(isset($req->nama)){
            $data = $data->whereRaw("LOWER(ps.nama) like '%".$req->nama."%'");
        }        
        
        if(isset($req->nobuku)){
            $data = $data->whereRaw(" LOWER(ps.nobuku) like '%".$req->nobuku."%'");
        }
        $data = $data->limit(30)->orderBy("ps.nama")->get();
        
        return response()->json($data);
    }


 


    public function saveDataPasien( Request $req )
    {
        try {
            if( $req['id'] ==""){
                $save = new Pasien();
            }else{
                $save = Pasien::find($req['id']);
            }

            $save->nik = $req['nik'];
            $save->nama = $req['nama'];
            $save->aktif = 1;
            $save->alamat = $req['alamat'];
            $save->nobuku = $req['nobuku'];
            $save->foto = $req['foto'];
            $save->nojkn = $req['nojkn'];
            $save->faskestk1 = $req['faskestk1'];
            $save->faskesrujukan = $req['faskesrujukan'];
            $save->goldarah = $req['goldarah'];
            $save->tempatlahir = $req['tempatlahir'];
            $save->tgllahir = $req['tgllahir'];
            $save->nohp = $req['nohp'];
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
