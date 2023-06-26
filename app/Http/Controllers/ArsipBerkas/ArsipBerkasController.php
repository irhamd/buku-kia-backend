<?php

namespace App\Http\Controllers\ArsipBerkas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Pasien;
use App\Model\Rujukan;
use App\Model\KehamilanSaatIni;
use App\Model\KunjunganPasien;
use App\Model\PemriksaanDokter;
use App\Model\Arsip\ArsipBerkas;
use App\Model\Arsip\ArsipDokumen;
use DB;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\Rujukan\RujukanController;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Response;

class ArsipBerkasController extends Controller
{
    public function testUpload( Request $req )
    {
        try {

            $newId = MasterController::Random();
            $fileupload = $req->file('fileupload');


            if ($fileupload)
            {
                $ext = $fileupload->getClientOriginalExtension();
                $namafile = $req->filename.".$ext";
                $upload = Storage::disk('local')->put('Arsip/'.$namafile,file_get_contents($fileupload));

                $save = new ArsipBerkas();
                $save->id = $newId;
                $save->aktif = "1";
                $save->noberkas = $req['noberkas'];
                $save->filename = $req['filename'];
                $save->deskripsi = $req['deskripsi'];
                $save->id_dokumen = $req['id_dokumen'];
                $save->id_registerpengadaan = $req['id_registerpengadaan'];
                $save->ext = $ext;
                $save->save();

            }

            $err = "";
            $status = 1;

        } catch (\Exception $e) {
            $status =0;
            $err = "[".$e->getMessage()."]";
        }

        return response()->json([
            "msg"=> $status == 0 ? "Gagal simpan data...". $err :"Suksess .",
            "sts" =>$status,
            // "lastinsertid" => $newId
        ]);
    }

    public function simpanDataBerkas( Request $req )
    {
        try {
            $dok = ArsipDokumen::firstOrNew(['id' =>  $req['id']]);
            $dok->id = $req['id'];
            $dok->aktif = "1";
            $dok->jenis = $req['jenis'];
            $dok->id_jenispekerjaan = $req['jenispekerjaan'];
            $dok->tanggal = date('Y-m-d');
            $dok->namapekerjaan = $req['namapekerjaan'];
            $dok->sumberdana = $req['sumberdana'];
            $dok->carapembelian = $req['carapembelian'];
            $dok->tahunanggaran = $req['tahunanggaran'];
            if($req['id_jenisarsip'] != null && $req['id_jenisarsip'] !=""){
                $dok->id_jenisarsip = $req['id_jenisarsip'] ; // 2 nakes lainnya
            } else{
                $dok->id_jenisarsip = "10" ; // 10 nakes lainnya
            }
            // $dok->id_jenisarsip = $req['id_jenisarsip'];
            $dok->id_ppk = $req['id_ppk'];
            $dok->hps = $req['hps'];
            $dok->nilaikontrak = $req['nilaikontrak'];
            $dok->save();

            $err = "";
            $status = 1;

        } catch (\Exception $e) {
            $status =0;
            $err = "[".$e->getMessage()."]";
        }

        return response()->json([
            "msg"=> $status == 0 ? "Gagal simpan data...". $err :"Suksess .",
            "sts" =>$status,
            // "lastinsertid" => $newId
        ]);
    }

    public function deleteUpload( Request $req )
    {
        try {
             DB::beginTransaction();
             $namafile = $req->filename.".".$req->ext;
              $del =  DB::table("arsip_arsipberkasproyek_t")->where("filename", $req->filename)->delete();
              if($del){
                //   unlink(storage_path("app/Arsip/$namafile"));
                \File::delete("Arsip". $namafile);
              }
            $err = "";
            $status =1;
            DB::commit();


        } catch (\Exception $e) {
            $status =0;
            DB::rollBack();
            $err = "[".$e->getMessage()."]";
        }

        return response()->json([
            "msg"=> $status ==0 ? "Gagal simpan data...". $err :"Suksess .",
            "sts" =>$status,
            "lastinsertid" => $req->filename
        ]);
    }

    public function deleteProyek( Request $req )
    {
        try {
             ArsipDokumen::find($req->id)->update(['aktif'=> "0"]);
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

    public function getBerkas( Request $req )
    {
        try {

            $status =1;
            $data = ArsipDokumen::get();

        } catch (\Exception $e) {
            $status =0;
            $err = "[".$e->getMessage()."]";
        }

        return response()->json([
            "sts" =>$status,
            "data" => $data
        ]);
    }

    public function getfileupload( Request $req )
    {
        try {

            $status =1;
            $data = DB::table('');

        } catch (\Exception $e) {
            $status =0;
            $err = "[".$e->getMessage()."]";
        }

        return response()->json([
            "sts" =>$status,
            "data" => $data
        ]);
    }
    public function downloadBerkas( Request $req ){

        $file_name = "rc-upload-1637549753295-13.pdf";
        $path = storage_path().'/'.'app'.'/Arsip/'.$file_name;
        // if (file_exists($path)) {
        //     return \Response::download($path);
        // }

        // return Storage::response(storage_path('app/Arsip/'.$file_name));

        // return response()->download(storage_path('app/Arsip/'.$file_name));

        // $filename = "Arsip\rc-upload-1637550102490-7.pdf";

        // $path = storage_path($filename);

        return Response::make(file_get_contents($path), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"'
        ]);

    }


    public function showBerkasArsip( Request $req )
    {
      $data = DB:: table("arsip_dokumen_t as ad")
      ->leftjoin("pegawai_m as pg","pg.id","=","ad.id_ppk")
      ->leftjoin("jenisarsip_m as ja","ja.id","=","ad.id_jenisarsip")
      ->leftjoin("bidang_m as bd","bd.id","=","ja.bidang_id")
      ->leftjoin("arsip_jenispekerjaan_m as jp","jp.id","=","ad.id_jenispekerjaan")
        ->select(
                "ad.id","ad.id_jenisarsip","ja.jenisarsip","ad.tanggal","ja.bidang_id","bd.bidang","ad.sumberdana","ad.carapembelian", "ad.aktif", "ad.created_at", "ad.jenis", "ad.id_jenispekerjaan", "ad.namapekerjaan","ad.nilaikontrak","ad.hps", "ad.id_ppk","ad.tahunanggaran",
                "pg.namapegawai as namappk",
                "jp.jenispekerjaan"
                )->where("ad.aktif","1");

        if(isset($req->jenis) && $req->jenis != ""){
            $data = $data->where("ad.jenis",$req->jenis);
        }
        if(isset($req->tglawal) && $req->tglawal != "" && $req->tglawal != "Invalid date" ){
            $data = $data->whereRaw(" ad.tanggal between  '$req->tglawal 00:00:00' and  '$req->tglakhir 23:59:59'");
        }

        if(isset($req->tahunanggaran) && $req->tahunanggaran != ""){
            $data = $data->where("ad.tahunanggaran",$req->tahunanggaran);
        }
        if(isset($req->jenisarsip) && $req->jenisarsip != ""){
            $data = $data->where("ad.id_jenisarsip",$req->jenisarsip);
        }

        if(isset($req->jenispekerjaan) && $req->jenispekerjaan != ""){
            $data = $data->where("ad.id_jenispekerjaan",$req->jenispekerjaan);
        }

        if(isset($req->sumberdana) && $req->sumberdana != ""){
            $data = $data->where("ad.sumberdana",$req->sumberdana);
        }

        if(isset($req->namappekerjaan) && $req->namappekerjaan != ""){
            $data = $data->where("ad.namapekerjaan","like","%$req->namappekerjaan%");
        }


        if(isset($req->id_ppk) && $req->id_ppk != ""){
            $data = $data->where("ad.id_ppk",$req->id_ppk);
        }

        $data = $data->orderBy("ad.created_at","desc")->limit(100)->get();

        return response()->json([
            "data" => $data
        ]);
    }

    public function getdetailRegister( Request $req )
    {
      $data = DB:: table("arsip_arsipberkasproyek_t as arb")
      ->join("arsip_registerpengadaan_m as rg","rg.id", "arb.id_registerpengadaan")
        ->select("arb.keterangan", "rg.registerpengadaan", "arb.id_registerpengadaan")
        ->where("arb.id_dokumen", $req['id_dokumen'])
        ->where("arb.aktif","1")
        // ->orderBy( "rg.id" )
        ->groupBy("arb.keterangan", "rg.registerpengadaan","arb.id_registerpengadaan")
        ->get();


        foreach ($data as $datas) {
                $detail = DB:: table("arsip_arsipberkasproyek_t as arb")
                // ->select("id","filename","deskripsi","ext")
                ->where("id_dokumen", $req['id_dokumen'])
                ->where("id_registerpengadaan", $datas->id_registerpengadaan)
                ->get();
                $datas->detail = $detail;

        }


        return response()->json([
            "data" => $data
        ]);
    }







}
