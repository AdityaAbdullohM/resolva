<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KelasMataPelajaran;
use Illuminate\Support\Facades\Log;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $fillable = ['nama'];

    /**
     * Mendapatkan semua kelas yang menggunakan mata pelajaran ini.
     */
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_mata_pelajaran')
                    ->using(KelasMataPelajaran::class)
                    ->withPivot('user_id')
                    ->withTimestamps();
    }

    public function guru()
    {
        return $this->belongsToMany(User::class, 'kelas_mata_pelajaran', 'mata_pelajaran_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Get all of the materis for the MataPelajaran
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function materis()
    {
        return $this->hasMany(Materi::class);
    }

    /**
     * Get all of the problems for the MataPelajaran
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function problems()
    {
        return $this->hasMany(Problem::class);
    }

    /**
     * Get all of the pengumumans for the MataPelajaran
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pengumumans()
    {
        return $this->hasMany(Pengumuman::class);
    }
}
