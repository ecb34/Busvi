
    <div class="row row-services-items">
        <div class="col-xs-12 col-sm-6">
            @foreach ($services as $service)                
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('services[]', $service->id) !!}
                        {{ $service->name }}
                    </label>
                </div>
                @if ($loop->index == ((int)(count($services) / 2) - 1))
                    </div>
                    <div class="col-xs-12 col-sm-6">
                @endif
            @endforeach
        </div>
    </div>