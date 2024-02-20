@props(['gameBox'])

<div class="col-lg-4 col-md-6 col-sm-6">
    <div class="product__item">

        <div class="product__item__pic set-bg" data-setbg="{{ asset('img/game-definition/' . $gameBox['slug'] . '.jpg') }}">
            @if($gameBox['isActive'] === true)
                <div class="ep">
                    <a href="{{ route('games.show', $gameBox['slug']) }}" class="text-white">PLAY</a>
                </div>
            @endif
            <div class="comment"><i class="fa fa-comments"></i> 11</div>
            <div class="view"><i class="fa fa-eye"></i> 9141</div>
        </div>

        <div class="product__item__text">
            <ul>
                <li>{{ $gameBox['isActive'] ? 'Active' : 'Inactive' }}</li>
                <li>Movie</li>
            </ul>
            <h5><a href="{{ route('games.show', $gameBox['slug']) }}">{{ $gameBox['name'] }}</a></h5>
        </div>
    </div>
</div>
