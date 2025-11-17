<?php

namespace App\Services;

use App\Models\Bidan;
use App\Models\KontenEdukasi;
use Illuminate\Validation\ValidationException;

class KontenEdukasiService
{
    // app/Services/KontenEdukasiService.php

    public function buatKonten(string $bidan, array $data): KontenEdukasi
    {
        $judul = $data['judul_konten'] ?? null;
        $isi = $data['isi_konten'] ?? null;

        if (!$judul || !$isi) {
            throw ValidationException::withMessages([
                'judul_konten' => ['Judul konten wajib diisi.'],
                'isi_konten' => ['Isi konten wajib diisi.'],
            ]);
        }

        // Generate ID konten otomatis
        $lastKonten = KontenEdukasi::orderBy('id', 'desc')->first();
        $nextNumber = $lastKonten ? intval(preg_replace('/\D/', '', $lastKonten->id)) + 1 : 1;
        $kontenId = 'Konten' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Buat konten edukasi dengan bidan_id yang otomatis diambil dari login user
        return KontenEdukasi::create([
            'id' => $kontenId,   // ID konten otomatis
            'bidan_id' => $bidan,  // ID bidan diambil dari yang login
            'judul_konten' => $judul,
            'isi_konten' => $isi,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function hapusKonten(Bidan $bidan, string $kontenId): void
    {
        $konten = KontenEdukasi::where('id', $kontenId)
            ->where('bidan_id', $bidan->id)
            ->first();

        if (!$konten) {
            throw ValidationException::withMessages([
                'konten' => ['Konten tidak ditemukan atau bukan milik Anda.'],
            ]);
        }

        $konten->hapusKonten();
    }

    public function listKontenUntukPasien()
    {
        // bisa ditambah filter publish / kategori nanti
        return KontenEdukasi::orderBy('created_at', 'desc')->get();
    }

    public function listKontenUntukBidan(Bidan $bidan)
    {
        return KontenEdukasi::where('bidan_id', $bidan->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }


    public function detailKontenEduksi(string $kontenId): KontenEdukasi
    {
        // Cari konten berdasarkan ID
        $konten = KontenEdukasi::where('id', $kontenId)->first();

        if (!$konten) {
            throw ValidationException::withMessages([
                'konten' => ['Konten edukasi tidak ditemukan.'],
            ]);
        }

        return $konten;
    }

}
