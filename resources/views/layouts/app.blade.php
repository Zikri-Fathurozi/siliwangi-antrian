<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Language" content="en" />
    <meta name="msapplication-TileColor" content="#2d89ef">
    <meta name="theme-color" content="#4188c9">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <link rel="icon" href="./favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ URL::to('/') }}">
    <meta name="web-socket" content="{{ config('app.websocket') }}">
    <meta name="service-key" content="{{ config('app.service-key') }}">

    <title>{{ config('antrian.header.name') }}</title>

    <!-- Scripts -->
    <script src="{{ asset(mix('js/app.js')) }}" defer></script>
    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('css/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <!-- Styles -->
    <link href="{{ asset(mix('css/app.css')) }}" rel="stylesheet">


    <style>
      html, body {
        overflow: auto!important;
      }
    </style>
</head>

<body class="">
    <div class="page" id="app">
      <div class="page-main">
        <div class="header py-4 cstm_header">
          <div class="container">
            <div class="d-flex">
              <a class="header-brand" href="{{ route('home')}}">
                <img src="{{asset('logo.png')}}" class="header-brand-img">
                {{ config('antrian.header.name') }}
              </a>

              <div class="d-flex order-lg-2 ml-auto">
                @include('layouts.app.notification')
                <div class="dropdown">
                  @include('layouts.app.profile')
                </div>
              </div>
              <a href="#" class="header-toggler d-lg-none ml-3 ml-lg-0" data-toggle="collapse" data-target="#headerMenuCollapse">
                <span class="header-toggler-icon"></span>
              </a>
            </div>
          </div>
        </div>

        <!-- menu navigation -->
		    @include('layouts.app.menu')

        <div class="my-3 my-md-12">
          <div class="container-fluid">

            @yield('content')

          </div>
        </div>
      </div>

      <!-- footer -->
	  @include('layouts.app.footer')
	  @yield('register_script')
</body>
</html>
