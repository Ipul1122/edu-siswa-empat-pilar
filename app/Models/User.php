<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Complete list of 84 DPR-RI Electoral Districts (Dapil) sorted ascending (A-Z).
     */
    public const DAPIL_LIST = [
        'ACEH I',
        'ACEH II',
        'BALI',
        'BANTEN I',
        'BANTEN II',
        'BANTEN III',
        'BENGKULU',
        'DAERAH ISTIMEWA YOGYAKARTA',
        'DKI JAKARTA I',
        'DKI JAKARTA II',
        'DKI JAKARTA III',
        'GORONTALO',
        'JAMBI',
        'JAWA BARAT I',
        'JAWA BARAT II',
        'JAWA BARAT III',
        'JAWA BARAT IV',
        'JAWA BARAT V',
        'JAWA BARAT VI',
        'JAWA BARAT VII',
        'JAWA BARAT VIII',
        'JAWA BARAT IX',
        'JAWA BARAT X',
        'JAWA BARAT XI',
        'JAWA TENGAH I',
        'JAWA TENGAH II',
        'JAWA TENGAH III',
        'JAWA TENGAH IV',
        'JAWA TENGAH V',
        'JAWA TENGAH VI',
        'JAWA TENGAH VII',
        'JAWA TENGAH VIII',
        'JAWA TENGAH IX',
        'JAWA TENGAH X',
        'JAWA TIMUR I',
        'JAWA TIMUR II',
        'JAWA TIMUR III',
        'JAWA TIMUR IV',
        'JAWA TIMUR V',
        'JAWA TIMUR VI',
        'JAWA TIMUR VII',
        'JAWA TIMUR VIII',
        'JAWA TIMUR IX',
        'JAWA TIMUR X',
        'JAWA TIMUR XI',
        'KALIMANTAN BARAT I',
        'KALIMANTAN BARAT II',
        'KALIMANTAN SELATAN I',
        'KALIMANTAN SELATAN II',
        'KALIMANTAN TENGAH',
        'KALIMANTAN TIMUR',
        'KALIMANTAN UTARA',
        'KEPULAUAN BANGKA BELITUNG',
        'KEPULAUAN RIAU',
        'LAMPUNG I',
        'LAMPUNG II',
        'MALUKU',
        'MALUKU UTARA',
        'NUSA TENGGARA BARAT I',
        'NUSA TENGGARA BARAT II',
        'NUSA TENGGARA TIMUR I',
        'NUSA TENGGARA TIMUR II',
        'PAPUA',
        'PAPUA BARAT',
        'PAPUA BARAT DAYA',
        'PAPUA PEGUNUNGAN',
        'PAPUA SELATAN',
        'PAPUA TENGAH',
        'RIAU I',
        'RIAU II',
        'SULAWESI BARAT',
        'SULAWESI SELATAN I',
        'SULAWESI SELATAN II',
        'SULAWESI SELATAN III',
        'SULAWESI TENGAH',
        'SULAWESI TENGGARA',
        'SULAWESI UTARA',
        'SUMATERA BARAT I',
        'SUMATERA BARAT II',
        'SUMATERA SELATAN I',
        'SUMATERA SELATAN II',
        'SUMATERA UTARA I',
        'SUMATERA UTARA II',
        'SUMATERA UTARA III',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'class_name',
        'school_name',
        'image',
        'address',
        'dapil',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Helper to get avatar / photo URL.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            }
            return asset('storage/' . $this->image);
        }

        // Fallback default avatar generator using initials
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=dc2626&color=ffffff&size=200&bold=true';
    }

    /**
     * Check if user is Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is Siswa.
     */
    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    /**
     * Relationship to student progress.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(StudentProgress::class);
    }

    /**
     * Relationship to quiz attempts.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
