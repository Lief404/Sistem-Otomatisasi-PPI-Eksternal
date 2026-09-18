<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['id_prodi', 'kd_mat', 'nama_komp', 'jam_min'];

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'id_prodi', 'id_prodi');
    }

    public function logbooks()
    {
        return $this->hasMany(Logbook::class, 'kd_mat', 'kd_mat');
    }
}
