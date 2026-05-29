<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pertanyaan extends Model
{
    protected $table = 'pertanyaan';

    protected $primaryKey = 'pertanyaan_id';

    public $timestamps = false;

    protected $fillable = [
        'pertanyaan',
        'aspek',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    public function opsiJawaban(): HasMany
    {
        return $this->hasMany(
            OpsiJawaban::class,
            'pertanyaan_id',
            'pertanyaan_id'
        )->orderBy('label');
    }
}
