<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CatatanPartografService;
use App\Models\CatatanPartograf;
use App\Models\Partograf;
use App\Models\Persalinan;

class CatatanPartografController extends Controller
{
    protected $service;

    public function __construct(CatatanPartografService $service)
    {
        $this->service = $service;
    }

    public function buatCatatanPartograf(Request $request, $id)
    {
        $data = $request->all();

        $data['partograf_id'] = $id;

        $catatan = $this->service->create($data);

        return response()->json([
            'message' => 'Catatan partograf berhasil dibuat',
            'data' => $catatan
        ], 201);
    }

    public function getCatatanByPartograf($id)
    {
        $catatan = $this->service->getByPartografId($id);

        if ($catatan->isEmpty()) {
            return response()->json(['message' => 'Belum ada catatan untuk partograf ini'], 404);
        }

        return response()->json([
            'data' => $catatan
        ]);
    }

  public function getCatatanPartografPasien($noReg)
{
    // 1. Cari persalinan aktif pasien
    $persalinan = Persalinan::where('pasien_no_reg', $noReg)
        ->where('status', 'aktif')
        ->first();

    if (!$persalinan) {
        return response()->json([
            'message' => 'Pasien belum memiliki persalinan aktif'
        ], 404);
    }

    // 2. Ambil partograf dari persalinan
    $partograf = $persalinan->partograf;

    if (!$partograf) {
        return response()->json([
            'message' => 'Partograf belum dibuat untuk persalinan ini'
        ], 404);
    }

    // 3. Ambil catatan partograf terbaru
    $catatan = CatatanPartograf::with('kontraksi')
        ->where('partograf_id', $partograf->id)
        ->orderBy('waktu_catat', 'desc')
        ->first();

    if (!$catatan) {
        return response()->json([
            'message' => 'Belum ada catatan partograf'
        ], 404);
    }

    return response()->json([
        'message' => 'Catatan partograf terbaru ditemukan',
        'data' => $catatan
    ], 200);
}


}
