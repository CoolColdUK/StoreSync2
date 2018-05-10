@extends('layouts.base') 

@section('main')
<div class="panel-heading">Welcome</div>

<div class="panel-body">
	@guest Please login @else Welcome {{ Auth::user()->name }} @endguest
</div>
@endsection