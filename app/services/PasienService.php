<?php

namespace App\Services;

use App\Models\Pasien;
use Illuminate\Support\Facades\Hash;

class PasienService
{
  public function login(array $credentials)
    {
        $pasien = Pasien::where('username', $credentials['username'])->first();
        if ($pasien && Hash::check($credentials['password'], $pasien->password)) {
            return $pasien;
        }

        return null;
    }

    public function updateProfile(Pasien $pasien, array $data)
    {
        $pasien->update($data);
        return $pasien;
    }

    //Lihat progress persalinan
    public function lihatProgresPersalinan(Pasien $pasien)
    {
        $persalinan = Persalinan::where('pasien_id', $pasien->id)
            ->orderBy('tanggalAwalRawat', 'desc')
            ->first();

        if (!$persalinan) {
            return [
                'status' => 'Belum ada data persalinan untuk pasien ini.',
                'detail' => null,
            ];
        }

        return [
            'status' => $persalinan->status,
            'tanggal_awal' => $persalinan->tanggalAwalRawat,
            'tanggal_selesai' => $persalinan->tanggalSelesai ?? null,
        ];
    }

    //kirm pesan
    public function kirimPesan(string $pasienId, string $bidanId, string $isiPesan)
    {
        $pasien = Pasien::find($pasienId);
        $bidan = Bidan::find($bidanId);

        if (!$pasien || !$bidan) {
            throw ValidationException::withMessages([
                'target' => 'Pasien atau Bidan tidak ditemukan.',
            ]);
        }

        return Pesan::create([
            'pasien_id' => $pasien->id,
            'bidan_id' => $bidan->id,
            'isiPesan' => $isiPesan,
            'pengirim' => 'pasien',
            'waktuKirim' => now(),
        ]);
    }
}
