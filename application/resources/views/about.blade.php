<x-app-layout>

    <!-- Breadcrumb Begin -->
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="{{ route('home') }}"><i class="fa fa-home"></i> Home</a>
                        <span>About</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <div class="container text-white">

        <x-template.about-header value="About this website" />
        <x-template.about-paragraph value="
            My Games is online board games website that let you play selected board games with other players.
        "/>
        <x-template.about-paragraph value="
            This is also my learning project to develop programming skills.
        "/>

        <x-template.about-header value="Terms of use" />
        <x-template.about-paragraph value="
            I will do my best to give you most enjoyable experience and to avoid issues when using the app.
        "/>
        <x-template.about-paragraph value="
            Still it is also my playground, please consider things may go wrong (hope not) or website may be closed down without prior notice.
        "/>
        <x-template.about-paragraph value="
            Feel free to use the website at your convenience.
        "/>

        <x-template.about-header value="Privacy" />
        <x-template.about-paragraph value="
            This website is using cookies for core functionalities only.
        "/>
        <x-template.about-paragraph value="
            I am processing your personal data for personal and domestic purposes, not related with professional or marketing purposes.
            Still I will take appropriate measures so that personal data you provide (e.g. email address) is safe.
            Please do not store your personal data in your username or within messages posted on the website as they may be visible to other website visitors.
        "/>
        <x-template.about-paragraph value="
            For any questions or issues please contact me: michal dot dramowicz at gmail dot com
        "/>

        <p>&nbsp;</p>
    </div>

</x-app-layout>
