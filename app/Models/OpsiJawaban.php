<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpsiJawaban extends Model
{
    protected $table      = 'opsi_jawaban';
    protected $primaryKey = 'opsi_id';

    protected $fillable = [
        'pertanyaan_id',
        'label',
        'teks',
        'nilai',
    ];

    protected $casts = [
        'nilai' => 'integer',
    ];

    public function pertanyaan(): BelongsTo
    {
        return $this->belongsTo(Pertanyaan::class, 'pertanyaan_id', 'pertanyaan_id');
    }
}
