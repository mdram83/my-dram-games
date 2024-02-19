<script>
    window.MyDramGames = {...window.MyDramGames,
        'player': {
            'name': @if(isset($player)) `{{ $player->getName() }}` @else {{ 'undefined' }} @endif,
        }
    }
</script>
