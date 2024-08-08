@props(['gameBox'])

<div class="col-lg-4 col-md-6 col-sm-6">
    <div class="product__item">

        <!-- Product Image Area -->
        <div class="product__item__pic set-bg" data-setbg="{{ asset('img/games/' . $gameBox['slug'] . '.jpg') }}">

            <a href="{{ route('games.show', $gameBox['slug']) }}" class="w-full h-full">
                <div class="link"></div>
            </a>

            @if($gameBox['isActive'] === true)
                <div class="ep">
                    <a href="{{ route('games.show', $gameBox['slug']) }}" class="text-white">PLAY</a>
                </div>
            @endif

            {{--<!-- Comments and Views -->
            <div class="comment"><i class="fa fa-comments"></i> 11</div>
            <div class="view"><i class="fa fa-eye"></i> 9141</div>--}}

        </div>
        <!-- End Product Image Area -->

        <div class="product__item__text">

            {{--<!-- Tags -->
            <ul>
                <li>{{ $gameBox['isActive'] ? 'Active' : 'Inactive' }}</li>
                <li>Movie</li>
            </ul>
            <!-- End Tags -->--}}

            <h5><a href="{{ route('games.show', $gameBox['slug']) }}">{{ $gameBox['name'] }}</a></h5>
        </div>
    </div>
</div>
