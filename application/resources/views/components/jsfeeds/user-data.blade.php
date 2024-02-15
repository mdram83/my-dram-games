@php use Illuminate\Support\Facades\Auth; @endphp

<script>
    window.MyDramGames = {...window.MyDramGames,
        'user': {
            'auth': {{ Auth::check() ? 'true' : 'false' }},
            'username': @if(Auth::check()) '{{ Auth::getUser()->name }}' @else {{ 'undefined' }} @endif,
        }
    }
</script>
