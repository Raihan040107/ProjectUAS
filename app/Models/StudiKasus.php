<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudiKasus extends Model
{
    protected $table = 'studi_kasus';

    protected $fillable = [
        'nomor',
        'nama_usaha',
        'deskripsi',
        'pencapaian',
        'order',
        'is_active',
    ];

    protected $casts = [
        'pencapaian' => 'array',
        'is_active'  => 'boolean',
        'order'      => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
