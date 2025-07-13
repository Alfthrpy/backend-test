<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class NilaiSTController extends Controller
{
    public function index()
    {
        $data_raw = DB::table('nilai as n')
            ->select(
                'n.nama',
                'n.nama_pelajaran',
                'n.nisn',
                DB::raw("CASE 
                    WHEN n.pelajaran_id = 44 THEN n.skor * 41.67
                    WHEN n.pelajaran_id = 45 THEN n.skor * 29.67
                    WHEN n.pelajaran_id = 46 THEN n.skor * 100
                    WHEN n.pelajaran_id = 47 THEN n.skor * 23.81
                    ELSE 0 
                END AS nilai_terkonversi")
            )
            ->where('n.materi_uji_id', 4)
            ->get();

        $grouped = $data_raw->groupBy('nama');

        $results = $grouped->map(function ($item_grup, $nama) {
            $nisn = $item_grup->first()->nisn;
            $sorted_grup = $item_grup->sortBy('nama_pelajaran');

            $nilai = $sorted_grup->mapWithKeys(function ($detail) {
                return [
                    strtolower($detail->nama_pelajaran) => (float) number_format($detail->nilai_terkonversi, 2, '.', '')
                ];
            });

            // Hitung total nilai
            $total_nilai = $sorted_grup->sum('nilai_terkonversi');

            return [
                'nama' => $nama,
                'nisn' => $nisn,
                'listNilai' => $nilai,
                'total' => (float) number_format($total_nilai, 2, '.', '')
            ];
        });

        $sortedResults = $results->sortByDesc('total')->values();
        return response()->json($sortedResults);
    }
}
