<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class GameEloquentModel extends Model
{
    use HasFactory;
    use HasUuids;

    public function playersRegistered(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'game_player');
    }

    public function playersAnonymous(): MorphToMany
    {
        return $this->morphedByMany(PlayerAnonymousEloquent::class, 'game_player');
    }

    public function hostable(): MorphTo
    {
        return $this->morphTo();
    }
}
