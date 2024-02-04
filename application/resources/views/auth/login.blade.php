@php($redirect = request()->query('redirect') ?? null)

<x-app-layout>

    <x-template.normal-breadcrumb title="Login" subtitle="Welcome to the official Anime blog." />

    <!-- Login Section Begin -->
    <section class="login spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="login__form">

                        <!-- Session Status -->
                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <h3>Login</h3>
                        <form method="POST" action="{{ route('login', ['redirect' => $redirect]) }}">
                            @csrf

                            <!-- Email Address -->
                            <div class="input__item">
                                <x-input-label for="email" class="collapse" :value="__('Email')" />
                                <input id="email" type="email" name="email" placeholder="Email address" value="{{ old('email') }}" required autofocus autocomplete="username">
                                <span class="icon_mail"></span>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Password -->
                            <div class="input__item">
                                <x-input-label for="password" class="collapse" :value="__('Password')" />
                                <input id="password" type="password" name="password" placeholder="Password" required autocomplete="current-password">
                                <span class="icon_lock"></span>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Remember Me -->
                            <div>
                                <label for="remember_me" class="inline-flex items-center">
                                    <input id="remember_me" type="checkbox" class="rounded accent-[#e53637]" name="remember">
                                    <span class="ms-2 text-[15px] text-white">{{ __('Remember me') }}</span>
                                </label>
                            </div>

                            <button type="submit" class="site-btn">{{ __('Log in') }}</button>

                        </form>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forget_pass">{{ __('Forgot your password?') }}</a>
                        @endif


                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="login__register">
                        <h3>Dont’t Have An Account?</h3>
                        <a href="{{ route('register', ['redirect' => $redirect]) }}" class="primary-btn">Register Now</a>
                    </div>
                </div>
            </div>

            <div class="login__social">
                <div class="row d-flex justify-content-center">
                    <div class="col-lg-6">
                        <div class="login__social__links">
                            <span>or</span>
                            <ul>
                                <li><a href="#" class="facebook"><i class="fa fa-facebook"></i> Sign in With
                                        Facebook</a></li>
                                <li><a href="#" class="google"><i class="fa fa-google"></i> Sign in With Google</a></li>
                                <li><a href="#" class="twitter"><i class="fa fa-twitter"></i> Sign in With Twitter</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <!-- Login Section End -->

</x-app-layout>
