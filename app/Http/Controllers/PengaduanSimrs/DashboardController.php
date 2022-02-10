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

class DashboardController extends Controller
{
    public function getData( Request $req )
    {
        $datacount = DB::select("
            SELECT 
                count( id) as jumlah,
                0 as byapp,
                count( id) as bycall,
                sum( case when progres = 'dn' then 1 else 0 end ) as selesai,
                sum( case when progres <> 'dn' then 1 else 0 end ) as pending
            from(
                SELECT * from pgd_pengaduan_t WHERE created_at BETWEEN '$req->tglawal' and '$req->tglakhir'
            and aktif = '1'
            ) as rr
        ");

        $datachart = DB::select("
          


            SELECT 
                tgl, bulan,
                count( id) as jumlah,
                0 as byapp,
                count( id) as bycall,
                sum( case when progres = 'dn' then 1 else 0 end ) as selesai,
                sum( case when progres <> 'dn' then 1 else 0 end ) as pending
            from(
                SELECT extract( day from  created_at ) AS  tgl, extract( MONTH from created_at ) AS  bulan,  *  from pgd_pengaduan_t WHERE created_at  BETWEEN '$req->tglawal' and '$req->tglakhir' and aktif = '1' 
            ) as rr
            GROUP BY bulan, tgl order BY bulan, tgl

        ");


        $databyruangan = DB::select("
            SELECT  unitkerja , count( unitkerja) as jumlah
            from(
                SELECT  pdt.*  from pgd_pengaduan_t as pdt 
                WHERE pdt.created_at BETWEEN '$req->tglawal' and '$req->tglakhir' and pdt.aktif = '1'
            ) as rr GROUP BY unitkerja ORDER BY jumlah desc limit 8
        ");
        $databypetugas = DB::select("
                SELECT 
                namapegawai , count( namapegawai ) as jumlah
            from(
                SELECT pg.namapegawai ,  pdt.*  from pgd_pengaduan_t as pdt 
                left join pegawai_m as pg on pg.id = pdt.assignto
                WHERE pdt.created_at BETWEEN '$req->tglawal' and '$req->tglakhir' and pdt.aktif = '1'
            ) as rr
            GROUP BY namapegawai
            ORDER BY jumlah desc 
            limit 5
        ");

        return response()->json([
            "chart"=> $datachart,
            "count"=> $datacount,
            "bypetugas"=> $databypetugas,
            "byruangan"=> $databyruangan,
        ]);
        
    }


 

   
    

}
