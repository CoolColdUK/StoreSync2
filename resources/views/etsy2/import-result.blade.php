@extends('etsy2.app') 
 
@section('content1')

<div class="panel-heading">Import listing results</div>
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
@endsection
