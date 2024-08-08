<x-app-layout>

    {{--<!-- Breadcrumb Begin -->
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="{{ route('home') }}"><i class="fa fa-home"></i> Home</a>
                        <a href="./categories.html">Categories</a>
                        <span>Romance</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->--}}

    <!-- Anime Section Begin -->
    <section class="anime-details spad">
        <div class="container">
            <div class="anime__details__content">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="anime__details__pic set-bg"
                             data-setbg="{{ asset('img/game-definition/' . $gameBox['slug'] . '.jpg') }}"
                        >
                            <div class="comment"><i class="fa fa-comments"></i> 11</div>
                            <div class="view"><i class="fa fa-eye"></i> 9141</div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="anime__details__text">
                            <div class="anime__details__title">
                                <h3>{{ $gameBox['name'] }}</h3>
                                <span>フェイト／ステイナイト, Feito／sutei naito</span>
                            </div>
                            {{--<div class="anime__details__rating">
                                <div class="rating">
                                    <a href="#"><i class="fa fa-star"></i></a>
                                    <a href="#"><i class="fa fa-star"></i></a>
                                    <a href="#"><i class="fa fa-star"></i></a>
                                    <a href="#"><i class="fa fa-star"></i></a>
                                    <a href="#"><i class="fa fa-star-half-o"></i></a>
                                </div>
                                <span>1.029 Votes</span>
                            </div>--}}
                            <p>{{ $gameBox['description'] }}</p>
                            <div class="anime__details__widget">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <ul>
                                            <li><span>Type:</span> TV Series</li>
                                            <li><span>Studios:</span> Lerche</li>
                                            <li>
                                                <span>No. players:</span> {{ $gameBox['numberOfPlayersDescription'] }}
                                            </li>
                                            <li>
                                                <span>Status:</span> {{ $gameBox['isActive'] ? 'Active' : 'Inactive' }}
                                            </li>
                                            <li><span>Genre:</span> Action, Adventure, Fantasy, Magic</li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <ul>
                                            <li><span>Scores:</span> 7.31 / 1,515</li>
                                            <li><span>Rating:</span> 8.5 / 161 times</li>
                                            <li><span>Duration:</span>
                                                {{ $gameBox['durationInMinutes'] }}
                                                {{ $gameBox['durationInMinutes'] == 1 ? 'minute' : 'minutes'}}
                                            </li>
                                            <li><span>Min. age:</span> {{ $gameBox['minPlayerAge'] }}</li>
                                            <li><span>Views:</span> 131,541</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="anime__details__btn">
                                {{--                                <a href="#" class="follow-btn"><i class="fa fa-heart-o"></i> Follow</a>--}}

                                @if($gameBox['isActive'] === true)

                                    @push('custom-scripts')
                                        @vite('resources/js/template/single/game-invite-controls/index.jsx')
                                    @endpush

                                    <div id="single-game-invite-controls-root"
                                         data-game.box="{{ json_encode($gameBox) }}"
                                         data-game.invite="{{ isset($gameInvite) ? json_encode($gameInvite) : null }}"
                                         data-game.playid="{{ $gamePlayId ?? null }}"
                                         data-game.records="{{ isset($gameRecords) ? json_encode($gameRecords) : null }}"
                                    ></div>

                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{--<!-- Reviews and You might like Sections -->--}}
            {{--<div class="row">
                <div class="col-lg-8 col-md-8">
                    <div class="anime__details__review">
                        <div class="section-title">
                            <h5>Reviews</h5>
                        </div>
                        <div class="anime__review__item">
                            <div class="anime__review__item__pic">
                                <img src="{{ asset('img/tmp/anime/review-1.jpg') }}" alt="">
                            </div>
                            <div class="anime__review__item__text">
                                <h6>Chris Curry - <span>1 Hour ago</span></h6>
                                <p>whachikan Just noticed that someone categorized this as belonging to the genre
                                    "demons" LOL</p>
                            </div>
                        </div>
                        <div class="anime__review__item">
                            <div class="anime__review__item__pic">
                                <img src="{{ asset('img/tmp/anime/review-2.jpg') }}" alt="">
                            </div>
                            <div class="anime__review__item__text">
                                <h6>Lewis Mann - <span>5 Hour ago</span></h6>
                                <p>Finally it came out ages ago</p>
                            </div>
                        </div>
                        <div class="anime__review__item">
                            <div class="anime__review__item__pic">
                                <img src="{{ asset('img/tmp/anime/review-3.jpg') }}" alt="">
                            </div>
                            <div class="anime__review__item__text">
                                <h6>Louis Tyler - <span>20 Hour ago</span></h6>
                                <p>Where is the episode 15 ? Slow update! Tch</p>
                            </div>
                        </div>
                        <div class="anime__review__item">
                            <div class="anime__review__item__pic">
                                <img src="{{ asset('img/tmp/anime/review-4.jpg') }}" alt="">
                            </div>
                            <div class="anime__review__item__text">
                                <h6>Chris Curry - <span>1 Hour ago</span></h6>
                                <p>whachikan Just noticed that someone categorized this as belonging to the genre
                                    "demons" LOL</p>
                            </div>
                        </div>
                        <div class="anime__review__item">
                            <div class="anime__review__item__pic">
                                <img src="{{ asset('img/tmp/anime/review-5.jpg') }}" alt="">
                            </div>
                            <div class="anime__review__item__text">
                                <h6>Lewis Mann - <span>5 Hour ago</span></h6>
                                <p>Finally it came out ages ago</p>
                            </div>
                        </div>
                        <div class="anime__review__item">
                            <div class="anime__review__item__pic">
                                <img src="{{ asset('img/tmp/anime/review-6.jpg') }}" alt="">
                            </div>
                            <div class="anime__review__item__text">
                                <h6>Louis Tyler - <span>20 Hour ago</span></h6>
                                <p>Where is the episode 15 ? Slow update! Tch</p>
                            </div>
                        </div>
                    </div>
                    <div class="anime__details__form">
                        <div class="section-title">
                            <h5>Your Comment</h5>
                        </div>
                        <form action="#">
                            <textarea placeholder="Your Comment"></textarea>
                            <button type="submit"><i class="fa fa-location-arrow"></i> Review</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="anime__details__sidebar">
                        <div class="section-title">
                            <h5>you might like...</h5>
                        </div>
                        <div class="product__sidebar__view__item set-bg"
                             data-setbg="{{ asset('img/tmp/sidebar/tv-1.jpg') }}">
                            <div class="ep">18 / ?</div>
                            <div class="view"><i class="fa fa-eye"></i> 9141</div>
                            <h5><a href="#">Boruto: Naruto next generations</a></h5>
                        </div>
                        <div class="product__sidebar__view__item set-bg"
                             data-setbg="{{ asset('img/tmp/sidebar/tv-2.jpg') }}">
                            <div class="ep">18 / ?</div>
                            <div class="view"><i class="fa fa-eye"></i> 9141</div>
                            <h5><a href="#">The Seven Deadly Sins: Wrath of the Gods</a></h5>
                        </div>
                        <div class="product__sidebar__view__item set-bg"
                             data-setbg="{{ asset('img/tmp/sidebar/tv-3.jpg') }}">
                            <div class="ep">18 / ?</div>
                            <div class="view"><i class="fa fa-eye"></i> 9141</div>
                            <h5><a href="#">Sword art online alicization war of underworld</a></h5>
                        </div>
                        <div class="product__sidebar__view__item set-bg"
                             data-setbg="{{ asset('img/tmp/sidebar/tv-4.jpg') }}">
                            <div class="ep">18 / ?</div>
                            <div class="view"><i class="fa fa-eye"></i> 9141</div>
                            <h5><a href="#">Fate/stay night: Heaven's Feel I. presage flower</a></h5>
                        </div>
                    </div>
                </div>
            </div>--}}
        </div>
    </section>
    <!-- Anime Section End -->

</x-app-layout>
