<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaranPerusahaan extends Model
{
    protected $table = 'saran_perusahaans';
    
    protected $fillable = [
        'nim',
        'perusahaan',
        'tanggal',
        'saran',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }
}
