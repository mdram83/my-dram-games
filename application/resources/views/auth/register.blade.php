@php($redirect = request()->query('redirect') ?? null)

<x-app-layout>

    <x-template.normal-breadcrumb title="Sign Up" subtitle="Welcome to board gaming website." />

    <!-- Signup Section Begin -->
    <section class="signup spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="login__form">
                        <h3>Sign Up</h3>

                        @php($betaRegistrationCode = config('auth.beta_registration_code'))

                        @isset($betaRegistrationCode)
                        <p class="text-white">Registration is currently open for beta users only</p>
                        @endisset

                        <form method="POST" action="{{ route('register', ['redirect' => $redirect]) }}">
                            @csrf

                            <!-- Email -->
                            <div class="input__item">
                                <x-input-label for="email" class="collapse" :value="__('Email')" />
                                <input id="email" type="email" name="email" placeholder="Email address" value="{{ old('email') }}" required autocomplete="username">
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                <span class="icon_mail"></span>
                            </div>

                            <!-- Name -->
                            <div class="input__item">
                                <x-input-label for="name" class="collapse" :value="__('Name')" />
                                <input id="name" type="text" name="name" placeholder="Your Name" value="{{ old('name') }}" required autofocus autocomplete="name">
                                <span class="icon_profile"></span>
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Password -->
                            <div class="input__item">
                                <x-input-label for="password" class="collapse" :value="__('Password')" />
                                <input id="password" type="password" name="password" placeholder="Password" required autocomplete="new-password">
                                <span class="icon_lock"></span>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirm Password -->
                            <div class="input__item">
                                <x-input-label for="password_confirmation" class="collapse" :value="__('Confirm Password')" />
                                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password">
                                <span class="icon_lock"></span>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>

                            @isset($betaRegistrationCode)
                            <!-- Beta Section -->
                            <div class="input__item">
                                <x-input-label for="beta_registration_code" class="collapse" :value="__('Beta Registration Code')" />
                                <input id="beta_registration_code" type="password" name="beta_registration_code" placeholder="Beta Registration Code" required autocomplete="beta_registration_code">
                                <span class="icon_lock"></span>
                                <x-input-error :messages="$errors->get('beta_registration_code')" class="mt-2" />
                            </div>
                            @endisset

                            <p class="text-white">By registering you confirm that you've read and agree with <a href="{{ route('about') }}" class="underline">Terms & Privacy</a></p>

                            <button type="submit" class="site-btn">{{ __('Register') }}</button>

                        </form>
                        <h5>Already have an account? <a href="{{ route('login', ['redirect' => $redirect]) }}">Log In!</a></h5>
                    </div>
                </div>

                <div class="col-lg-6">
                    {{--<!-- Social login -->
                    <div class="login__social__links">
                        <h3>Login With:</h3>
                        <ul>
                            <li><a href="#" class="facebook"><i class="fa fa-facebook"></i> Sign in With Facebook</a>
                            </li>
                            <li><a href="#" class="google"><i class="fa fa-google"></i> Sign in With Google</a></li>
                            <li><a href="#" class="twitter"><i class="fa fa-twitter"></i> Sign in With Twitter</a></li>
                        </ul>
                    </div>--}}
                </div>

            </div>
        </div>
    </section>
    <!-- Signup Section End -->

</x-app-layout>
