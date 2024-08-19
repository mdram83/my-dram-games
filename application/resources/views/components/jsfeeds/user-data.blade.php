@php(
    $appEnv = env('APP_ENV', 'env not set')
)

<script>
    window.MyDramGames = {...window.MyDramGames,
        'player': {
            'name': @if(isset($player)) `{{ $player->getName() }}` @else {{ 'undefined' }} @endif,
            'id': @if(isset($player)) `{{ $player->getId() }}` @else {{ 'undefined' }} @endif,
            'premium': @if(isset($player)) {{ $player->isPremium() ? 'true' : 'false' }} @else {{ 'false' }} @endif,
        }
    }
</script>
