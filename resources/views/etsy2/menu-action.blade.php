<div class="panel-heading">Action</div>
<div class="panel-body">
    <table width=90%>
        <tr>
            <td width=20%>
                <a href="{{route('etsy2.link')}}">Add etsy store</a>
            </td>
            <td width=20%>
                <a href="{{route('pinterest2.link')}}">Add pinterest</a>
            </td>
        </tr>

        <tr>
            <td colspan=10>
                {{ Form::open(['route' => 'etsy2.keyword'])}}
                <table>
                    <tr>
                        <td>

                            Search terms {{ Form::textarea('keyword', '',['size'=>'30x2'])}}

                        </td>
                        <td>
                            Group {{ Form::textarea('groups', '',['size'=>'30x2'])}}
                        </td>
                        <td>
                            {{ Form::submit('Keyword Search')}}
                        </td>
                    </tr>
                </table>
                {{ Form::close()}}
            </td>
        </tr>
        <tr>
            <td colspan=10>
                {{ Form::open(['route' => 'etsy2.shops'])}}
                <table>
                    <tr>
                        <td>
                            {{ Form::text('shops')}}
                        </td>
                        <td>
                            {{ Form::submit('Other shop listings')}}
                        </td>
                    </tr>
                </table>
                {{ Form::close()}}
        </tr>
    </table>
</div>