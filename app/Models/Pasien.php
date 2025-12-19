<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Hash;
use App\Models\Bidan;
use App\Models\Persalinan;
use App\Services\CatatanPartografService;

class Pasien extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'pasien';
    protected $primaryKey = 'no_reg';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_reg',
        'username',
        'nama',
        'password',
        'alamat',
        'umur',
        'gravida',
        'paritas',
        'abortus',
        'bidan_id',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'umur' => 'integer',
        'gravida' => 'integer',
        'paritas' => 'integer',
        'abortus' => 'integer',
    ];

    // Relasi ke bidan
    public function bidan()
    {
        return $this->belongsTo(Bidan::class, 'bidan_id', 'id');
    }

    // Login pasien
    public static function login(string $username, string $password): ?self
{
    $pasien = self::where('username', $username)->first();

    if (!$pasien || !Hash::check($password, $pasien->password)) {
        return null;
    }

    return $pasien;
}

    // Generate no_reg otomatis
    public static function generateNoReg(): string
    {
        $tahun = date('Y');
        $jumlahPasienTahunIni = self::where('no_reg', 'like', "PASIEN%$tahun")->count();
        $n = $jumlahPasienTahunIni + 1;

        return "PASIEN{$n}{$tahun}";
    }

    public function persalinan()
    {
        return $this->hasMany(Persalinan::class, 'pasien_no_reg', 'no_reg');
    }

    public function lihatProgresPersalinan()
{
    $service = app(CatatanPartografService::class);
    return $service->getAllCatatanPartografPasien($this->no_reg);
}

    public function kirimSinyalDarurat(string $tipe): RiwayatDarurat
    {
        // 1. Generate ID Unik (Misal: EMG + Timestamp + Random)
        $newId = 'EMG' . time() . rand(100, 999);

        // 2. Buat Record RiwayatDarurat (Create)
        // Sesuai logika diagram: Pasien "membuat" RiwayatDarurat
        $darurat = RiwayatDarurat::create([
            'id' => $newId,
            'pasien_no_reg' => $this->no_reg,
            'bidan_id' => $this->bidan_id, // Asumsi pasien sudah punya bidan
            'waktu_dibuat' => now(),
            'waktu_selesai' => null,
            'status' => 'PENDING',
            // 'tipe' tidak disimpan di DB sesuai diskusi sebelumnya, 
            // tapi parameter tetap ada sesuai diagram untuk validasi logic jika perlu.
        ]);

        return $darurat;
    }


    // JWT methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['role' => 'pasien'];
    }
}
