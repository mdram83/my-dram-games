<script>
    window.MyDramGames = {...window.MyDramGames,
        'player': {
            'name': @if(isset($player)) `{{ $player->getName() }}` @else {{ 'undefined' }} @endif,

            // TODO I am exposing id to connect player to indivitual player private channels. Is it ok to expose it?
            'id': @if(isset($player)) `{{ $player->getId() }}` @else {{ 'undefined' }} @endif,

            // TODO temp to check env vars issues
            'app': {
                'env': @if(env('APP_ENV') !== null) `{{ env('APP_ENV') }}` @else {{ 'not set' }} @@endif,
            }
        }
    }
</script>
