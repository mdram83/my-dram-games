<x-play-layout>

    <div>
        GamePlayId:<br>
        {{ $gamePlayId }}
    </div>

    <div>
        Invite:<br>
        @php(print_r($gameInvite))
    </div>

    <div>
        Situation:<br>
        @php(print_r($situation))
    </div>


</x-play-layout>
