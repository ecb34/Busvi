
    <div class="col-xs-12 col-sm-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Datos Negocio</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    @include('admin.companies.parts.company_fields_show')
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Marcar como favorito</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <a href="{{ route('companies.setFavourite', $company) }}" class="btn btn-danger">
                            @if ($company->isFavourite()->first())
                                <i class="fa fa-heart-o" aria-hidden="true"></i> Eliminar de Favorito
                            @else
                                <i class="fa fa-heart" aria-hidden="true"></i> Marcar como favorito
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Contacto Administración</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <!-- Email Field -->
                        <div class="form-group col-sm-6">
                            {!! Form::label('email', 'Email:') !!}
                            @if ($company->admin->email)
                                <a href="mailto:{{ $company->admin->email }}">
                                    {{ $company->admin->email }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Servicios</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <!-- Email Field -->
                        <div class="form-group col-sm-12">
                            <ul>
                                @foreach ($company->services as $service)
                                    <li>{{ $service->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Pedir Cita</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 text-center">
                        @if ($company->blocked == 0 && $company->type == 1 && $company->enable_events == 1)
                            <a href="{{ route('calendar.goToCreate', $company->id) }}" class="btn btn-primary btn-lg">
                                Pedir cita <i class="fa fa-calendar"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12">
        <div class="box box-dark">
            <div class="box-header with-border">
                <h3 class="box-title">Imágenes de la Galería</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    @foreach ($images as $image)
                        <div class="col-xs-3">
                            <a data-fancybox="gallery" href="{{ asset('/img/companies/galleries/' . $company->id . '/original/' . $image) }}">
                                <img src="{{ asset('/img/companies/galleries/' . $company->id . '/thumb/' . $image) }}" alt="{{ $company->name_comercial }}" width="100%">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>