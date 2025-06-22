<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @OA\Schema(
 * schema="User",
 * type="object",
 * title="User Schema",
 * description="Representa a un usuario del sistema",
 * @OA\Property(property="id", type="integer", readOnly=true, example=1),
 * @OA\Property(property="name", type="string", example="Leidy Palomino"),
 * @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 * @OA\Property(property="role", type="string", enum={"admin", "customer"}, example="customer"),
 * @OA\Property(property="is_active", type="boolean", example=true),
 * @OA\Property(property="created_at", type="string", format="date-time", readOnly=true, example="2025-06-17T18:00:00Z")
 * )
 */



class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    public $timestamps = false; // El schema no especifica updated_at

    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'role' => $this->role,
        ];
    }

    // Renombramos el método para obtener la contraseña para que coincida con el hash
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}