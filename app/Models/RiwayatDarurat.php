<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatDarurat extends Model
{
    use HasFactory;

    protected $table = 'riwayat_darurat';
    protected $primaryKey = 'id';
    public $incrementing = false; // Karena ID string manual
    public $timestamps = false;   // Kita atur waktu manual sesuai diagram

    protected $fillable = [
        'id',
        'pasien_no_reg',
        'bidan_id',
        'waktu_dibuat',
        'waktu_selesai',
        'status'
    ];

    // Relasi balik ke Pasien
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_no_reg', 'no_reg');
    }

    // Relasi balik ke Bidan
    public function bidan()
    {
        return $this->belongsTo(Bidan::class, 'bidan_id', 'id');
    }
}