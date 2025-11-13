<?php

namespace App\Services;

use App\Models\Bidan;
use App\Models\Pasien;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class BidanService
{
    /**
     * 🔹 Login Bidan
     */
     public function login(array $credentials)
    {
        $bidan = Bidan::where('username', $credentials['username'])->first();
        if ($bidan && Hash::check($credentials['password'], $bidan->password)) {
            return $bidan;
        }

        return null;
    }

    /**
     * 🔹 Bidan membuat pasien baru.
     */
    public function createPasien(array $data, string $bidanId): Pasien
    {
        // Cek duplikasi no_reg atau username
        if (Pasien::where('no_reg', $data['no_reg'])->exists()) {
            throw ValidationException::withMessages([
                'no_reg' => 'Nomor registrasi sudah terdaftar.',
            ]);
        }

        if (Pasien::where('username', $data['username'])->exists()) {
            throw ValidationException::withMessages([
                'username' => 'Username sudah terdaftar.',
            ]);
        }

        // Buat pasien baru
        return Pasien::create([
            'no_reg' => $data['no_reg'],
            'username' => $data['username'],
            'nama' => $data['nama'],
            'password' => Hash::make($data['password']),
            'alamat' => $data['alamat'],
            'umur' => $data['umur'],
            'gravida' => $data['gravida'],
            'paritas' => $data['paritas'],
            'abortus' => $data['abortus'],
            'bidan_id' => $bidanId, // dikaitkan ke bidan yang sedang login
        ]);
    }

    //Lihat datapasien
    public function lihatDataPasien(string $bidanId)
    {
        return Pasien::where('bidan_id', $bidanId)->get();
    }

    //Mulai persalinan
    public function mulaiPersalinan(Pasien $pasien): Persalinan
    {
        $existing = Persalinan::where('pasien_id', $pasien->id)
            ->where('status', '!=', 'selesai')
            ->first();

        if ($existing) {
            throw ValidationException::withMessages([
                'persalinan' => 'Pasien ini sudah memiliki persalinan aktif.',
            ]);
        }

        return Persalinan::create([
            'pasien_id' => $pasien->id,
            'tanggalAwalRawat' => now(),
            'status' => 'aktif',
        ]);
    }

    //Kirim pesan ke pasien
     public function kirimPesan(string $bidanId, string $pasienId, string $isiPesan)
    {
        $bidan = Bidan::find($bidanId);
        $pasien = Pasien::find($pasienId);

        if (!$bidan || !$pasien) {
            throw ValidationException::withMessages([
                'target' => 'Bidan atau Pasien tidak ditemukan.',
            ]);
        }

        return Pesan::create([
            'bidan_id' => $bidan->id,
            'pasien_id' => $pasien->id,
            'isiPesan' => $isiPesan,
            'pengirim' => 'bidan',
            'waktuKirim' => now(),
        ]);
    }
}
