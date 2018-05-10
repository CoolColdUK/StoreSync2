<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'StoreSync') }}</title>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                            <span class="sr-only">Toggle Navigation</span>
                            <span class="icon-bar">1</span>
                            <span class="icon-bar">2</span>
                            <span class="icon-bar">3</span>
                        </button>
                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                            {{ config('app.name', 'Laravel') }}
                        </a>
                </div>
                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                    Etsy <span class="caret"></span>
                                </a> @guest @else
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('etsy') }}">Stores List</a>
                                </li>
                                @if(Session::has('menu.etsy.stores') && !empty(Session::get('menu.etsy.stores'))) @forelse(Session::get('menu.etsy.stores')
                                as $m)
                                <li>
                                    <a href="{{ route('etsy.store',['store'=>$m]) }}">
                                            {{$m}}
                                        </a>
                                </li>
                                @empty @endforelse @endif
                                <li>&nbsp;</li>
                                <li>
                                    <a href="{{ route('etsy.link') }}">Add Store</a>
                                </li>
                            </ul>
                            @endguest
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                    pInterest <span class="caret"></span>
                                </a> @guest @else
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('pinterest') }}">Account List</a>
                                </li>
                                @if(Session::has('menu.pinterest.accounts') && !empty(Session::get('menu.pinterest.accounts'))) @forelse(Session::get('menu.pinterest.accounts')
                                as $m)
                                <li>
                                    <a href="{{ route('pinterest.account',['accounts'=>$m]) }}">
                                            {{$m}}
                                        </a>
                                </li>
                                @empty @endforelse @endif
                                <li>&nbsp;</li>
                                <li>
                                    <a href="{{ route('pinterest.link') }}">Add Account</a>
                                </li>
                            </ul>
                            @endguest
                        </li>
                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @guest
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                        @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                   document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">Section</div>
                        <div class="panel-body">
                            @yield('section')
                        </div>
                        <div class="panel-heading">Action</div>
                        <div class="panel-body">
                            @yield('action')
                        </div>
                        <div class="panel-heading">@yield('content_title') </div>
                        <div class="panel-body">
                            @yield('content')
                        </div>

                        <div class="panel-heading">@yield('list1_title') </div>
                        <div class="panel-body">
                            @yield('list1')
                        </div>

                        <div class="panel-heading">@yield('list2_title') </div>
                        <div class="panel-body">
                            @yield('list2')
                        </div>

                        <div class="panel-heading">@yield('list3_title') </div>
                        <div class="panel-body">
                            @yield('list3')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>