<x-join-redirect-layout>

    <div class="h-[61px] bg-[rgb(7,7,32)]"></div>
    <x-template.normal-breadcrumb title="Please wait" subtitle="Joining the game..." />

    <section class="login spad sm:-mt-[40px] -mt-[100px] pb-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
{{--                <div class="col-lg-6">--}}
                    <div class="login__register">
                        <h3 class="w-full text-center">We are redirecting you to game lobby</h3>
                    </div>
                </div>
{{--                <div class="col-lg-6">--}}
{{--                    <div class="login__register">--}}
{{--                        <a href="javascript:history.back()" class="primary-btn mr-2 mb-2">Go Back</a>--}}
{{--                        <a href="/" class="primary-btn">Home Page</a>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
        </div>
    </section>

    @push('custom-scripts')
        @vite('resources/js/src/game-core/game-invite/joinRedirect.jsx')
    @endpush

</x-join-redirect-layout>
