<x-errors-layout>


    <div class="h-[61px] bg-[rgb(7,7,32)]"></div>
    <x-template.normal-breadcrumb title="{{ $__env->yieldContent('code') }} | {{ $__env->yieldContent('title') }}" subtitle="{{ $__env->yieldContent('message') ?: 'Something went wrong.' }}" />

    <section class="login spad sm:-mt-[40px] -mt-[100px] pb-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="login__register">
                        <h3>Where do you want to go?</h3>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="login__register">
                        <a href="javascript:history.back()" class="primary-btn mr-2 mb-2">Go Back</a>
                        <a href="/" class="primary-btn">Home Page</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-errors-layout>
