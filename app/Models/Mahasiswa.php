<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['nim', 'user_id', 'nama_mhs', 'kelas', 'id_prodi', 'nidn', 'id_pem'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'id_prodi', 'id_prodi');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'nidn', 'nidn');
    }

    public function pembimbingIndustri()
    {
        return $this->belongsTo(PembimbingIndustri::class, 'id_pem', 'id_pem');
    }

    public function saranMahasiswas()
    {
        return $this->hasMany(SaranMahasiswa::class, 'nim', 'nim');
    }

    public function disiplinMahasiswas()
    {
        return $this->hasMany(DisiplinMahasiswa::class, 'nim', 'nim');
    }

    public function kuisionerMentors()
    {
        return $this->hasMany(KuisionerMentor::class, 'nim', 'nim');
    }

    public function logbooks()
    {
        return $this->hasMany(Logbook::class, 'nim', 'nim');
    }

    public function penilaians()
    {
        return $this->hasMany(Penilaian::class, 'nim', 'nim');
    }
}
