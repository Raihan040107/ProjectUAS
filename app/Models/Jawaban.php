<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    protected $table = 'jawaban';

    protected $primaryKey = 'jawaban_id';

    public $timestamps = false;

    protected $fillable = [
        'id_usaha',
        'pertanyaan_id',
        'jawaban',
    ];
}
