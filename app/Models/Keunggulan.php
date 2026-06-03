<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keunggulan extends Model
{
    protected $table = 'keunggulan';

    protected $fillable = ['nomor', 'judul', 'deskripsi', 'order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
