<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    protected $primaryKey = 'id_log';

    protected $fillable = ['nim', 'kd_mat', 'tanggal', 'durasi_mnt', 'kegiatan', 'status', 'jam_mulai', 'jam_selesai', 'minggu_ke', 'catatan_mentor'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'kd_mat', 'kd_mat');
    }
}
