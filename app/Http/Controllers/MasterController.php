<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class MasterController extends Controller
{
    public function saveDataPasien( Request $req )
    {
        try {
            DB::beginTransaction();
            if( $req['id'] ==""){
                $save = new Pasien();
                $save->aktif = 1;

            }else{
                $save = Pasien::find($req->id);
            }

            $save->nik = $req->nik;
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

    public  function getMasterData(Request $req)
    {
        $data =DB::table($req->masterData)->where("aktif", true);

        if(isset($req->limit)){
            $data = $data->limit($req->limit);
        }

 

        $data= $data->orderBy("id")->get();

        return response()->json($data);
    }

    public  function nonaktifkan(Request $req)
    {
        $data =DB::table($req->masterData);

        
        if(isset($req->aktif) && $req->aktif !="" ){
            $data = $data->where("aktif", $req->aktif);
        } else{
            $data = $data->where("aktif", true);

        }
    
        if(isset($req->limit) && $req->limit != ""){
            $data = $data->limit($req->limit);
        }

 

        $data= $data->orderBy("id")->get();

        return response()->json($data);
    }

    public static function Random()
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $date = (string) date('YmdHis');
        $rand = (string) mt_rand(0,9999999999);

        // $ran = $date .$rand.substr(str_shuffle(str_repeat($pool, 5)), 0, 16) ; 
        $ran = $date .$rand; 

        return $ran; 
    }

    public static function Random1()
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $date = (string) date('YmdHis');
        $rand = (string) mt_rand(0,9999999999);

        // $ran = $date .$rand.substr(str_shuffle(str_repeat($pool, 5)), 0, 16) ; 
        $ran = $rand; 

        return $ran; 
    }

  /*  create table postgres

    CREATE TABLE public.namatable (
        id int4 NULL GENERATED BY DEFAULT AS IDENTITY,
        aktif bit NULL,
        id_pasien int4 NULL,
        fint int2 not null,
        field2 varchar null,
        created_at timestamp(0) NULL,
        updated_at timestamp(0) NULL
    );

    */  
    
}
