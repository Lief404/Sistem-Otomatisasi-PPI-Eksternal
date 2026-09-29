<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $table = 'approvals';

    // Sesuaikan dengan nama kolom di migration Anda
    protected $fillable = [
        'mahasiswa_nim',
        'jenis_form',
        'pihak_penilai',
        'penilai_id',
        'status',
        'alasan_reject',
        'qr_code_path',
        'token_verifikasi',
        'approved_at',
    ];

    // Relasi opsional jika dibutuhkan
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_nim', 'nim');
    }

    public function penilai()
    {
        return $this->belongsTo(User::class, 'penilai_id');
    }
}