<?php

namespace App\Http\Controllers\Kunjungan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\Pasien\KehamilanController;
use App\Model\KunjunganPasien;
use DB;
use Carbon\Carbon;

use App\Http\Controllers\Rujukan\RujukanController;

class KunjunganController extends Controller
{
    public function saveKunjungan1( Request $req )
    {
        try {
            $newId = MasterController::Random();
            DB::beginTransaction();
            if( $req->id ==""){
                $save = new KunjunganPasien();
                $save->id = $newId;
                $save->aktif = 1;
            }else{
                $save = KunjunganPasien::find($req->id);
                DB::table("listkeluhan_t")->where("id_kunjungan","=",$req->id)->delete();
                DB::table("listhasillab_t")->where("id_kunjungan","=",$req->id)->delete();
            }
            // id	aktif	tanggal	umurkehamilan	beratbadan	tekanandarah	tinggifundus	imunisasi	tablettambahdarah	analisa	created_at	updated_at
            // $save->umurkehamilan = $req->umurkehamilan;
            $save->tanggal = $req->tanggal;
            $save->beratbadan = $req->beratbadan;
            $save->tekanandarah = $req->tekanandarah;
            $save->tinggifundus = $req->tinggifundus;
            $save->imunisasi = $req->imunisasi;
            $save->analisa = $req->analisa;
            $save->id_letakjanin = $req->letakjanin;
            $save->tablettambahdarah = $req->tablettambahdarah;
            $save->id_pasien = $req->id_pasien;
            $save->kunjunganke = $req->kunjunganke;
            $save->lila = $req->lila;
            $save->tatalaksana = $req->tatalaksana;
            $save->masukpanggul = $req->masukpanggul;
            $save->konseling = $req->konseling;
            $save->id_pegawai = \Auth::user()->id_pegawai;
            $save->save();

            $keluhan = $req->listKeluhan;
            if(isset($keluhan) && $keluhan !="undefined" ){
                $lists = explode(",",$keluhan);
                foreach ($lists as $list){
                    $keluhan = DB::table('listkeluhan_t')->insert([ 
                        "id"=> MasterController::Random(),
                        "id_keluhan"=> $list,
                        "aktif"=> 1,
                        "id_kunjungan" =>$newId
                    ]);
                }
            }

            $hasillab = $req->listHasilLab;
            if(isset($hasillab) && $hasillab !="undefined" ){
                $lists = explode(",",$hasillab);
                foreach ($lists as $list){
                    $keluhan = DB::table('listhasillab_t')->insert([ 
                        "id"=> MasterController::Random(),
                        "id_hasillab"=> $list,
                        "aktif"=> 1,
                        "id_kunjungan" =>$newId,
                        "hb" =>$req->hb
                    ]);
                }
            }
            $status = $save ? 1:0;
            $err = "";
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $status =0;
            $err = "[".$e->getMessage()."]";
        }
    
        return response()->json([
            "msg"=> $status ==0 ? "Gagal simpan data...".$err :"Suksess .",
            "sts" =>$status,
            "id_kunjungan" =>$newId,
            "keluhan" =>$keluhan,
            "hasillab" =>$hasillab,
        ]);
    } 

    public function saveKunjungan( Request $req )
    {
        try {
            
            // $cek = DB::table('kunjungan_t')->where("id_pasien","=",$req->id_pasien)
            // ->where("kunjunganke","=","0")
            // ->first();
            
            DB::beginTransaction();
            $newId = "";

            if( $req->id == ""){
                $newId = MasterController::Random();
                $save = new KunjunganPasien();
                $save->id = $newId;
                $save->aktif = 1;
            }else{
                $newId = $req->id;
                $save = KunjunganPasien::find($newId);
                // DB::table("listkeluhan_t")->where("id_kunjungan","=",$req->id)->delete();
                // DB::table("listhasillab_t")->where("id_kunjungan","=",$req->id)->delete();
            }
            // id	aktif	tanggal	umurkehamilan	beratbadan	tekanandarah	tinggifundus	imunisasi	tablettambahdarah	analisa	created_at	updated_at
            
            // $umurK = KehamilanController::getUmurKehamilan($req->id_pasien, $req->tanggal);

            // $save->umurkehamilan =  $umurK[0];
            $save->umurkehamilan1 =  $req->umurkehamilan1;

            $save->tanggal = $req->tanggal;
            $save->beratbadan = $req->beratbadan;
            $save->tekanandarah = $req->tekanandarah;
            $save->tinggifundus = $req->tinggifundus;
            $save->imunisasi = $req->imunisasi;
            $save->analisa = $req->analisa;
            $save->id_pasienregistrasi = $req->id_pasienregistrasi;
            $save->id_letakjanin = $req->letakjanin;
            $save->tablettambahdarah = $req->tablettambahdarah;
            $save->id_pasien = $req->id_pasien;
            $save->kunjunganke = $req->kunjunganke;
            $save->lila = $req->lila;
            $save->tatalaksana = $req->tatalaksana;
            $save->masukpanggul = $req->masukpanggul;
            $save->vaksin1 = $req->vaksin1;
            $save->vaksin2 = $req->vaksin2;
            $save->vaksin3 = $req->vaksin3;
            
            $save->timbang = $req->timbang;
            $save->linkarlengan = $req->linkarlengan;
            $save->tinggirahim = $req->tinggirahim;
            $save->skreningdokter = $req->skreningdokter;
            $save->ppia = $req->ppia;

            
            $save->beratjanin = $req->beratjanin;
            $save->konseling = $req->konseling;
            $save->id_pegawai = \Auth::user()->id_pegawai;
            $save->save();

            $keluhan = $req->listKeluhan;
            if(isset($keluhan) && $keluhan !="undefined" ){
                $lists = explode(",",$keluhan);
                foreach ($lists as $list){
                    $keluhan = DB::table('listkeluhan_t')->insert([ 
                        "id"=> MasterController::Random(),
                        "id_keluhan"=> $list,
                        "aktif"=> 1,
                        "id_kunjungan" =>$newId
                    ]);
                }
            }

            $hasillab = $req->listHasilLab;
            if(isset($hasillab) && $hasillab !="undefined" ){
                $lists = explode(",",$hasillab);
                foreach ($lists as $list){
                    $keluhan = DB::table('listhasillab_t')->insert([ 
                        "id"=> MasterController::Random(),
                        "id_hasillab"=> $list,
                        "aktif"=> 1,
                        "id_kunjungan" =>$newId,
                        "hb" =>$req->hb
                    ]);
                }
            }
            $status = $save ? 1:0;
            $err = "";
            DB::commit();

         

        } catch (\Exception $e) {
            DB::rollBack();
            $status =0;
            $err = "[".$e->getMessage()."]";
        }

        if($status === 1 ){
            DB::table('jadwalkunjungan_t')
            ->where('id_pasien',$req->id_pasien)
            ->where('kunjunganke',$req->kunjunganke)
            ->update([
                "id_kunjungan" =>$newId,
                "isdatang"=>"1"
            ]);
        }
    
        return response()->json([
            "msg"=> $status ==0 ? "Gagal simpan data...".$err :"Suksess .",
            "sts" =>$status,
            "id_kunjungan" =>$newId,
            // "cekrujuk" =>$cekRujuk,
            // "umur" => $umurK,
            // "keluhan" =>$keluhan,
            // "hasillab" =>$hasillab,
        ]);
    } 

    
    public function detailKunjungan( $id_pasien )
    {
        $kunjungan = KunjunganPasien::where("id_pasien",$id_pasien)
        ->select("kunjunganke")
        ->orderBy("kunjunganke","desc")
        ->first();

        $umurkehamilan =DB::table("kehamilansaatini_t as khm")
        ->selectRaw("AGE(khm.hpht,'".date('Y-m-d')."') as umurkehamilan")
        ->where("id_pasien","=",$id_pasien)
        ->where("aktif","=",1)
        ->first();

        $a = $umurkehamilan->umurkehamilan;
         $umur = str_replace('days','Hari',$a);
         $umur = str_replace('mons','Bulan',$umur);
         $umur = str_replace('years','Tahun',$umur);
         $umur = str_replace('-','',$umur);
         $umur = str_replace('00:00:00','0 Hari',$umur);

        return response()->json([
            "kunjunganke"=>$kunjungan ? $kunjungan->kunjunganke +1 :1,
            "umurkehamilan" => $umurkehamilan ? $umur : "0 Hari"
        ]);
    }

    public function getKunjunganByPasien1( Request $req )
    {
        $data = DB::select("select  kjt.* from kunjungan_t as kjt 
        where kjt.id_pasien =".$req->id_pasien);

        $datas = [];
        foreach ($data as $item) {
            $keluhan = DB::table("listkeluhan_t as lkel")
            ->join("keluhan_m as kel","kel.id = lkel.id_keluhan")
            ->select("kel.keluhan")
            ->where("lkel","=",$item->id)
            ->get();
            array_push($datas, $keluhan);
        }

        // dd($datas);

        return response()->json([
            "data"=> $datas,

        ]);
    }

    public function getKunjunganByPasien(Request $req)
    {
        $data = DB::table("kunjungan_t as kj")
        ->join("pegawai_m as pg","pg.id","=","kj.id_pegawai")
        ->join("unitkerja_m as uk","uk.id","=","pg.id_unitkerja")
        ->select("kj.*", "pg.namapegawai","uk.unitkerja")
        ->where("kj.id_pasien","=",$req->id_pasien)
        ->get();

        $datas = [];
        foreach ($data as $item) {
            
            $keluhan = DB::table("listkeluhan_t as lkel")
            ->join("keluhan_m as kel","kel.id","=","lkel.id_keluhan")
            ->select("kel.keluhan")
            ->where("lkel.id_kunjungan","=",$item->id)
            ->get();

            $hasillab = DB::table("listhasillab_t as lhl")
            ->join("hasillab_m as hl","hl.id","=","lhl.id_hasillab")
            ->select("hl.hasillab")
            ->where("lhl.id_kunjungan","=",$item->id)
            ->get();


            $item->keluhan = $keluhan;
            $item->hasillab = $hasillab;
            array_push($datas, $item);

            // array_push($datas, $item);
        }

        return response()->json([
            "data"=>$datas

        ]);
    }


    public function getKeluhanHasilLab(Request $req)
    {
        if($req->jenis == 'kel'){

            $query = "
            select kjt.id, kl.keluhan as keluhan from kunjungan_t as kjt 
            left join listkeluhan_t as lkl on lkl.id_kunjungan = kjt.id
            join keluhan_m as kl on kl.id = lkl.id_keluhan 
            where kjt.id_pasien = ".$req->id_pasien."
            and kjt.id='".$req->id_kunjungan."'";
            
        } else{
            $query = "
            select kjt.id, kl.hasillab  as keluhan  from kunjungan_t as kjt 
            left join listhasillab_t as lkl on lkl.id_kunjungan = kjt.id
            join hasillab_m as kl on kl.id = lkl.id_hasillab 
            where kjt.id_pasien = ".$req->id_pasien."
            and kjt.id='".$req->id_kunjungan."'";
        }


        $data = DB::select($query);

        return response()->json($data);

    }

    public function setJadwalKunjungan(Request $req)
    {
    try {
        //code...
        DB::beginTransaction();
            DB::table('jadwalkunjungan_t')->where('id_pasien','=',$req->id_pasien)->delete();
            $kunjunganawal = Carbon::parse($req->kunjunganawal);

            DB::table("jadwalkunjungan_t")->insert([
                "id"=> MasterController::Random(),
                "kunjunganke"=>'1',
                "aktif"=>'1',
                "id_pasien"=>$req->id_pasien,
                "jadwal"=>$kunjunganawal
            ]);
                
                $jadwal =  Array();
            for ($i=2; $i <=4 ; $i++) { 
                $jadwal[$i] = $kunjunganawal->addMonths(3)->format('Y-m-d');
                DB::table("jadwalkunjungan_t")->insert([
                    "id"=> MasterController::Random(),
                    "kunjunganke"=>$i,
                    "aktif"=>'1',
                    "id_pasien"=>$req->id_pasien,
                    "jadwal"=>$jadwal[$i]
                ]);
            }

            // for ($i=8; $i <=17 ; $i++) { 
            //     $jadwal[$i] = $kunjunganawal->addWeeks(1)->format('Y-m-d');
            //     DB::table("jadwalkunjungan_t")->insert([
            //         "id"=> MasterController::Random(),
            //         "aktif"=>'1',
            //         "id_pasien"=>$req->id_pasien,
            //         "kunjunganke"=>$i,
            //         "jadwal"=>$jadwal[$i]
            //     ]);
            // } 
            
            DB::commit();
            return response()->json([
                "tanggalkunjungan"=>$kunjunganawal,
                "jadwal"=>$jadwal,
                "jadwal1"=>$jadwal[5],

            ]);
        } catch (\Exception $th) {
            DB::rollback();
            return response()->json([
                "tanggalkunjungan"=>'0',
                "jadwal"=>'0',
                "jadwal1"=>'0',
            ]);
        }
    }


    public function getJadwalKunjungan(Request $req)
    {
        $data = DB::table("jadwalkunjungan_t")
        ->orderBy("kunjunganke")->where("id_pasien", $req->id_pasien )->get();
        return response()->json([
            "data"=>$data

        ]);
    }

    public function getGrafikPertumbuhanJanin(Request $req)
    {
        $data = DB::select("select kunjunganke,tinggifundus, masukpanggul , beratjanin from kunjungan_t as kjt where id_pasien =".$req->id_pasien);
        return response()->json([
            "datas"=>$data

        ]);
    }

}
