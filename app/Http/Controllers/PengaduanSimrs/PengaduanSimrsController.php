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
            if( $req['id'] =='' ){
                $save = new PengaduanSimrs();
                $save->aktif = 1;
                $save->progres = 'rq';
                $save->nomorpengaduan = isset($req['nomorpengaduan']) ? $req['nomorpengaduan'] : $newId;
                $save->close = '0';

            } else{
                $save =  PengaduanSimrs::find($req['id']);
            }

            $save->unitkerja = $req['unitkerja'];
            $save->id_ruangan = $req['id_ruangan'];
            $save->nama = $req['nama'];
            $save->nohp = $req['nohp'];
            $save->isipengaduan = $req['isipengaduan'];
            $save->id_kategori = $req['id_kategori'];
            $save->keterangan = $req['keterangan'];
            $save->assignto = $req['assignto'];
            $save->save();
            $status = $save ? 1:0;
            $err = "";

            $token = "";
            if($status == 1){
                $token = DB::table("pegawai_m")->where("id", $req['assignto'])->select("token_firebase")->first();
                $token = $token->token_firebase;
            }
        } catch (\Exception $e) {
            $token = "";
            $status =0;
            $err = "[".$e->getMessage()."]";
        }
    
        return response()->json([
            "msg"=> $status ==0 ? "Gagal simpan data...".$err :"Suksess .",
            "id" =>  $save->id,
            "token" =>  $token,
            "sts" =>$status,
        ]);
    }
    public function assignTo( Request $req )
    {
        try {
            $save = PengaduanSimrs::find($req['id']);
            $save->assignto = $req['assignto'];
            $save->alihkanke = $req['alihkanke'];
            if( $req['alihkanke']){
                $save->progres = 'alh'; // alihkan
            } else {
                $save->progres = 'rq'; // alihkan
            }
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
        ->leftjoin("unitkerja_m as un","un.id","=", "pdg.alihkanke")
        ->select("pdg.*", "pg.namapegawai as nmpg", "pg.foto", DB::raw(" case when pdg.progres='alh' then un.unitkerja else pg.namapegawai end as namapegawai "))
        ->where("pdg.created_at", ">", $req['tglawal'])
        ->where("pdg.created_at", "<", $req['tglakhir']);
        
        if(isset($req->id_pegawai)){
            $data = $data->where("pdg.assignto", $req['id_pegawai']);
        }        
        if(isset($req->ruangan)){
            $data = $data->whereRaw(" lower(pdg.unitkerja) ILIKE  '%$req->ruangan%' ");
            // $data = $data->whereRaw("pdg.unitkerja","like", "%$req->ruangan%");
        }        
        
        $data= $data->orderBy("pdg.created_at")->get();
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
        ->where("close","=",$req["status"]);
        
        if( \Auth::user()->role == "kains" ){
            $data = $data->where("assignto","=", $req->id_pegawai );

        } else{
            $data = $data->where("assignto","=", \Auth::user()->id_pegawai );
        }

        if(isset($req->tanggal)){
            $data = $data->where("created_at", ">", "$req->tanggal 00:00:00")
            ->where("created_at", "<", "$req->tanggal 23:59:00");
        }        
         $data = $data->orderBy("created_at", $req["status"] == 1 ? "desc" :"asc" )->take(50)->get();
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
            // $save->penyebab = $req['penyebab'];
            $save->close = $req['close'];
            $save->waktu_selesai = date('Y-m-d H:i:s');

            if($req['close'] == "1"){
                    $save->progres = "dn";
                
            }

            $file_sebelum = $req->file('image_sebelum'); 
            $file_setelah = $req->file('image_setelah'); 

            if ( isset ($file_sebelum) && $file_sebelum!= null)
            {
                $ext = $file_sebelum->getClientOriginalExtension();
                $fn = $filene."-sb .$ext";
                $save->foto_sebelum =  $fn ;

                $file_sebelum->move(public_path('/Pengaduan'), $fn);
            }
            if (isset($file_setelah) && $file_setelah!= null )
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
    public function simpanInputPengaduan( Request $req )
    {
        try {
            $filene = MasterController::Random();
            DB::beginTransaction();
            $id_pegawai = \Auth::user()->id_pegawai;
            $save = new PengaduanSimrs();

            // $save->id = $filene;
            $save->nohp = $req['nohp'];
            $save->unitkerja = $req['unitkerja'];
            $save->isipengaduan = $req['isipengaduan'];
            $save->assignto = $id_pegawai;
            $save->waktu_tindaklanjut = date('Y-m-d H:i:s');
            $save->waktu_selesai = date('Y-m-d H:i:s');
            $save->nomorpengaduan = $filene;
            $save->progres = "dn";
            $save->aktif = "1";
            $save->pelapor = "langsung";
            $save->finished_by = $id_pegawai;
            $save->solusi = $req['solusi'];
            $save->keterangan = $req['keterangan'];
            $save->penyebab = $req['penyebab'];
            $save->nama = $req['nama'];
            $save->close = "1";

            $file_sebelum = $req->file('image_sebelum'); 
            $file_setelah = $req->file('image_setelah'); 

            if ( isset ($file_sebelum) && $file_sebelum!= null)
            {
                $ext = $file_sebelum->getClientOriginalExtension();
                $fn = $filene."-sb .$ext";
                $save->foto_sebelum =  $fn ;

                $file_sebelum->move(public_path('/Pengaduan'), $fn);
            }
            if (isset($file_setelah) && $file_setelah!= null )
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

    public function getRuangan(){

        $ruang = [];

        $data = DB::table("m_ruangan")->where("aktif", "1")->get();

        foreach ($data as $dat) {
            array_push($ruang, $dat->ruangan);
        }
       
        return response()->json($ruang);
    }

    public function updateStatus( Request $req ){
        $update = PengaduanSimrs::find($req['id'])
        ->update([ 'status'=> $req['status'] ]);
        return response()->json( $update ? "1" : "0" );
    }

    public function hapusTugas( Request $req ){
        $del = PengaduanSimrs::find($req['id'])->delete();
        return response()->json( $del ? "1" : "0" );
    }

  public function simpanRuangan( Request $req )
    {
        try {
            $id = DB::table('m_ruangan')->max('id') + 1;
            $save = DB::table("m_ruangan")->insert([ "ruangan"=>$req['ruangan'], "aktif"=> "1", "id"=>$id] );
            $status = 1;
        } catch (\Exception $e) {
            $status = 0;
            $err = $e->getmessage();   
        }
        return response()->json([
            "msg"=> $status ==0 ? "Gagal simpan data...".$err :"Suksess .",
            "sts" =>$status
        ]);
    }


 

   
    

}
