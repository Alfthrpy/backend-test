<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class NilaiRTController extends Controller
{
    public function index()
    {
        $data_raw = DB::table('nilai as n')
            ->select('n.nama', 'n.nisn', 'n.nama_pelajaran', 'n.skor')
            ->where('n.materi_uji_id', 7)
            ->whereNot('n.nama_pelajaran', 'Pelajaran Khusus')
            ->get();


        $grouped = $data_raw->groupBy('nama');

        $hasil_akhir = $grouped->map(function ($item, $nama) {
            $nisn = $item->first()->nisn;

            $sorted = $item->sortBy(function ($detail_nilai) {
            return strtolower($detail_nilai->nama_pelajaran);
            });
            $nilai = $sorted->mapWithKeys(function ($detail_nilai) {
            return [strtolower($detail_nilai->nama_pelajaran) => $detail_nilai->skor];
            });

            return [
            'nama' => $nama,
            'nisn' => $nisn,
            'nilaiRT' => $nilai
            ];
        });

        return response()->json($hasil_akhir->values());
    }
}
