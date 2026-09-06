<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembimbingIndustri extends Model
{
    protected $primaryKey = 'id_pem';

    protected $fillable = ['user_id', 'nama_pem', 'perusahaan'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class, 'id_pem', 'id_pem');
    }

    public function penilaians()
    {
        return $this->hasMany(Penilaian::class, 'id_pem', 'id_pem');
    }
}
