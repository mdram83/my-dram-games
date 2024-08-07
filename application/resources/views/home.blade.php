<x-app-layout>

    <x-template.hero-section />

    <!-- Product Section Begin -->
    <section class="product spad">
        <div class="container">
            <div class="row">

                <!-- Main Pane -->
                <div class="col-lg-8">
                    <div class="trending__product">

                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="section-title">
                                    <h4>Trending Now</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="btn__all">
                                    <a href="#" class="primary-btn">View All <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>

                        <!-- Index -->
                        <div class="row">
                            @forelse($gameBoxList as $gameBox)
                                <x-template.product-list-single :gameBox="$gameBox" />
                            @empty
                                <p>No Games Available</p>
                            @endforelse
                        </div>

                    </div>
                </div>

                <!-- Side Bar -->
                <div class="col-lg-4 col-md-6 col-sm-8">
                    <div class="product__sidebar">

                        <!-- Products -->
                        <div class="product__sidebar__view">

                            <div class="section-title">
                                <h5>Top Views</h5>
                            </div>

                            <ul class="filter__controls">
                                <li class="active" data-filter="*">Day</li>
                                <li data-filter=".week">Week</li>
                                <li data-filter=".month">Month</li>
                                <li data-filter=".years">Years</li>
                            </ul>

                            <div class="filter__gallery">
                                <!-- Index -->
                                <div class="product__sidebar__view__item set-bg mix day years"
                                     data-setbg="{{ asset('img/tmp/sidebar/tv-1.jpg') }}">
                                    <div class="ep">18 / ?</div>
                                    <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                    <h5><a href="#">Boruto: Naruto next generations</a></h5>
                                </div>
                                <div class="product__sidebar__view__item set-bg mix month week"
                                     data-setbg="{{ asset('img/tmp/sidebar/tv-2.jpg') }}">
                                    <div class="ep">18 / ?</div>
                                    <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                    <h5><a href="#">The Seven Deadly Sins: Wrath of the Gods</a></h5>
                                </div>
                                <div class="product__sidebar__view__item set-bg mix week years"
                                     data-setbg="{{ asset('img/tmp/sidebar/tv-3.jpg') }}">
                                    <div class="ep">18 / ?</div>
                                    <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                    <h5><a href="#">Sword art online alicization war of underworld</a></h5>
                                </div>
                                <div class="product__sidebar__view__item set-bg mix years month"
                                     data-setbg="{{ asset('img/tmp/sidebar/tv-4.jpg') }}">
                                    <div class="ep">18 / ?</div>
                                    <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                    <h5><a href="#">Fate/stay night: Heaven's Feel I. presage flower</a></h5>
                                </div>
                                <div class="product__sidebar__view__item set-bg mix day"
                                     data-setbg="{{ asset('img/tmp/sidebar/tv-5.jpg') }}">
                                    <div class="ep">18 / ?</div>
                                    <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                    <h5><a href="#">Fate stay night unlimited blade works</a></h5>
                                </div>
                            </div>
                        </div>

                        <!-- Comments Section -->
                        <div class="product__sidebar__comment">

                            <div class="section-title">
                                <h5>New Comment</h5>
                            </div>

                            <!-- Index -->
                            <div class="product__sidebar__comment__item">
                                <div class="product__sidebar__comment__item__pic">
                                    <img src="{{ asset('img/tmp/sidebar/comment-1.jpg') }}" alt="">
                                </div>
                                <div class="product__sidebar__comment__item__text">
                                    <ul>
                                        <li>Active</li>
                                        <li>Movie</li>
                                    </ul>
                                    <h5><a href="#">The Seven Deadly Sins: Wrath of the Gods</a></h5>
                                    <span><i class="fa fa-eye"></i> 19.141 Viewes</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- Product Section End -->

</x-app-layout>
