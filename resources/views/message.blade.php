@extends('layouts.base') 
@section('main')
<div class="panel-heading">
    {{$title?:"Error"}}
</div>
@if(isset($message))
<div class="panel-body">
    {{$message?:"Something went wrong and we don't know what. Will you let us know?"}}
</div>
@endif

@if(isset($grid))
<div class="panel-body">
    <table width="50%">
    @foreach ($grid as $k => $v)
        <tr><td width="20%">{{ $k }}</td><td> {{ $v }}</td></tr>
    @endforeach
    </table>
</div>
@endif

@if(isset($table))
<div class="panel-body">
    <table width="50%">
    @foreach($table as $t)
        <tr>
            @if ($loop->first)
                
                @foreach ($t as $k => $v)
                    <td> {{ $k }}</td>
                @endforeach
                </tr><tr>
            @endif

            @foreach ($t as $k => $v)
                <td> {{ $v }}</td>
            @endforeach
        </tr>
    @endforeach
    </table>
</div>
@endif
@endsection