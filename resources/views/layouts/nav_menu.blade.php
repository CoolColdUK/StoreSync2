<!-- Left Side Of Navbar -->

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
<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                dropbox <span class="caret"></span>
            </a> @guest @else
    <ul class="dropdown-menu">
        <li>
            <a href="{{ route('dropbox') }}">Account List</a>
        </li>
        @if(Session::has('menu.dropbox.accounts') && !empty(Session::get('menu.dropbox.accounts'))) @forelse(Session::get('menu.dropbox.accounts')
        as $m)
        <li>
            <a href="{{ route('dropbox.account',['accounts'=>$m]) }}">
                        {{$m}}
                    </a>
        </li>
        @empty @endforelse @endif
        <li>&nbsp;</li>
        <li>
            <a href="{{ route('dropbox.link') }}">Add Account</a>
        </li>
    </ul>
    @endguest
</li>


<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                Etsy V2 <span class="caret"></span>
            </a> @guest @else
    <ul class="dropdown-menu">
        <li>
            <a href="{{ route('etsy2') }}">Account List</a>
        </li>
        <li>&nbsp;</li>
        <li>
            <a href="{{ route('etsy2.link') }}">Add Account</a>
        </li>
    </ul>
    @endguest
</li>