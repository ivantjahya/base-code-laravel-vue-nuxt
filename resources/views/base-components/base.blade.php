<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="cypress" content="Cypress Testing">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/logo-venditore-plus.png') }}"/>

    <title>@yield('title') | {{ config('app.name') }}</title>
    </head>

    <body>
    @section('base')
    <noscript>
        <div class="grid content-center w-full h-full bg-slate-200">
        <div class="flex justify-center">
            <div class="bg-white rounded-lg p-1">
            <div class="m-auto antialiased p-5 text-9xl text-center">&#128245</div>
            <div class="m-auto antialiased pt-5 text-center">Error! JavaScript tidak aktif</div>
            <div class="m-auto antialiased pb-5 text-center">App ini memerlukan JavaScript agar dapat berfungsi</div>
            </div>
        </div>
        </div>
    </noscript>
    <div id="app" class="isolate">
        <cmp-app-set></cmp-app-set>
        @section('body')
        @show
    </div>
    @show

    @section('font')
    @show

    @section('css')
        @vite(['resources/css/app.css'])
    @show

    @section('script')
        @vite(['resources/js/app.js'])
    @show
    </body>
</html>