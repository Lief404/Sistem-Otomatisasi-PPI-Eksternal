<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    protected $primaryKey = 'kd_mat';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['kd_mat', 'nama_komp', 'jam_min'];

    public function logbooks()
    {
        return $this->hasMany(Logbook::class, 'kd_mat', 'kd_mat');
    }
}
