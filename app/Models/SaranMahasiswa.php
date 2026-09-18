<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaranMahasiswa extends Model
{
    protected $fillable = [
        'nim',
        'id_pem',
        'tanggal',
        'saran',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    public function pembimbingIndustri()
    {
        return $this->belongsTo(PembimbingIndustri::class, 'id_pem', 'id_pem');
    }
}
