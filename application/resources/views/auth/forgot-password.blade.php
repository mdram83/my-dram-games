<x-app-layout>

    <x-template.normal-breadcrumb title="Password Reset" />

    <!-- Login Section Begin -->
    <section class="login spad">
        <div class="container">

            <div class="row">
                <div class="col-lg-6">
                    <div class="login__form">

                        <!-- Session Status -->
                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <h3>Forgot Password?</h3>
                        <p class="text-[15px] text-white pb-2">{{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}</p>
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <!-- Email Address -->
                            <div class="input__item">
                                <x-input-label for="email" class="collapse" :value="__('Email')" />
                                <input id="email" type="email" name="email" placeholder="Email address" value="{{ old('email') }}" required autofocus>
                                <span class="icon_mail"></span>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <button type="submit" class="site-btn">{{ __('Email Password Reset Link') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-app-layout>
