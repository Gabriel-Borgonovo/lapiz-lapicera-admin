<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image', // Agrega este campo
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
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getProfileImageUrlAttribute()
    {
        // Ruta a la imagen por defecto
        $defaultProfileImage = app()->environment('production')
            ? 'imgs/profile_images/default.png'
            : 'storage/profile_images/default.png';

        if (app()->environment('production')) {
            // Entorno de producción
            if ($this->profile_image) {
                return env('APP_URL') . '/imgs/' . $this->profile_image;
            }
        } else {
            // Otros entornos (desarrollo, pruebas, etc.)
            if ($this->profile_image) {
                return env('APP_URL') . '/storage/' . $this->profile_image;
            }
        }

        // Retorna la ruta de la imagen por defecto
        return asset($defaultProfileImage);
    }
}
