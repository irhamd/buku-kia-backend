<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Pasien;
use App\Model\Rujukan;
use App\Model\KehamilanSaatIni;
use App\Model\KunjunganPasien;
use App\Model\PemriksaanDokter;
use DB;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\Rujukan\RujukanController;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class KehamilanController extends Controller
{
   
    public function saveKunjunganDummy($id_pasien)
    {

        $newId = MasterController::Random();

        $save = new KunjunganPasien();
        $save->id = $newId;
        $save->aktif = 1;
        $save->kunjunganke = 0;
        $save->id_pasien = $id_pasien;
        $save->id_unitkerja =  \Auth::user()->id_unitkerja;
        $save->id_pegawai = \Auth::user()->id_pegawai;
        $save->save();

        $keluhan = DB::table('listkeluhan_t')->insert([ 
            "id"=> MasterController::Random(),
            "id_keluhan"=> 18, // 18 = Tidak tahu Hari Pertama Haid Terakhir (HPHT)
            "aktif"=> 1,
            "id_kunjungan" =>$newId
        ]);

        return $newId;


    }

    public function simpanDataKehamilanSaatIni( Request $req )
    {
        try {
            DB::beginTransaction();
            if( $req->id ==""){
                $newId = MasterController::Random();
                $save = new KehamilanSaatIni();
                $save->id = $newId;
                $save->aktif = 1;

            }else{
                $save = KehamilanSaatIni::find($req->id);
            }
            $save->id_pasien = $req->id_pasien;
            $save->hpht = $req->hpht !=""? $req->hpht: null ;
            $save->htp = $req->htp !=""? $req->htp: null ;
            $save->penggunaankontrasepsi = $req->penggunaankontrasepsi;
            $save->riwayatpenyakit = $req->riwayatpenyakit;
            $save->riwayatalergi = $req->riwayatalergi;
            $save->tetanustrakhir = $req->tetanustrakhir;
            // $save->id_unitkerja = \Auth::user()->id_unitkerja;
            $save->g = $req->g;
            $save->p = $req->p;
            $save->o = $req->o;
            $save->a = $req->a;
            $save->tb = $req->tb;
            $save->save();

            $status = $save ? 1:0;
            $err = "";

            if(!$req->hpht || $req->hpht ==null || $req->hpht=="" || $req->hpht=="null"){
                $id_kehamilan = $save->id;
                
                Rujukan::where("id_kehamilansaatini",$id_kehamilan)->delete();
                $id_kunjungan =  $this->saveKunjunganDummy($req->id_pasien);
                $ket = "Tidak tahu Hari Pertama Haid Terakhir (HPHT)";
                RujukanController::addKeterangan($ket,$id_kunjungan);

                Rujukan::insert([
                    // "keterangan" =>"Tidak tahu terakhir haid terakhir",
                    "id"=>MasterController::Random(),
                    "aktif"=>1,
                    "hasrujuk"=>0,
                    "status"=>"request",
                    "id_kunjungan"=>$id_kunjungan
                    // "id_kehamilansaatini" => $save->id
                ]);
            }

// SIMPAN JADWAL KUNJUNGAN ================================================================================================================
            $kunjunganawal = Carbon::parse(date('Y-m-d'));
                        
            DB::table('jadwalkunjungan_t')->where('id_pasien','=',$req->id_pasien)->delete();

            DB::table("jadwalkunjungan_t")->insert([
                "id"=> MasterController::Random(),
                "kunjunganke"=>'1',
                "aktif"=>'1',
                "id_pasien"=>$req->id_pasien,
                "jadwal"=> date('Y-m-d')
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


// SIMPAN JADWAL KUNJUNGAN ================================================================================================================


        DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            $status =0;
            $err = "[".$e->getMessage()."]";
        }

        return response()->json([
            "msg"=> $status ==0 ? "Gagal simpan data...".$err :"Suksess .",
            "sts" =>$status,
            "id_pasien" =>$req->id_pasien,
            "lastinsertid" => $save->id,
            "hpht" => $req->hpht,
        ]);
    }

    public function simpanPemeriksaanDokter( Request $req )
    {        
        try {
            DB::beginTransaction();
            if( $req->id ==""){
                $newId = MasterController::Random();
                $save = new PemriksaanDokter();
                $save->id = $newId;
                $save->aktif = 1;
                
            }else{
                $newId = $req->id;
                $save = PemriksaanDokter::find($req->id);
            }

            $save->id_pasien = $req->id_pasien;
            $save->htp = $req->htp !=""? $req->htp: null ;
            $save->gs = $req->gs;
            $save->crl = $req->crl;
            $save->djj = $req->djj;
            $save->usiakehamilan = $req->usiakehamilan;
            $save->letakjanin = $req->letakjanin;
            $save->kedaanumum = $req->kedaanumum;
            $save->sklera = $req->sklera;
            $save->kulit = $req->kulit;
            $save->leher = $req->leher;
            $save->gigimulut = $req->gigimulut;
            $save->tht = $req->tht;
            $save->leher = $req->leher;
            $save->gigimulut = $req->gigimulut;
            $save->paru = $req->paru;
            $save->perut = $req->perut;
            $save->tungkai = $req->tungkkai;
            $save->kunjunganke = $req->kunjunganke;
            $save->save();

            $status = $save ? 1:0;
            $err = "";

            if(isset($req->id) && isset($req->id) !=""){
                KehamilanSaatIni::where("id_pasien", $req->id_pasien)->update(["htp"=> $req->htp]);
            }

            $fileusg = $req->file('fileusg'); 
    
            if ($fileusg)
            {
                $ext = $fileusg->getClientOriginalExtension();
                $namafile = "usg-".$newId.".".$ext;
                $upload = Storage::disk('local')->put('USG/'.$namafile,file_get_contents($fileusg));
            }

            DB::commit();
            // }


        } catch (\Exception $e) {
            DB::rollback();
            $status =0;
            $err = "[".$e->getMessage()."]";
        }

        return response()->json([
            "msg"=> $status ==0 ? "Gagal simpan data...". $err :"Suksess .",
            "sts" =>$status,
            "lastinsertid" => $newId
        ]);
    }


    public function testUpload( Request $req )
    {        
        try {
           
            $newId = MasterController::Random();
            $fileupload = $req->file('fileupload'); 
    
            if ($fileupload)
            {
                $ext = $fileupload->getClientOriginalExtension();
                $namafile = $req->filename.".".$ext;
                $upload = Storage::disk('local')->put('TES/'.$namafile,file_get_contents($fileupload));
            }

            $err = "";
            $status =1;

        } catch (\Exception $e) {
            $status =0;
            $err = "[".$e->getMessage()."]";
        }

        return response()->json([
            "msg"=> $status ==0 ? "Gagal simpan data...". $err :"Suksess .",
            "sts" =>$status,
            // "lastinsertid" => $newId
        ]);
    }

    public function deleteUpload( Request $req )
    {        
        try {
                $namafile = "";
                unlink(storage_path("app/TES/$req->filename"));
           
            $err = "";
            $status =1;

        } catch (\Exception $e) {
            $status =0;
            $err = "[".$e->getMessage()."]";
        }

        return response()->json([
            "msg"=> $status ==0 ? "Gagal simpan data...". $err :"Suksess .",
            "sts" =>$status,
            "lastinsertid" => $req->filename
        ]);
    }

    public function getDataKehamilanSaatIni(Request $req)
    {
        $data =DB::table("kehamilansaatini_t as khm")
        ->select("khm.*","kjt.kunjunganke")
        ->leftJoin("kunjungan_t as kjt","kjt.id_pasien","=","khm.id_pasien")
        ->selectRaw("AGE(khm.hpht,'".date('Y-m-d')."') as umurkehamilan")
        ->where("khm.id_pasien","=",$req->id_pasien)
        ->where("khm.aktif","=",1)
        ->orderBy("kjt.kunjunganke","desc")
        ->first();

        $cekPanggul = DB::table("kunjungan_t")
            ->select("masukpanggul as masukpanggull")
            ->where("id_pasien","=",$req->id_pasien)
            ->where("masukpanggul","1")
            ->first();

        return response()->json(["data"=> $data, "panggul"=>$cekPanggul]);
    }

    public static function getUmurKehamilan($id_pasien, $tgl)
    {
        $data = KehamilanSaatIni::where("id_pasien",$id_pasien)
            ->select("hpht", 
                DB::raw("hpht + interval '1 month' * 8 + interval '1 DAY' * 9 as tgl"), 
                DB::raw("AGE(hpht - INTERVAL '9 MONTH - 10 DAY','".date($tgl)."') as umurkehamilan"))
            ->first();

            $a = $data->umurkehamilan;
            $umur = str_replace('days','Hari',$a);
            $umur = str_replace('mons','Bulan',$umur);
            $umur = str_replace('years','Tahun',$umur);
            $umur = str_replace('-','',$umur);
            $umur = str_replace('00:00:00','0 Hari',$umur);


        if($data){
            $tgl1 = new \DateTime($tgl); 
            $tgl2 = new \DateTime($data->hpht); 
            
            $umurhari = $tgl2->diff($tgl1)->days;
            return array ($umurhari, $data->tgl);
            
        } else{
            return array (0, "0 Hari");
        }

       
    }

 
    

}
