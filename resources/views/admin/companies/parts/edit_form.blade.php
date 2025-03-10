
                <div class="col-xs-12 col-sm-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Datos Negocio</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                @include('admin.companies.parts.company_fields_edit')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6">
                    @if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'operator')
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">Datos Negocio</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    @include('admin.companies.parts.company_types')
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Datos Administrador</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                @include('admin.companies.parts.user_fields_edit')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Datos complementarios</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                @include('admin.companies.parts.company_other_fields')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6">
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title">Datos bancarios</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                @include('admin.companies.parts.company_bank_fields')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Datos Estadísticos</h3>
                        </div>
                        <div class="box-body" style="padding-bottom: 20px">
                                
                            <div class="row">
                                <div class="col-xs-3 text-center">
                                    <h4 style="margin-bottom: 4px;">Citas totales</h4>
                                    <h2 style="margin-top: 10px;">{{ $company->events->count() }}</h2>
                                </div>

                                <div class="col-xs-3 text-center">
                                    <h4 style="margin-bottom: 4px;">Próximas citas</h4>
                                    <h2 style="margin-top: 10px;">{{ count($company->future_events()) }}</h2>
                                </div>

                                <div class="col-xs-3 text-center">
                                    <h4 style="margin-bottom: 4px;">Reservas totales</h4>
                                    <h2 style="margin-top: 10px;">{{ $company->reservas->count() }}</h2>
                                </div>

                                <div class="col-xs-3 text-center">
                                    <h4 style="margin-bottom: 4px;">Próximas reservas</h4>
                                    <h2 style="margin-top: 10px;">{{ $company->reservas()->where('fecha', '>', date('Y-m-d'))->count() }}</h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-center">
                                    <h4 style="margin-bottom: 4px;">Clicks web</h4>
                                    <h2 style="margin-top: 10px;">{{ $company->web_counter }}</h2>
                                </div>

                                <div class="col-xs-3 text-center">
                                    <h4 style="margin-bottom: 4px;">Clicks teléfono</h4>
                                    <h2 style="margin-top: 10px;">{{ $company->phone_counter }}</h2>
                                </div>

                                <div class="col-xs-3 text-center">
                                    <h4 style="margin-bottom: 4px;">Clicks email</h4>
                                    <h2 style="margin-top: 10px;">{{ $company->email_counter }}</h2>
                                </div>

                                <div class="col-xs-3 text-center">
                                    <h4 style="margin-bottom: 4px;">Clicks plano</h4>
                                    <h2 style="margin-top: 10px;">{{ $company->map_counter }}</h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-3 text-center">
                                    <h4 style="margin-bottom: 4px;">Visitas</h4>
                                    <h2 style="margin-top: 10px;">{{ $company->visits }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12">
                    <div class="box box-dark">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                Imágenes de la Galería
                            </h3>

                            <a href="#" class="btn btn-primary btn-gallery-modal pull-right">
                                Añadir imagen
                            </a>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <table class="table table-striped table-hover table-responsive data-table imagenes">
                                        <thead>
                                            <th>Nombre</th>
                                            <th width="5%"></th>
                                        </thead>
                                        <tbody>
                                            @foreach ($images as $image_id => $image)
                                            <tr data-id="{{ $image_id }}">
                                                <td>
                                                    <a class="btn btn-xs btn-primary btn-orden" data-accion="bajar-todo" data-name="{{ $image->filename }}" data-id="{{ $image_id }}">
                                                        <i class="fa fa-angle-double-down" aria-hidden="true"></i>
                                                    </a>
                                                    <a class="btn btn-xs btn-primary btn-orden" data-accion="bajar" data-name="{{ $image->filename }}" data-id="{{ $image_id }}">
                                                        <i class="fa fa-angle-down" aria-hidden="true"></i>
                                                    </a>
                                                    <a class="btn btn-xs btn-primary btn-orden" data-accion="subir" data-name="{{ $image->filename }}" data-id="{{ $image_id }}">
                                                        <i class="fa fa-angle-up" aria-hidden="true"></i>
                                                    </a>
                                                    <a class="btn btn-xs btn-primary btn-orden" data-accion="subir-todo" data-name="{{ $image->filename }}" data-id="{{ $image_id }}">
                                                        <i class="fa fa-angle-double-up" aria-hidden="true"></i>
                                                    </a>

                                                    <a class="btn btn-xs btn-primary btn-edit-gallery" data-id="{{ $image_id }}" description="<?=htmlentities($image->description)?>"><i class="fa fa-info-circle" aria-hidden="true"></i> Info</a>
                                                    <a class="btn btn-xs <?=$image->offer ? 'btn-success' : 'btn-default' ?> btn-offer" data-id="{{ $image_id }}"><i class="fa fa-eur" aria-hidden="true"></i> Oferta</a>

                                                    <a href="{{ url('/img/companies/galleries/' . $company->id . '/original/' . $image->filename) }}" target="_blank">
                                                        <img src="{{ asset('/img/companies/galleries/' . $company->id . '/thumb/' . $image->filename) }}" height="80px" style="margin: 0 15px;">
                                                    </a>

                                                    <a href="{{ url('/img/companies/galleries/' . $company->id . '/original/' . $image->filename) }}" target="_blank">
                                                        {{ $image->filename }}
                                                    </a>
                                                    
                                                </td>
                                                <td width="10%" class="text-right">
                                                    <a href="#" class="btn btn-sm text-danger btn-remove-gallery-image" data-name="{{ $image->filename }}">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Field -->
                <div class="form-group col-xs-12">
                    <div class="box box-solid">
                        <div class="box-body">
                            {!! Form::submit('Guardar', ['class' => 'btn btn-primary', 'disabled' => $disabled]) !!}
                            <a href="{!! route('companies.index') !!}" class="btn btn-default pull-right">
                                Cancelar
                            </a>
                            <?php /* 
                            @if ($disabled) 
                                <p class="text-warning">
                                    <small>
                                        Es necesario que existan <a href="{{ route('sectors.index') }}">sectores</a> y <a href="{{ route('services.index') }}">servicios</a> para poder crear un negocio
                                    </small>
                                </p>
                            @endif 
                            */ ?>
                        </div>
                    </div>
                </div>