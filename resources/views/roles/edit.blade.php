@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
           {!! $role->name!!}
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Datos</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
              </button>
              <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
          </div>
           <div class="box-body">
               <div class="row">
                   {!! Form::model($role, ['route' => ['roles.update', $role->id], 'method' => 'patch', 'id' => 'roleForm']) !!}

                        @include('roles.fields')

               </div>
           </div>
       </div>      
        <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Permisos</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('roles.permissions')
                </div>
                <!-- Submit Field -->
                <div class="form-group col-sm-12">
                    {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
                    <a href="{!! route('roles.index') !!}" class="btn btn-default">Cancelar</a>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
   </div>
@endsection