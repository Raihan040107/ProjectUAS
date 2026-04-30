<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    protected $table = 'dokumen';

    protected $primaryKey = 'id_dokumen';

    public $timestamps = false;

    protected $fillable = [
        'id_usaha',
        'ktp',
        'npwp',
        'surat_izin_usaha',
        'status_verifikasi',
        'tanggal_registrasi',
        'tanggal_verifikasi',
    ];
}
