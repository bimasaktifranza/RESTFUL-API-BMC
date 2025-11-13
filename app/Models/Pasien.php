<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Hash;
use App\Models\Bidan;

class Pasien extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'pasien';
    protected $primaryKey = 'no_reg';
    public $incrementing = false;
    public $timestamps = false;

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
