<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pertanyaan extends Model
{
    protected $table = 'pertanyaan';

    protected $primaryKey = 'pertanyaan_id';

    public $timestamps = false;

    protected $fillable = [
        'pertanyaan',
    ];

    public function jawaban()
    {
        return $this->hasMany(Jawaban::class, 'pertanyaan_id', 'pertanyaan_id');
    }
}
