@extends('etsy2.app') 
 
@section('content1')
@if(isset($store_name_list) && !empty($store_name_list))
<div class="panel-heading">Store Listings</div>
<div class="panel-body">
    <table width="100%">
        @forelse ($etsy_name_list as $store)
        <tr>
            <td>
                <a href="{{ route('etsy2.store',['etsyStore'=>$store]) }}">
                        {{ $store }}
                    </a>
            </td>

            <td>
                <a href="{{ route('etsy2.download',['etsyStore'=>$store]) }}">
                        Download
                    </a>
            </td>

            <td>
                <a href="{{ route('etsy2.export',['etsyStore'=>$store]) }}">
                        Export
                    </a>
            </td>

            <td>
                <a href="{{ route('etsy2.import',['etsyStore'=>$store]) }}">
                        Import
                    </a>
            </td>

            <td>
                <a href="{{ route('etsy2.upload',['etsyStore'=>$store]) }}">
                        Upload
                    </a>
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