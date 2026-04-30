<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usaha extends Model
{
    protected $table = 'usaha';

    protected $primaryKey = 'id_usaha';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'nama_usaha',
        'bidang_usaha',
        'alamat',
    ];
}
