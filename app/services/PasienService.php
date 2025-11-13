<?php

namespace App\Services;

use App\Models\Pasien;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
}
