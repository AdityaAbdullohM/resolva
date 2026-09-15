<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'photo',
        'nis', // Add nis
        'nip', // Add nip
        'kelas_id', // Add kelas_id
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's profile photo URL.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the class that the user (siswa) belongs to.
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Mendapatkan semua kelas yang diajar oleh user (sebagai guru).
     */
    public function kelasYangDiajar(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'kelas_mata_pelajaran', 'user_id', 'kelas_id')
                    ->using(KelasMataPelajaran::class)
                    ->withPivot('mata_pelajaran_id');
    }

    /**
     * Mendapatkan semua kelas yang diikuti oleh user (sebagai siswa).
     */
    public function kelasYangDiikuti()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_user');
    }

    /**
     * Mendapatkan semua submission yang dimiliki oleh user.
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Get all of the refleksi for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function refleksi()
    {
        return $this->hasMany(Refleksi::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    public function discussionPosts()
    {
        return $this->hasMany(DiscussionPost::class);
    }

    /**
     * Mendapatkan semua problem yang dibuat oleh user (sebagai guru).
     */
    public function problems()
    {
        return $this->hasMany(Problem::class);
    }

    /**
     * Mendapatkan semua grup yang diikuti oleh user.
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_user');
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if the user is a guru/dosen (teacher).
     *
     * @return bool
     */
    public function isGuru(): bool
    {
        return in_array($this->role, ['guru', 'dosen']);
    }

    /**
     * Check if the user is a siswa/mahasiswa (student).
     *
     * @return bool
     */
    public function isSiswa(): bool
    {
        return in_array($this->role, ['siswa', 'mahasiswa']);
    }

    /**
     * Check if the user is a teacher for a specific class or an admin.
     *
     * @param Kelas $kelas
     * @return bool
     */
    public function isTeacherOrAdminForKelas(Kelas $kelas): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        // Check if the user is directly assigned as the main teacher for the class
        if ($kelas->user_id === $this->id) {
            return true;
        }

        // Check if the user is assigned as a teacher for any subject in the class
        return $kelas->teachers()->where('users.id', $this->id)->exists();
    }
}
