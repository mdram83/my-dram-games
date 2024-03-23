<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="description" content="Anime Template">
    <meta name="keywords" content="Anime, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="m-0 p-0 bg-gray-200">

<!-- Page Loading... -->
@push('custom-scripts')
    @vite('resources/js/template/play/loading.jsx')
@endpush
<div id="loading" class="fixed w-full h-full inset-y-0 inset-x-0 z-[999999] bg-gray-800">
    <div class="flex justify-center items-center w-full h-full">
        <div class="font-['Oswald'] text-6xl sm:text-8xl text-neutral-300 animate-pulse">
            Loading...
        </div>
    </div>
</div>


{{ $slot }}


<!-- Flash Message -->
<x-flash/>


<x-jsfeeds.route-names />
<x-jsfeeds.user-data />
@stack('custom-scripts')

</body>
</html>
