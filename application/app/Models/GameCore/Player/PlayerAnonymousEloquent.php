<?php

namespace App\Models\GameCore\Player;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class PlayerAnonymousEloquent extends Authenticatable implements PlayerAnonymous
{
    use HasFactory;
    use HasUuids;
    use Prunable;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['hash', 'name'];

    public function prunable(): Builder
    {
        return static::where('updated_at', '<=', now()->subMinutes(config('player.playerHashExpiration')));
    }

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
        return false;
    }
}
