<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DisiplinMahasiswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nim',
        'id_pem',
        'p1', 'p2', 'p3', 'p4', 'p5',
        's3', 's5', 's6', 's7', 's8',
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
