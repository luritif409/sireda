<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public function canUploadSignature(): bool
    {
        return $this->role === 'dosen' || $this->role === 'admin';
    }

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
        'nim',
        'judul_tugas_akhir',
        'signature_path',
        'dosen_pembimbing_id',
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

    public function revisionsAsMahasiswa(): HasMany
    {
        return $this->hasMany(\App\Models\Revision::class, 'mahasiswa_id');
    }

    public function revisionsAsDosen(): HasMany
    {
        return $this->hasMany(\App\Models\Revision::class, 'dosen_id');
    }

    /**
     * The dosen pembimbing (mentor) for this mahasiswa
     */
    public function dosenPembimbing(): BelongsTo
    {
        return $this->belongsTo(self::class, 'dosen_pembimbing_id');
    }

    /**
     * All mahasiswa assigned to this dosen as pembimbing
     */
    public function mahasiswaBimbingan(): HasMany
    {
        return $this->hasMany(self::class, 'dosen_pembimbing_id');
    }
}
