<!-- Hero Section Begin -->
<section class="hero">
    <div class="container">
        <div class="hero__slider owl-carousel">

            <x-template.hero-item label="News"
                                  title="Welcome!"
                                  description="We're live! Play your favorite games online"
                                  img="{{ asset('img/template/gaming-room.jpg') }}" />

            <x-template.hero-item label="Paper Game"
                                  title="Tic-Tac-Toe game live!"
                                  description="Play famous tic-tac-toe game"
                                  img="{{ asset('img/games/tic-tac-toe.jpg') }}"
                                  link="/games/tic-tac-toe"
                                  button="Play Now" />

            <x-template.hero-item label="Playing Cards"
                                  title="Thousand Schnapsen"
                                  description="'1000' card games now available"
                                  img="{{ asset('img/games/thousand.jpg') }}"
                                  link="/games/thousand"
                                  button="Play Now" />

            <x-template.hero-item label="Upcoming Game"
                                  title="Netrunners coming soon!"
                                  description="Prepare for a cyber style adventure"
                                  img="{{ asset('img/games/netrunners.jpg') }}" />

        </div>
    </div>
</section>
<!-- Hero Section End -->
