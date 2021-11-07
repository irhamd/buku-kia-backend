<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function(){
        Route::get("/getUser","User\UserController@getUser");
        Route::get("/listUser","User\UserController@listUser");
        Route::get("/userDetail","User\UserController@userDetail");
        Route::post("/simpanUserBaru","User\UserController@simpanUserBaru");
        Route::get("/hapusUser","User\UserController@hapusUser");
        Route::post("/simpanDataKehamilanSaatIni","Pasien\KehamilanController@simpanDataKehamilanSaatIni");
        Route::get("/getDataKehamilanSaatIni","Pasien\KehamilanController@getDataKehamilanSaatIni");

        // PEMERIKSAAN DOKTER
        Route::post("/simpanPemeriksaanDokter","Pasien\KehamilanController@simpanPemeriksaanDokter");

        // KUNJUNGAN PASIEN
        Route::post("/saveKunjungan","Kunjungan\KunjunganController@saveKunjungan");
        Route::get("/getKeluhanHasilLab","Kunjungan\KunjunganController@getKeluhanHasilLab");
        Route::get("/getGrafikPertumbuhanJanin","Kunjungan\KunjunganController@getGrafikPertumbuhanJanin");
        Route::post("/evaluasiKesehatanIbuHamil","Kunjungan\EvaluasiKesehatanController@evaluasiKesehatanIbuHamil");

        // RUJUKAN
        Route::post("/saveRujukan","Rujukan\RujukanController@saveRujukan");
        Route::get("/checkPasienDiRujuk","Rujukan\RujukanController@checkPasienDiRujuk");
        Route::get("/getPasienRujuk","Rujukan\RujukanController@getPasienRujuk");
        Route::get("/detailPasienRujuk","Rujukan\RujukanController@detailPasienRujuk");
        Route::get("/tidakPerluDiRujuk/{id}","Rujukan\RujukanController@tidakPerluDiRujuk");
        
        
        
        Route::post("/getMasterData","MasterController@getMasterData");
        Route::get("/Random","MasterController@Random");
        Route::get("/make", function(){
                // $data = csrf_token();

               
                return response()->json(csrf_token());
                // return  Auth::user()->currentAccessToken();
        });
        
        // PASIEN 
        Route::get("/getDataPasien","Pasien\PasienController@getDataPasien");
        Route::post("/saveDataPasien","Pasien\PasienController@saveDataPasien");
        
        Route::get("logout","User\UserController@logout");
        
        Route::get("/detailKunjungan/{id_pasien}","Kunjungan\KunjunganController@detailKunjungan");
        
        
        
        
});


// Route::post('/tokens/create', function (Request $request) {
//         $token = $request->user()->createToken($request->token_name);
    
//         return ['token' => $token->plainTextToken];
//     });


Route::post("/setJadwalKunjungan","Kunjungan\KunjunganController@setJadwalKunjungan");
Route::get("/getJadwalKunjungan","Kunjungan\KunjunganController@getJadwalKunjungan");

Route::get("/getKunjunganByPasien","Kunjungan\KunjunganController@getKunjunganByPasien");


        // $umurkehamilan = DB::table("kehamilansaatini_t as khm")
        //     ->selectRaw("AGE(khm.hpht,'".date('Y-m-d')."') as umurkehamilan")
        //     ->where('id_pasien',$user->id)
        //     ->first();


Route::get("/cekAuth","User\UserController@cekAuth");
Route::get("login","User\UserController@login");
Route::post("loginRev","User\UserController@loginRev");

