<?php

namespace App\Http\Controllers\Kunjungan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\Pasien\KehamilanController;
use App\Model\EvaluasiKesehatanIbu;
use DB;
use Carbon\Carbon;

use App\Http\Controllers\Rujukan\RujukanController;

class EvaluasiKesehatanController extends Controller
{
    public function evaluasiKesehatanIbuHamil11( Request $req )
    {
        try {
            // dd($req['riwayatkesehatanibu1']);

        } catch (\Exception $e) {
            $status =0;
            $err = "[".$e->getMessage()."]";
        }
    
        return response()->json([
            "sts" => implode(",", $req['riwayatkesehatanibu1']),
        ]);
    } 

    public function evaluasiKesehatanIbuHamil( Request $req )
    {
        try {
            $newId = MasterController::Random();
            DB::beginTransaction();
            if( $req->id ==""  ){
                $save = new EvaluasiKesehatanIbu();
                $save->id = $newId;
                $save->aktif = 1;
            }else{
                $save = EvaluasiKesehatanIbu::find($req->id);
            }
            $save->tanggal = $req->tanggal;
            $save->id_pasien = $req->id_pasien;
            $save->kunjunganke = $req->kunjunganke;
            $save->id_pasienregistrasi = $req->id_pasienregistrasi;
            $save->riwayatkehamilandanpersalinan_lainnya = $req->riwayatkehamilandanpersalinan_lainnya;
            $save->riwayatpenyakitkeluarga = implode(",", $req->riwayatpenyakitkeluarga );
            $save->riwayatpenyakitkeluarga_lainnya = $req->riwayatpenyakitkeluarga_lainnya;
            $save->tb = $req->tb;
            $save->bb = $req->bb;
            $save->lila = $req->lila;
            $save->riwayatkesehatanibuskarang =  implode(",", $req->riwayatkesehatanibuskarang );
            $save->riwayatkesehatanibuskarang_lainnya = $req->riwayatkesehatanibuskarang_lainnya;
            $save->statusimunisasi_checked =  implode(",", $req->statusimunisasi_checked );
            $save->riwayatprilaku_checked =   implode(",", $req->riwayatprilaku_checked );
            $save->riwayatprilaku_lain = $req->riwayatprilaku_lain;
            $save->inspeksiinspekulo = $req->inspeksiinspekulo;
            $save->vulva = $req->vulva;
            $save->uretra = $req->uretra;
            $save->vagina = $req->vagina;
            $save->fluksus = $req->fluksus;
            $save->fluor = $req->fluor;
            $save->porsio = $req->porsio;

            $save->id_pegawai = \Auth::user()->id_pegawai;
            $save->save();

            $riwayat = $req->riwayatkehamilandanpersalinan;

            if(isset($riwayat) && $riwayat !=""){
                foreach ($riwayat as  $val) {
                    $idR = MasterController::Random();
                    DB::table("riwayatkehamilanpersalinan_t")->insert([
                        "id" =>$idR,
                        "tahun"=>$val['tahun'],
                        "aktif"=>"1",
                        "berat"=>$val['berat'],
                        "persalinan"=>$val['persalinan'],
                        "penolong"=>$val['penolong'],
                        "komplikasi"=>$val['komplikasi'],
                        "created_at"=> date('Y-m-d H:i:s'),
                        "updated_at"=> date('Y-m-d H:i:s'),
                        "id_evaluasikesehatanibu" => $newId
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
        ]);
    } 


   

}
