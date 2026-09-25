<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
    ];

    /**
     * Relationship to regencies (Kabupaten / Kota).
     */
    public function regencies(): HasMany
    {
        return $this->hasMany(Regency::class)->orderBy('name');
    }

    /**
     * Relationship to users (Siswa).
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
