@extends('etsy2.app') 
 
@section('content1')
@if(isset($etsy_name_list) && !empty($etsy_name_list))
<div class="panel-heading">Etsy Store</div>
<div class="panel-body">
    <table width="100%">
        @forelse ($etsy_name_list as $store)
        <tr>
            <td>
                        {{ $store }}
            </td>

            <td>
                    <a href="{{ route('etsy2.unlink',['etsyStore'=>$store]) }}">
                            Unlink
                        </a>
                </td>

            <td>
                <a href="{{ route('etsy2.download',['etsyStore'=>$store]) }}">
                        Download
                    </a>
            </td>

            <td>

        {{ Form::open(array('url' => route('etsy2.upload',['etsyStore'=>$store]),'files'=>'true'))}}
        {{ Form::file('csv')}}
        {{ Form::submit('Upload File')}}
        {{ Form::close()}}
            </td>

        </tr>
        @empty
        <tr>
            <td>No stores linked</td>
        </tr>
        @endforelse
    </table>
</div>
@endif
@endsection


@section('content2')
@if(isset($pinterest_name_list) && !empty($pinterest_name_list))
<div class="panel-heading">Pinterest account</div>
<div class="panel-body">
    <table width="100%">
        @forelse ($pinterest_name_list as $acc)
        <tr>
            <td>
                        {{ $acc }}
            </td>

            <td>
                <a href="{{ route('pinterest2.unlink',['pinterestAccount'=>$acc]) }}">
                        Unlink
                    </a>
            </td>
        </tr>
        @empty
        <tr>
            <td>No account linked</td>
        </tr>
        @endforelse
    </table>
</div>
@endif
@endsection