<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontenEdukasi extends Model
{
    use HasFactory;

    protected $table = 'konten_edukasi';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'id',
        'bidan_id',
        'judul_konten',
        'isi_konten',
    ];

    public function bidan()
    {
        return $this->belongsTo(Bidan::class, 'bidan_id', 'id');
    }

    // representasi method di class diagram
    public static function buatKonten(array $attributes): self
    {
        return self::create($attributes);
    }

    public function hapusKonten(): ?bool
    {
        return $this->delete();
    }
}
