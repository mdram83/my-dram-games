<script>
    window.MyDramGames = {...window.MyDramGames,
        'routes': {
            'home': '{{ route('home') }}',
            'games.show': (slug) => '{{ route('games.show', ['slug' => '/']) }}' + `/${slug}`,
            'ajax.games.index': '{{ route('ajax.games.index') }}',
            'ajax.game-invites.store': '{{ route('ajax.game-invites.store') }}',
            'ajax.gameplay.store': '{{ route('ajax.gameplay.store') }}',
            'game-invites.join': (slug, gameId) => '{{ route('game-invites.join', ['slug' => '/', 'gameId' => '/']) }}' + `/${slug}/${gameId}`,
            'gameplay.show': (gameId) => '{{ route('gameplay.show', ['gameId' => '/']) }}' + `/${gameId}`,
            'login': '{{ route('login') }}',
        }
    }
</script>
