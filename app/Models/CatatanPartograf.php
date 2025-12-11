<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class CatatanPartograf extends Model
{
    use HasFactory;

    protected $table = 'catatan_partograf';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'waktu_catat',
        'djj',
        'pembukaan_servik',
        'penurunan_kepala',
        'nadi_ibu',
        'suhu_ibu',
        'sistolik',
        'diastolik',
        'aseton',
        'protein',
        'volume_urine',
        'obat_cairan',
        'air_ketuban',
        'kontraksi_frekuensi',
        'kontraksi_durasi',
        'molase',
        'partograf_id',
    ];

    // Relasi ke parent Partograf
    public function partograf()
    {
        return $this->belongsTo(Partograf::class, 'partograf_id', 'id');
    }
    // Relasi ke banyak Kontraksi
    public function kontraksi()
    {
        return $this->hasMany(Kontraksi::class, 'catatan_partograf_id', 'id');
    }


    // Validasi data
    public static function validateData(array $data)
    {
        $validator = Validator::make($data, [
            'partograf_id' => 'required|string',
            'djj' => 'nullable|numeric',
            'pembukaan_servik' => 'nullable|numeric',
            'penurunan_kepala' => 'nullable|numeric',
            'nadi_ibu' => 'nullable|numeric',
            'suhu_ibu' => 'nullable|numeric',
            'sistolik' => 'nullable|numeric',
            'diastolik' => 'nullable|numeric',
            'aseton' => 'nullable|in:-,+',
            'protein' => 'nullable|in:-,+,++,+++',
            'volume_urine' => 'nullable|numeric',
            'obat_cairan' => 'nullable|string|max:100',
            'air_ketuban' => 'nullable|in:J,U,M,D,K',
            'kontraksi_frekuensi' => 'nullable|numeric|min:0|max:5', // <--- TAMBAHAN
            'kontraksi_durasi' => 'nullable|numeric|',
            'molase' => 'nullable|in:0,1,2,3',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    
}
