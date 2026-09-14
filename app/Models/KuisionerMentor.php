<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KuisionerMentor extends Model
{
    use HasFactory;

    protected $fillable = [
        'nim',
        'id_pem',
        'h1a', 'h1b', 'h1c', 'h1d', 'h2', 'h3', 'h4',
        's1', 's2', 's3', 's4', 's5', 's6', 's7', 's8',
        'tanggal'
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    public function mentor()
    {
        return $this->belongsTo(PembimbingIndustri::class, 'id_pem', 'id_pem');
    }
}
