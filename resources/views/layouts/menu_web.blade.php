
    <nav>
        <ul>
            @if (isset($menu))
                @foreach ($menu as $element)
                	@if ($element->slug != 'terminos-y-condiciones')
    	                <li>
    	                    <a href="{{ route('home.show', $element->slug) }}">{{ $element->title }}</a>
    	                </li>
                	@endif
                @endforeach
            @endif

            @if (! Auth::check())
                @if (Request::is('company*'))
                    <li><a href="#" data-toggle="modal" data-target="#loginModal">{{ mb_strtoupper(trans('app.common.init_session')) }}</a></li>
                @else
                    <li><a href="{{ route('login') }}">{{ mb_strtoupper(trans('app.common.init_session')) }}</a></li>
                @endif
                <li><a href="{{ route('home.select_register_type') }}">{{ mb_strtoupper(trans('app.common.register')) }}</a></li>
            @else
                <li><a href="{{ route('home') }}">{{ mb_strtoupper(trans('app.common.control_panel')) }}</a></li>
                <li><a href="{{ route('exit') }}">{{ mb_strtoupper(trans('app.common.close_session')) }}</a></li>
            @endif

        </ul>
    </nav>