@extends('layouts.base') 
@section('main')
<!--div class="panel-heading">Section</div>
                <div class="panel-body">
                    @yield('section')
                </div-->
    @include('etsy.menu-section')
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
@endsection