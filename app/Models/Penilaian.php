<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $primaryKey = 'id_nilai';

    protected $fillable = ['nim', 'nidn', 'id_pem', 'n_presentasi', 'n_makalah', 'n_prestasi', 'n_supervisi', 'detail_presentasi', 'detail_makalah'];

    protected $casts = [
        'detail_presentasi' => 'array',
        'detail_makalah' => 'array',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'nidn', 'nidn');
    }

    public function pembimbingIndustri()
    {
        return $this->belongsTo(PembimbingIndustri::class, 'id_pem', 'id_pem');
    }
}
