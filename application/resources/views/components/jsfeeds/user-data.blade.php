<script>
    window.MyDramGames = {...window.MyDramGames,
        'player': {
            'name': @if(isset($player)) `{{ $player->getName() }}` @else {{ 'undefined' }} @endif,

            // TODO I am exposing id to connect player to indivitual player private channels. Is it ok to expose it?
            'id': @if(isset($player)) `{{ $player->getId() }}` @else {{ 'undefined' }} @endif,
        }
    }
</script>
