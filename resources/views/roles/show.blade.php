@extends('layouts.app')

@section('content')
    <section class="content-header">
        <a href="{!! route('roles.index') !!}" class="btn btn-primary pull-right">Volver</a>
        <h1>
            Rol
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('roles.show_fields')
                </div>
            </div>
        </div>
        <h4>Permisos Asociados</h4>
        <div class="box box-success">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('roles.show_permissions')
                </div>
            </div>
        </div>
    </div>
@endsection
