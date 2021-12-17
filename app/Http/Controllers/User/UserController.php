<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\User;
use App\Model\Pegawai;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use DB;


class UserController extends Controller
{
    function login(Request $request)
    {

        // return $request;
        $user= User::where('name','=', $request->name)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'msg' => ['Akses di tolak ...']
            ], 404);
        }
        $token = $user->createToken('my-app')->plainTextToken;

        $csrf = csrf_token();

        $pegawai = DB::table('pegawai_m as pg')
            ->join("unitkerja_m as un","un.id","=","pg.id_unitkerja")  
            ->join("users as uss","uss.id_pegawai","=","pg.id")   
            ->select("pg.*","un.unitkerja")   
            ->where("uss.id",$user->id)
            ->first();


        
        $response = [
            'user' => $user,
            'pegawai' => $pegawai,
            'token' => $token,
            'tokencrsf' => csrf_token(),
            // 'umurkehamilan' => $umurkehamilan,
            'tokenx' => \Hash::make(date('Ymisu'))
        ];
        
        return response($response, 201);
    }

    function loginRev(Request $request)
    {
        // return $request;
        $user= User::where('name','=', $request->name)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'msg' => ['Akses di tolak ...']
            ], 404);
        }
        $token = $user->createToken('my-app')->plainTextToken;

        $csrf = csrf_token();

        $pegawai = DB::table('pegawai_m as pg')
            ->leftjoin("unitkerja_m as un","un.id","=","pg.id_unitkerja")  
            ->join("users as uss","uss.id_pegawai","=","pg.id")   
            ->select("pg.*","un.unitkerja","un.lat","un.long","un.notelpon","un.kodefirebase")   
            ->where("uss.id",$user->id)
            ->first();


        
        $response = [
            'user' => $user,
            'pegawai' => $pegawai,
            'token' => $token,
            'tokencrsf' => csrf_token(),
            // 'umurkehamilan' => $umurkehamilan,
            'tokenx' => \Hash::make(date('Ymisu'))
        ];
        
        return response($response, 201);
    }

    public function getUser()
    {
        try {
            return response([
                'data' => "OK"
            ], 201);
        } catch (\Throwable $th) {
            return response([
                'data' => "Token tidak tersedia"
            ], 404);
        }
    }

    public function cekAuth()
    {
        try {
            return response([
                'cek' => "ok"
            ], 201);
        } catch (\Throwable $th) {
            return response([
                'cek' => "Token tidak tersedia"
            ], 404);
        }

    }

    

    public function getToken()
    {
        return csrf_token() ;
    }

    public function listUser()
    {
        $data = User::orderBy("id","asc")->get();
        return response()->json($data);
    }    
    
    
    public function userDetail( Request $req )
    {
        $data = User::where("id",$req->id)->first();
        return response()->json($data);
    }    
    
    public function hapusUser( Request $req )
    {
        $hapus = User::where("id",$req->id)->delete();
        return response()->json([
            "msg"=>$hapus ? "suksess":"gagal hapuss. ..."
        ]);
    }

    public function simpanUserBaru( Request $req )
    {
        $item = [
            "email"=>$req->email,
            "name"=>$req->name,
            "password"=>\Hash::make($req->password)
        ];

        if($req->id == 0){
            $simpan = User::insert($item);
        } else{
            $simpan = User::where("id",$req->id)->update($item);
        }

        return response()->json([
            "sts"=>$simpan ? "suksess":"gagal simpan ..."
        ]);
    }


    public function logout()
    {
    //    $logout =  \Auth::user()->currentAccessToken()->delete();
       $logout =  auth()->user()->currentAccessToken()->delete();

        return response()->json([
            "sts"=>$logout ? "suksess":"gagal simpan ..."
        ]);
    }


}
