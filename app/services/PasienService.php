<?php

namespace App\Services;

use App\Models\Pasien;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Bidan;
class PasienService
{
    /**
     * Login pasien
     */
    public function login(array $credentials): ?Pasien
    {
        return Pasien::login($credentials['username'], $credentials['password']);
    }

    /**
     * Buat pasien baru
     */
    public function createPasien(array $data, string $bidanId): Pasien
    {
        // Cek username duplikat
        if (Pasien::where('username', $data['username'])->exists()) {
            throw ValidationException::withMessages([
                'username' => 'Username sudah terdaftar.',
            ]);
        }

        // Generate no_reg otomatis
        $data['no_reg'] = Pasien::generateNoReg();
        $data['bidan_id'] = $bidanId;
        $data['password'] = Hash::make($data['password']);

        return Pasien::create($data);
    }

    public function getBidanByPasien(string $no_reg): array
{
    // Ambil pasien tanpa eager loading dulu
    $pasien = Pasien::find($no_reg);

    if (!$pasien) {
        return [
            'error' => 'Pasien tidak ditemukan',
            'status' => 404
        ];
    }

    // Jika bidan_id kosong/null → langsung return
    if (empty($pasien->bidan_id)) {
        return [
            'no_reg' => $pasien->no_reg,
            'bidan_id' => null,
            'bidan_nama' => null,
            'status' => 200
        ];
    }
    $bidanId = (string) $pasien->bidan_id;

    // Query bidan manual (untuk hindari integer cast)
    $bidan = Bidan::where('id', $bidanId)->first();

    return [
        'no_reg' => $pasien->no_reg,
        'bidan_id' => $pasien->bidan_id,
        'bidan_nama' => $bidan ? $bidan->nama : null,
        'status' => 200
    ];
}

}
