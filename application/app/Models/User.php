<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use MyDramGames\Utils\Player\PlayerRegistered;

class User extends Authenticatable implements PlayerRegistered
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'premium',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isRegistered(): bool
    {
        return true;
    }

//    public function gameInvites(): MorphToMany
//    {
//        return $this->morphToMany(GameInviteEloquentModel::class, 'game_invite_player');
//    }
//
//    public function hostedGameInvites(): MorphMany
//    {
//        return $this->morphMany(GameInviteEloquentModel::class, 'hostable');
//    }
    public function isPremium(): bool
    {
        return (bool) $this->premium;
    }
}
