<?php

namespace App\Models\GameCore\Game;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GameEloquentModel extends Model
{
    use HasFactory;
    use HasUuids;

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
