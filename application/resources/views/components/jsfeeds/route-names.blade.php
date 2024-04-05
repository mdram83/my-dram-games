<script>
    window.MyDramGames = {...window.MyDramGames,
        'routes': {
            'home': '{{ route('home') }}',

            'games.show': (slug) => '{{ route('games.show', ['slug' => '/']) }}' + `/${slug}`,
            'ajax.games.index': '{{ route('ajax.games.index') }}',

            'ajax.game-invites.store': '{{ route('ajax.game-invites.store') }}',
            'game-invites.join': (slug, gameId) => '{{ route('game-invites.join', ['slug' => '/', 'gameInviteId' => '/']) }}' + `/${slug}/${gameId}`,

            'ajax.gameplay.store': '{{ route('ajax.gameplay.store') }}',
            'gameplay.show': (gamePlayId) => '{{ route('gameplay.show', ['gamePlayId' => '/']) }}' + `/${gamePlayId}`,
            'ajax.gameplay.move': (gamePlayId) => '{{ route('ajax.gameplay.move', ['gamePlayId' => '/']) }}' + `/${gamePlayId}`,
            'ajax.gameplay.disconnect': (gamePlayId) => '{{ route('ajax.gameplay.disconnect', ['gamePlayId' => '/']) }}' + `/${gamePlayId}`,
            'ajax.gameplay.connect': (gamePlayId) => '{{ route('ajax.gameplay.connect', ['gamePlayId' => '/']) }}' + `/${gamePlayId}`,
            'ajax.gameplay.disconnect-forfeit': (gamePlayId) => '{{ route('ajax.gameplay.disconnect-forfeit', ['gamePlayId' => '/']) }}' + `/${gamePlayId}`,

            'login': '{{ route('login') }}',
        }
    }
</script>
