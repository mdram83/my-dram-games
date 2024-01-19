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

                        <h3>Set New Password</h3>

                        <form method="POST" action="{{ route('password.store') }}">
                            @csrf

                            <!-- Password Reset Token -->
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <!-- Email Address -->
                            <div class="input__item">
                                <x-input-label for="email" class="collapse" :value="__('Email')" />
                                <input id="email" type="email" name="email" placeholder="Email address" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
                                <span class="icon_mail"></span>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
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

                            <button type="submit" class="site-btn">{{ __('Reset Password') }}</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>













</x-app-layout>
