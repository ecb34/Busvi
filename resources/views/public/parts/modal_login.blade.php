
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {!! Form::open(['route' => ['home.login'], 'id' => 'getLocation', 'method' => 'POST']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="locationModalLabel">{{ trans('app.common.init_session') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'E-Mail / Username']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Contraseña']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('app.cerrar') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('app.common.send') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>