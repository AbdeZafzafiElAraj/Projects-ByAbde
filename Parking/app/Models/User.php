<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public function getIsAdminAttribute()
{
    return $this->role === 'admin';
}

public function getIsOperadorAttribute()
{
    return $this->role === 'operador';
}
    
    public function getIsNormalAttribute()
    {
        
        return $this->role === "normal";
    }
    
    
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

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get all of the registros for the user.
     */
    public function registrosAcceso()
    {
        return $this->hasMany(RegistroAcceso::class, 'user_id');
    }
}
