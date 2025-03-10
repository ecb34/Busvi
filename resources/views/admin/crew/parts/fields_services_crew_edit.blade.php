<!-- Services Field -->
<div class="form-group">
    {!! Form::label('service_id', 'Services:') !!}
    <div class="row">
            @foreach ($services as $service)
                <div class="checkbox col-xs-12 col-sm-6" style="margin-top: 0 !important;">
                    <label>
                        {!! Form::checkbox('services[]', $service->id, $user->services->contains('service_id', $service->id)) !!}
                        {{ $service->name }}
                    </label>
                </div>
            @endforeach
    </div>
</div>