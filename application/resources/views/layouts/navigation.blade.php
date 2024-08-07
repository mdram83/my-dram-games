<header class="header">
    <div class="container">
        <div class="row">
            <div class="col-lg-2">
                <div class="header__logo">
                    <x-template.logo />
                </div>
            </div>
            <div class="col-lg-8">
                <div class="header__nav">
                    <nav class="header__menu mobile-menu">
                        <ul>
                            <li class="active"><a href="/">Homepage</a></li>

                            {{--<!-- Categories Section -->
                            <li><a href="./categories.html">Categories <span class="arrow_carrot-down"></span></a>
                                <ul class="dropdown">
                                    <li><a href="./categories.html">Categories</a></li>
                                    <li><a href="./anime-details.html">Anime Details</a></li>
                                    <li><a href="./anime-watching.html">Anime Watching</a></li>
                                    <li><a href="./blog-details.html">Blog Details</a></li>
                                    <li><a href="./signup.html">Sign Up</a></li>
                                    <li><a href="./login.html">Login</a></li>
                                </ul>
                            </li>--}}

                            <li><a href="{{ route('about') }}">About</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="header__right">

                    {{--<!-- Search Section -->--}}
                    {{--<a href="#" class="search-switch"><span class="icon_search"></span></a>--}}

                    @auth
                        <a class="fs1 text-white" href="{{ route('logout') }}" aria-hidden="true" data-icon="&#x51;"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"></a>
                        <form id="logout-form" class="collapse" method="POST" action="{{ route('logout') }}">
                            @csrf
                        </form>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}"><span class="icon_profile"></span></a>
                    @endguest
                </div>
            </div>
        </div>
        <div id="mobile-menu-wrap"></div>
    </div>
</header>
