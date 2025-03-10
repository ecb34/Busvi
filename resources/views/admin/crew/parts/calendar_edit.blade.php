

<div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header with-border">
                Calendario
                
                @if ((Auth::user()->role == 'crew' && Auth::user()->id == $user->id) || Auth::user()->role == 'admin')
                    <a href="{{ route('crew.blockEvent', $user) }}" class="btn btn-warning pull-right">
                        <i class="fa fa-lock" aria-hidden="true"></i> Bloquear / Desbloquear <i class="fa fa-unlock" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
            <div class="box-body">
                {!! $calendar->calendar() !!}
            </div>
        </div>
    </div>
</div>