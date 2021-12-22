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

class PengaduanSimrsController extends Controller
{
    public function simpanPengaduan( Request $req )
    {
        $newId = MasterController::Random1();
        try {
           DB::beginTransaction();
            $save = PengaduanSimrs::findOrNew($req['id']);

            $save->aktif = 1;
            $save->unitkerja = $req['unitkerja'];
            $save->id_ruangan = $req['id_ruangan'];
            $save->nohp = $req['nohp'];
            $save->isipengaduan = $req['isipengaduan'];
            $save->keterangan = $req['keterangan'];
            $save->progres = 'rq';
            $save->close = '0';
            $save->assignto = $req['assignto'];
            $save->nomorpengaduan = isset($req['nomorpengaduan']) ? $req['nomorpengaduan'] : $newId;
            $save->save();
            DB::commit();
            $status = $save ? 1:0;
            $err = "";


            if($status == 1){
                    $token = DB::table("pegawai_m")->where("id", $req['assignto'])->select("token_firebase")->first();
                    // $aa = DB::table("m_ruangan")->where("id", $req['id_ruangan'])->first();

                    NotifikasiController::sendNotification(
                        strtoupper($req['unitkerja']), 
                        $req->isipengaduan, 
                        $token->token_firebase
                    );
            }
        

        } catch (\Exception $e) {
            $status =0;
            DB::rollback();
            $err = "[".$e->getMessage()."]";
        }
    
        return response()->json([
            "msg"=> $status ==0 ? "Gagal simpan data...".$err :"Suksess .",
            "id" =>  $save->id,
            "token" =>  $token->token_firebase,
            "sts" =>$status,
        ]);
    }
    public function assignTo( Request $req )
    {
        try {
            $save = PengaduanSimrs::find($req['id']);
            $save->assignto = $req['assignto'];
            $save->save();
            $status = $save ? 1:0;
            $err = "";

        } catch (\Exception $e) {
            $status =0;
            DB::rollback();
            $err = "[".$e->getMessage()."]";
        }
    
        return response()->json([
            "msg"=> $status ==0 ? "Gagal simpan data...".$err :"Suksess .",
            "id" =>  $save->id,
            "sts" =>$status
        ]);
    }

    public function getKeluhanPasien(Request $req)
    {
        $data = DB::table("pgd_pengaduan_t as pdg")->where("pdg.aktif","1")
        ->leftjoin("pegawai_m as pg","pdg.assignto","=", "pg.id")
        ->select("pdg.*", "pg.namapegawai", "pg.foto")
        ->where("pdg.created_at", ">", $req['tglawal'])
        ->where("pdg.created_at", "<", $req['tglakhir'])
        ->orderBy("pdg.created_at")->get();

        // if(isset($req->nama)){
        //     $data = $data->whereRaw("LOWER(ps.nama) like '%".$req->nama."%'");
        // }        
        
        // if(isset($req->nobuku)){
        //     $data = $data->whereRaw(" LOWER(ps.nobuku) like '%".$req->nobuku."%'");
        // }
        // $data = $data->limit(10)->orderBy("ps.nama")->get();
        
        return response()->json($data);
    }
    public function CekProgresPengaduan(Request $req)
    {
        $data = DB::table("pgd_pengaduan_t")->find($req['id']);
        return response()->json($data);
    }

    public function getKeluhanPasienByPetugas(Request $req)
    {
        $data = PengaduanSimrs::where("aktif","1")
        ->where("assignto","=", \Auth::user()->id_pegawai )
        ->where("close","=",$req["status"])
        ->orderBy("created_at")->get();

        // if(isset($req->nama)){
        //     $data = $data->whereRaw("LOWER(ps.nama) like '%".$req->nama."%'");
        // }        
        
        // if(isset($req->nobuku)){
        //     $data = $data->whereRaw(" LOWER(ps.nobuku) like '%".$req->nobuku."%'");
        // }
        // $data = $data->limit(10)->orderBy("ps.nama")->get();
        
        return response()->json($data);
    }

    public function getCountNumber(Request $req)
    {
        $pegawai = \Auth::user()->id_pegawai;
        
        $rq = PengaduanSimrs::where("aktif","1")
        ->where("assignto", $pegawai )
        ->where("progres","rq")
        ->count();

        $pr = PengaduanSimrs::where("aktif","1")
        ->where("assignto", $pegawai )
        ->where("progres","pr")
        ->count();
        $dn = PengaduanSimrs::where("aktif","1")
        ->where("assignto", $pegawai )
        ->where("progres","dn")
        ->count();
        $rj = PengaduanSimrs::where("aktif","1")
        ->where("assignto", $pegawai )
        ->where("progres","rj")
        ->count();

        // if(isset($req->nama)){
        //     $data = $data->whereRaw("LOWER(ps.nama) like '%".$req->nama."%'");
        // }        
        
        // if(isset($req->nobuku)){
        //     $data = $data->whereRaw(" LOWER(ps.nobuku) like '%".$req->nobuku."%'");
        // }
        // $data = $data->limit(10)->orderBy("ps.nama")->get();
        
        return response()->json([
            "rq" => $rq,
            "pr" => $pr,
            "dn" => $dn,
            "rj" => $rj,
            "id_pegawai" =>\Auth::user()->id_pegawai
        ]);
    }

    public function addimage(Request $request)
    {
      
           $file_sebelum = $request->file('image'); 
           $file_setelah = $request->file('image_setelah'); 

           if ($file_sebelum)
           {
               $ext = $file_sebelum->getClientOriginalExtension();
               $namafile = $request->title."sebelum .$ext";
                   $file_sebelum->move(public_path('/Pengaduan'), $namafile);
           }
           if ($file_setelah)
           {
               $ext = $file_setelah->getClientOriginalExtension();
               $namafile = $request->title."-setelah.$ext";
                   $file_setelah->move(public_path('/Pengaduan'), $namafile);
           }

        // $image->save();
        // return new ImageResource($image);
    }

    public function simpanFollowUpPengaduan( Request $req )
    {
        $filene = MasterController::Random();
        try {
           DB::beginTransaction();
            $save = PengaduanSimrs::find($req['id']);

            $save->penyebab = $req['penyebab'];
            // $save->finished_by = \Auth::user()->id_pegawai;
            $save->solusi = $req['solusi'];
            $save->penyebab = $req['penyebab'];
            $save->close = $req['close'];
            $save->waktu_selesai = date('Y-m-d H:i:s');

            if($req['close'] == "1"){
                    $save->progres = "dn";
                
            }

            $file_sebelum = $req->file('image_sebelum'); 
            $file_setelah = $req->file('image_setelah'); 

            if ($file_sebelum)
            {
                $ext = $file_sebelum->getClientOriginalExtension();
                $fn = $filene."-sb .$ext";
                $save->foto_sebelum =  $fn ;

                $file_sebelum->move(public_path('/Pengaduan'), $fn);
            }
            if ($file_setelah)
            {
                $ext = $file_setelah->getClientOriginalExtension();
                $fn = $filene."-st .$ext";
                $save->foto_sesudah =  $fn ;
                $file_setelah->move(public_path('/Pengaduan'), $fn);
            }

            
            $save->save();
            DB::commit();
            $status = $save ? 1:0;
            $err = "";

        } catch (\Exception $e) {
            $status =0;
            DB::rollback();
            $err = "[".$e->getMessage()."]";
        }
    
        return response()->json([
            "msg"=> $status ==0 ? "Gagal simpan data...".$err :"Suksess .",
            "sts" =>$status
        ]);
    }




 

   
    

}
