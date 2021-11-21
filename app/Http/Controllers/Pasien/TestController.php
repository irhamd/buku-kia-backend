<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Pasien;
use App\Model\Rujukan;
use App\Model\KehamilanSaatIni;
use App\Model\KunjunganPasien;
use App\Model\PemriksaanDokter;
use App\Model\Arsip\ArsipBerkas;
use DB;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\Rujukan\RujukanController;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class TestController extends Controller
{
    public function testUpload( Request $req )
    {        
        try {
           
            $newId = MasterController::Random();
            $fileupload = $req->file('fileupload'); 
            $newId = MasterController::Random();
    
            if ($fileupload)
            {
                $ext = $fileupload->getClientOriginalExtension();
                $namafile = $req->filename.".$ext";
                $upload = Storage::disk('local')->put('Arsip/'.$namafile,file_get_contents($fileupload));

                $save = new ArsipBerkas();
                $save->id = $newId;
                $save->aktif = "1";
                $save->filename = $req['filename'];
                $save->deskripsi = $req['deskripsi'];
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

    public function deleteUpload( Request $req )
    {        
        try {
                $namafile = "";
                unlink(storage_path("app/Arsip/$req->filename"));
           
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
            $data = ArsipBerkas::all();
            
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

        $file_name = "rc-upload-1637298324372-4.png";
        // $path = storage_path().'/'.'app'.'/Arsip/'.$file_name;
        // if (file_exists($path)) {
        //     return \Response::download($path);
        // }

        // return Storage::response(storage_path('app/Arsip/'.$file_name));
        
        return response()->download(storage_path('app/Arsip/'.$file_name));
    }
    
   

 
    

}
