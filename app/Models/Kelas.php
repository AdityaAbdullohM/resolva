<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KelasMataPelajaran;

class Kelas extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'jurusan', 'deskripsi', 'semester_id', 'tahun_ajaran_id'];

    /**
     * Mendapatkan semester dari kelas ini.
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Mendapatkan tahun ajaran dari kelas ini.
     */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    /**
     * Mendapatkan mata pelajaran dari kelas ini.
     */
    public function mataPelajaran()
    {
        return $this->belongsToMany(MataPelajaran::class, 'kelas_mata_pelajaran')
                    ->using(KelasMataPelajaran::class)
                    ->withPivot('user_id');
    }

    /**
     * Mendapatkan guru (user) yang mengajar kelas ini.
     */
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'kelas_mata_pelajaran', 'kelas_id', 'user_id');
    }

    /**
     * Mendapatkan semua siswa (users) yang ada di kelas ini.
     */
    public function siswa()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Mendapatkan semua problem yang ada di kelas ini.
     */
    public function problems()
    {
        return $this->hasMany(Problem::class);
    }

    /**
     * Mendapatkan semua materi yang ada di kelas ini.
     */
    public function materis()
    {
        return $this->hasMany(Materi::class);
    }

    /**
     * The user who is the main teacher for this class (wali kelas).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias for the main teacher relationship.
     */
    public function guru()
    {
        return $this->user();
    }
}
