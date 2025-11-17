<?php

namespace App\Http\Controllers;

use App\Models\Bidan;
use App\Services\KontenEdukasiService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class KontenEdukasiController extends Controller
{
    private KontenEdukasiService $service;

    public function __construct(KontenEdukasiService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /api/konten-edukasi
     * List konten edukasi untuk pasien (tanpa auth bidan).
     */
    public function index()
    {
        $konten = $this->service->listKontenUntukPasien();

        return response()->json([
            'status' => 'success',
            'data'   => $konten,
        ]);
    }

    /**
     * POST /api/konten-edukasi
     * Buat konten edukasi (hanya bidan, butuh JWT).
     * Body: { "judulKonten": "...", "isiKonten": "..." }
     */
    // app/Http/Controllers/KontenEdukasiController.php

    public function store(Request $request)
    {
        // Validasi input untuk memastikan judul dan isi konten ada
        $request->validate([
            'judul_konten' => 'required|string|max:255',
            'isi_konten'   => 'required|string',
        ]);

        // Ambil data input
        $judulKonten = $request->input('judul_konten');
        $isiKonten = $request->input('isi_konten');

        // Ambil ID bidan dari pengguna yang sedang login (asumsi menggunakan JWT)
        $user = $request->auth_user; // Sesuaikan dengan cara kamu menangani autentikasi pengguna
        $bidanId = $user->id;

        try {
            // Buat konten edukasi, ID konten akan di-generate otomatis
            $konten = $this->service->buatKonten($bidanId, [
                'judul_konten' => $judulKonten,
                'isi_konten' => $isiKonten,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Konten edukasi berhasil dibuat.',
                'data' => $konten,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Input tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * DELETE /api/konten-edukasi/{id}
     * Hapus konten edukasi milik bidan.
     */
    public function destroy(string $id)
    {
        $user = $request->auth_user;


        try {
            $this->service->hapusKonten($user, $id);

            return response()->json([
                'status'  => 'success',
                'message' => 'Konten edukasi berhasil dihapus.',
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menghapus konten.',
            ], 500);
        }
    }
    // app/Http/Controllers/Api/KontenEdukasiController.php

    public function show(string $id)
    {
        try {
            $konten = $this->service->detailKontenEduksi($id);

            return response()->json([
                'status' => 'success',
                'data'   => $konten,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat mengambil konten.',
            ], 500);
        }
    }

}
