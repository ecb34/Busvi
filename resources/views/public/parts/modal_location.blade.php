
        <div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    {!! Form::open(['route' => ['home.getLocation'], 'id' => 'getLocation', 'method' => 'POST']) !!}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="locationModalLabel">{{ trans('app.common.tell_us_where_you_are') }}</h4>
                        </div>
                        <div class="modal-body">
                            {!! Form::text('address', null, ['class' => 'form-control', 'placeholder' => 'Indica tu dirección']) !!}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('app.cerrar') }}</button>
                            <button type="submit" class="btn btn-primary">{{ trans('app.common.send') }}</button>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>