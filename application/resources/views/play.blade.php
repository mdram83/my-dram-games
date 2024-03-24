<x-play-layout>

    @push('custom-scripts')
        @vite('resources/js/src/games/' . $gameInvite['slug'] . '/index.jsx')
    @endpush

    <div id="game-play-root"
         data-game.id="{{ $gamePlayId }}"
         data-game.invite="{{ json_encode($gameInvite) }}"
         data-game.situation="{{ json_encode($situation) }}"
    ></div>

</x-play-layout>
