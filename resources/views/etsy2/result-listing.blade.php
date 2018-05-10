@extends('etsy2.app') 
 
@section('content1')

<div class="panel-heading">Download Listings</div>
<div class="panel-body">
    <table width="100%">
        <tr>
            <td>Active</td>
            <td>{{$active}}</td>
        </tr>
        <tr>
            <td>Inactive</td>
            <td>{{$inactive}}</td>
        </tr>
        <tr>
            <td>Draft</td>
            <td>{{$draft}}</td>
        </tr>
        <tr>
            <td>Expired</td>
            <td>{{$expired}}</td>
        </tr>
    </table>
</div>
@endsection
