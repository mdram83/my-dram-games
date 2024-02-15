<?php

namespace App\Models\GameCore\Game;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GameEloquentModel extends Model
{
    use HasFactory;
    use HasUuids;

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function hostable(): MorphTo
    {
        return $this->morphTo();
    }
}
