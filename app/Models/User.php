<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable, SoftDeletes;

    /**
     * Column info:
     *
     * id: uuid
     * username: string(50)
     * name: string
     * password: string
     * profile_id: uuid
     * valid_date: date
     * remember_token: string
     * login_attempt: integer
     * status_id: uuid
     * created_at: timestamp
     * updated_at: timestamp, nullable
     * created_by: uuid, nullable
     * updated_by: uuid, nullable
     * deleted_at: timestamp, nullable
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'name',
        'password',
        'profile_id',
        'valid_date',
        'login_attempt',
        'status_id',
        'created_by',
        'updated_by',
        'deleted_at',
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
            'username' => 'string',
            'password' => 'hashed',
        ];
    }
}
