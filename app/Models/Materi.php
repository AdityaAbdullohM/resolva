<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'deskripsi',
        'link_url',
        'kelas_id',
        'mata_pelajaran_id',
        'pertemuan_number',
    ];

    /**
     * Mendapatkan kelas tempat materi ini berada.
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Mendapatkan mata pelajaran yang terkait dengan materi ini.
     */
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    /**
     * Mendapatkan file-file yang terkait dengan materi ini.
     */
    public function materiFiles()
    {
        return $this->hasMany(MateriFile::class);
    }
}
