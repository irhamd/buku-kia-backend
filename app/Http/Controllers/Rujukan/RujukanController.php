<?php

namespace App\Http\Controllers\Rujukan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Rujukan;
use App\Model\KunjunganPasien;
use DB;
use App\Http\Controllers\MasterController;

class RujukanController extends Controller
{
    public function saveRujukan( Request $req )
    {
        try {
            DB::beginTransaction();
            Rujukan::where('id_kehamilansaatini',"=",$req->id_kehamilansaatini)->delete();
            if( $req['id'] ==""){
                $save = new Rujukan();
                $save->aktif = 1;
            }else{
                $save = Rujukan::find($req->id);
            }
            // id	aktif	id_kunjungan	id_unitkerja	tanggalrujuk	keterangan	jenis	id_kehamilansaatini	created_at	updated_at
        
            $save->id_kunjungan = $req->id_kunjungan;
            $save->id_unitkerja = $req->id_unitkerja; // id_unitkerja = TUJUAN RS RUJUKAN
            $save->tanggalrujuk = $req->tanggalrujuk;
            $save->keterangan = $req->keterangan;
            $save->jenis = $req->jenis;
            $save->id_kehamilansaatini = $req->id_kehamilansaatini;
            $save->save();
            $status = $save ? 1:0;
            $err = "";
            
            DB::commit();
        } catch (\Exception $e) {
            $status =0;
            $err = "[".$e->getMessage()."]";
            DB::rollBack();
        }
        
        return response()->json([
            "msg"=> $status ==0 ? "Gagal simpan data...".$err :"Suksess .",
            "sts" =>$status
        ]);
    }

    public static function addKeterangan($ket, $id_kunj)
    {
      $input =   DB::table("keterangantambahan_t")->insert([
            "aktif"=>1,
            "keterangan"=>$ket,
            "id_kunjungan"=>$id_kunj
        ]);

        return $input ? true : false;
    }


    public static function simpanPerluRujuk($id_kunjungan)
    {
      $newId = MasterController::Random();
      Rujukan::where('id_kunjungan','=',$id_kunjungan)->delete();
      $input = Rujukan::insert([
            "id"=>$newId,
            "aktif"=>1,
            "hasrujuk"=>0,
            "id_kunjungan"=>$id_kunjungan
        ]);

        return $input ? 1 : 0;
    }



    public function checkPasienDiRujuk(Request $req)
    {
        try {
            $id_pasien = $req->id_pasien;
            $tinggifundus = $req->tinggifundus;
            $hb = $req->hb;
            $tablettambahdarah = $req->tablettambahdarah;
            $id_kunjungan = $req->id_kunjungan;
            $ket = [];

            $perlurujuk = false;
            $sr = 0;
            DB::beginTransaction();

            // KunjunganPasien::find($id_kunjungan)->delete();

            $hasillab = "
                select hl.hasillab as keluhan, hlb.hb , hl.isrujuk, kjt.id_pasien ,kjt.* from kunjungan_t as kjt
                left join listhasillab_t as hlb on hlb.id_kunjungan  = kjt.id
                join hasillab_m as hl on hl.id = hlb.id_hasillab  
                where hl.isrujuk = '1'
                and kjt.id_pasien=".$id_pasien."
                and kjt.id = '".$id_kunjungan."'
            ";

            $keluhan = "
                select kl.keluhan ,0 as hb, kl.isrujuk ,kjt.id_pasien ,kjt.* from kunjungan_t as kjt
                left join listkeluhan_t as lk on lk.id_kunjungan  = kjt.id
                join keluhan_m as kl on kl.id = lk.id_keluhan 
                where kl.isrujuk = '1'
                and kjt.id_pasien=".$id_pasien."
                and kjt.id = '".$id_kunjungan."'
            ";
            $cek = DB::select("
                select * from 
                ( 
                    ".$hasillab." union ".$keluhan."            
                ) as r1 order by r1.hb desc
            ");


            // $perlurujuk = $cek ? true : false;

            if($cek){
                $perlurujuk = true;
                foreach ($cek as $item) {
                    $this->addKeterangan($item->keluhan ,$id_kunjungan);
                }
            }
        
            $minTFU = 38 ;
            if( (int) $tinggifundus >= $minTFU ){
                $this->addKeterangan("Tinggi Fundus  >=".$minTFU, $id_kunjungan);
                $perlurujuk = true;
            }            
            
            $minHB = 10 ;
            if( $hb !="undefined" && $hb !="" && $hb !=0 && (int)$hb < $minHB){
                // array_push($ket,  );
                $this->addKeterangan("Hemoglobin (HB) < ".$minHB, $id_kunjungan);
                $perlurujuk = true;

            }

            $minTTD = 8;
            if((int) $tablettambahdarah <$minTTD){
                $this->addKeterangan("Tablet tambah darah kurang dari $minTTD transfusi",$id_kunjungan);
                $perlurujuk = true;
            }

            if($perlurujuk){
                $sr = $this->simpanPerluRujuk($id_kunjungan);
            }
            DB::commit();

        } catch (\Throwable $th) {
            DB::rollback();
        }

        return response()->json([
            "data"=> $cek? $cek :"",
            "keterangan"=>$req->ket,
           "perlurujuk"=>$perlurujuk,
           "sr"=>$sr
        ]);
    }

    public function getPasienRujuk(Request $req)
    {
        $rujuk ="";
        if(isset($req->rujuk)){
            $rujuk = " and psr.hasrujuk = '".$req->rujuk."'";
        }

        $un = \Auth::user()->id_unitkerja;

        $data = DB::select("
                select  psr.id, ps.id as id_pasien,ps.nama,ps.nobuku,psr.status, pr.id_unitkerja,
                ps.alamat ,psr.id_kunjungan,kjt.kunjunganke, kjt.umurkehamilan1, kjt.created_at,ps.nohp,
                ps.foto from pasienrujuk_t as psr 
                join kunjungan_t as kjt on kjt.id = psr.id_kunjungan 

                join pasien_m as ps on ps.id = kjt.id_pasien 
                join pasienregistrasi_t as pr on pr.id = kjt.id_pasienregistrasi
                
                where psr.aktif ='1'
                and pr.id_unitkerja = $un

                ".$rujuk."
                order by psr.id
        ");

        $datas = [];
        foreach ($data as $item) {
            
            $keterangan = DB::select("
                select
                ketr.keterangan , ketr.id_kunjungan from kunjungan_t as kjt 
                join keterangantambahan_t as ketr on ketr.id_kunjungan  = kjt.id
                where kjt.id = '".$item->id_kunjungan."'
            ");
        
            $item->keterangan = $keterangan;
            array_push($datas, $item);

            // array_push($datas, $item);
        }
        return response()->json([
            "data"=>$datas,
            "jlh" => count($data)
        ]);
    }

    public function detailPasienRujuk(Request $req)
    {
       
        $data = DB::select("
            select
            ps.nama 
            ,ketr.keterangan , ketr.id_kunjungan from kunjungan_t as kjt 
            join keterangantambahan_t as ketr on ketr.id_kunjungan  = kjt.id
            join pasien_m as ps on ps.id= kjt.id_pasien 
            where kjt.id = '".$req->id_kunjungan."'
        ");
        return response()->json([
            "data"=>$data ? $data[0]:"",
            "keterangan" => $data
        ]);
    }

    public function tidakPerluDiRujuk($id)
    {
       $cek = Rujukan::find($id)->update(["aktif"=>0]);
        return response()->json([
            "cek"=>$cek ? 1:0,
        ]);

    }

    public function updateStatusPasienRujuk($id, $status)
    {
       $cek = Rujukan::find($id)->update([
           "status"=>$status,
           "hasrujuk"=>$status == 'commit' ? '1' : '0',
        ]);
        return response()->json([
            "sts"=>$cek ? 1:0,
        ]);

    }

    public function getPasienRujukRS(Request $req)
    {
        $data = DB::select("
                select  psr.id, ps.id as id_pasien,ps.nama,ps.nobuku,psr.status,
                ps.alamat ,psr.id_kunjungan,kjt.kunjunganke, kjt.umurkehamilan1, psr.created_at,ps.nohp,
                ps.foto, pr.tanggal as tglregistrasi, uk.unitkerja as faskes, uk.kodefirebase
                from pasienrujuk_t as psr 
                join kunjungan_t as kjt on kjt.id = psr.id_kunjungan 
                join pasien_m as ps on ps.id = kjt.id_pasien 
                join pasienregistrasi_t as pr on pr.id = kjt.id_pasienregistrasi
                join unitkerja_m as uk on uk.id = pr.id_unitkerja
                
                WHERE pr.aktif = '1'
                and psr.aktif = '1'
                and psr.status <> 'commit'
                and psr.status <> 'ditolak'
                order by psr.id
        ");
 
        return response()->json([
            "data"=>$data,
            "jlh" => count($data)
        ]);
    }

    public function PushPanikButton()
    {
        try {
            DB::beginTransaction();
            $newId = MasterController::Random();

            
                $save = new Rujukan();
                $save->id = $newId;
                $save->aktif = 1;
           
            // id	aktif	id_kunjungan	id_unitkerja	tanggalrujuk	keterangan	jenis	id_kehamilansaatini	created_at	updated_at
        
            $save->id_kunjungan = "ini tess aja";
            // $save->id_unitkerja = $req->id_unitkerja; // id_unitkerja = TUJUAN RS RUJUKAN
            // $save->tanggalrujuk = $req->tanggalrujuk;
             
            $save->save();
            $status = $save ? 1:0;
            $err = "";
            
            DB::commit();
        } catch (\Exception $e) {
            $status =0;
            $err = "[".$e->getMessage()."]";
            DB::rollBack();
        }
        
        return response()->json([
            "msg"=> $status ==0 ? "Gagal simpan data...".$err :"Suksess .",
            "sts" =>$status
        ]);
    }




}
