<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("/PushPanikButton","Rujukan\RujukanController@PushPanikButton");
Route::post("/testUpload","Pasien\TestController@testUpload");
Route::get("/showFiles","Pasien\TestController@showFiles");



// EBBBB
Route::post("/eb-savePasienNewEB","AmergencyButton\EmergencyButtonController@savePasienNewEB");
Route::get("/eb-getDataDashboard","AmergencyButton\EmergencyButtonController@getDataDashboard");
Route::get("/eb-getDataPasienEB","AmergencyButton\EmergencyButtonController@getDataPasienEB");
Route::get("/eb-getRiwayatLengkapPasien","AmergencyButton\EmergencyButtonController@getRiwayatLengkapPasien"); // table pasien daftar
Route::get("/eb-cekDataPasienEB","AmergencyButton\EmergencyButtonController@cekDataPasienEB");
Route::post("/eb-getWaktuTekan","AmergencyButton\EmergencyButtonController@getWaktuTekan");

// Route::get("/eb2-riwayatButtonPasien","AmergencyButton\EmergencyButtonControllerDua@riwayatButtonPasien");
Route::get("/eb2-mobile-cekPasienBlock","AmergencyButton\EmergencyButtonControllerDua@cekPasienBlock");
Route::post('imageadd', 'PengaduanSimrs\PengaduanSimrsController@addimage');
Route::post('notif-sendNotification', 'Notification\NotifikasiController@sendNotification');



Route::group(['middleware' => 'auth:sanctum'], function(){
        Route::get("/getUser","User\UserController@getUser");
        Route::get("/listUser","User\UserController@listUser");
        Route::get("/userDetail","User\UserController@userDetail");
        Route::post("/simpanUserBaru","User\UserController@simpanUserBaru");
        Route::get("/hapusUser","User\UserController@hapusUser");

        // KEHAMILAN SAAT INI
        Route::post("/simpanDataKehamilanSaatIni","Pasien\KehamilanController@simpanDataKehamilanSaatIni");
        Route::get("/getDataKehamilanSaatIni","Pasien\KehamilanController@getDataKehamilanSaatIni");

        // PEMERIKSAAN DOKTER
        Route::post("/simpanPemeriksaanDokter","Pasien\KehamilanController@simpanPemeriksaanDokter");

        // TESTT

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
        Route::get("/updateStatusPasienRujuk/{id}/{status}","Rujukan\RujukanController@updateStatusPasienRujuk");
        Route::get("/getPasienRujukRS","Rujukan\RujukanController@getPasienRujukRS");


        Route::post("/updateKeluhanRujuk","Rujukan\KeluhanController@updateKeluhanRujuk");



        // Route::post("/getMasterData","MasterController@getMasterData");
        Route::get("/Random","MasterController@Random");
        Route::get("/make", function(){
                // $data = csrf_token();


                return response()->json(csrf_token());
                // return  Auth::user()->currentAccessToken();
        });

        // PASIEN REGISTRASI
        Route::post("/saveRegistrasiPasien","Pasien\RegistrasiController@saveRegistrasiPasien");
        Route::get("/daftarRegistrasiPasien","Pasien\RegistrasiController@daftarRegistrasiPasien");

        // PASIEN
        Route::get("/getDataPasien","Pasien\PasienController@getDataPasien");
        Route::get("/getDataPasienRev","Pasien\PasienController@getDataPasienRev");
        Route::post("/saveDataPasien","Pasien\PasienController@saveDataPasien");

        Route::get("logout","User\UserController@logout");

        Route::get("/detailKunjungan/{id_pasien}","Kunjungan\KunjunganController@detailKunjungan");



        // ARSIP ================================================================================================
        Route::get("/arsip-getBerkas","ArsipBerkas\ArsipBerkasController@getBerkas");
        Route::get("/arsip-fileupload","ArsipBerkas\ArsipBerkasController@getfileupload");
        Route::delete("/deleteUpload","ArsipBerkas\ArsipBerkasController@deleteUpload");
        Route::delete("/deleteProyek","ArsipBerkas\ArsipBerkasController@deleteProyek");
        Route::get("/downloadBerkas","ArsipBerkas\ArsipBerkasController@downloadBerkas");
        Route::post("/getMasterData","MasterController@getMasterData");
        Route::get("/arsip-showBerkasArsip","ArsipBerkas\ArsipBerkasController@showBerkasArsip");
        Route::get("/arsip-getdetailRegister","ArsipBerkas\ArsipBerkasController@getdetailRegister");
        Route::post("/simpanDataBerkas","ArsipBerkas\ArsipBerkasController@simpanDataBerkas");


        // PENGADUAN SIMRS
        Route::post("/pengaduan-simpanPengaduan","PengaduanSimrs\PengaduanSimrsController@simpanPengaduan");
        Route::get("/pengaduan-getKeluhanPasien","PengaduanSimrs\PengaduanSimrsController@getKeluhanPasien");
        Route::get("/pengaduan-getKeluhanPasien-by-petugas","PengaduanSimrs\PengaduanSimrsController@getKeluhanPasienByPetugas");
        Route::get("/pengaduan-CekProgresPengaduan","PengaduanSimrs\PengaduanSimrsController@CekProgresPengaduan");
        Route::post("/pengaduan-assignTo","PengaduanSimrs\PengaduanSimrsController@assignTo");
        Route::post("/pengaduan-simpanFollowUpPengaduan","PengaduanSimrs\PengaduanSimrsController@simpanFollowUpPengaduan");
        Route::get("/pengaduan-getCountNumber","PengaduanSimrs\PengaduanSimrsController@getCountNumber");
        Route::post("/pengaduan-simpanInputPengaduan","PengaduanSimrs\PengaduanSimrsController@simpanInputPengaduan");
        Route::get("/pengaduan-getRuangan","PengaduanSimrs\PengaduanSimrsController@getRuangan");
        Route::get("/pengaduan-updateStatus","PengaduanSimrs\PengaduanSimrsController@updateStatus");
        Route::delete("/pengaduan-hapusTugas","PengaduanSimrs\PengaduanSimrsController@hapusTugas");
        Route::post("/pengaduan-simpanRuangan","PengaduanSimrs\PengaduanSimrsController@simpanRuangan");
        Route::get("/pengaduan-detailTugas","PengaduanSimrs\PengaduanSimrsControllerAdd@detailTugas");
        Route::get("/pengaduan-getpetugas","PengaduanSimrs\PengaduanSimrsControllerAdd@getpetugas");
        Route::get("/pengaduan-dashboard-get","PengaduanSimrs\DashboardController@getData");

        // Route::resource('imageadd', 'PengaduanSimrs\PengaduanSimrsController@addimage');
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


