<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', trans('app.common.name') . ':') !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
</div>

<!-- Surame Field -->
<div class="form-group col-sm-6">
    {!! Form::label('surname', trans('app.common.surname') . ':') !!}
    {!! Form::text('surname', null, ['class' => 'form-control', 'required' => 'required']) !!}
</div>

<!-- Username Field -->
<div class="form-group col-sm-6">
    {!! Form::label('username', trans('app.common.username') . ':') !!}
    {!! Form::text('username', null, ['class' => 'form-control', 'required' => 'required', 'readonly']) !!}
</div>

<!-- Phone Field -->
<div class="form-group col-sm-6">
    {!! Form::label('phone', trans('app.common.phone') . ':') !!}
    {!! Form::tel('phone', null, ['class' => 'form-control', 'required' => 'required']) !!}
</div>

<!-- Email Field -->
<div class="form-group col-sm-6">
    {!! Form::label('email', trans('app.common.email') . ':') !!}
    {!! Form::email('email', null, ['class' => 'form-control', 'required' => 'required']) !!}
</div>

@if ($role_show)
    <!-- Roles Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('roles', trans('app.common.roles') . ':') !!}
        {!! Form::select('roles', ['superadmin' => 'Superadmin', 'operator' => 'Operadora'], null, ['class' => 'form-control', 'placeholder' => trans('app.admin.users.placeholder_roles'), 'name' => 'roles', 'required' => 'required']) !!}
    </div>
@endif


@if ($user->role == 'user')
    <!-- Address Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('address', trans('app.common.address') . ':') !!}
        {!! Form::text('address', old('address'), ['class' => 'form-control']) !!}
    </div>

    <!-- City Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('city', trans('app.common.city') . ':') !!}
        {!! Form::text('city', old('city'), ['class' => 'form-control']) !!}
    </div>

    <!-- CP Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('cp', trans('app.common.cp') . ':') !!}
        {!! Form::text('cp', old('cp'), ['class' => 'form-control']) !!}
    </div>

    <!-- Calendar Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('birthday', trans('app.common.date_birth') . ':') !!}
        <div class='input-group date' id='datetimepicker1'>
            {!! Form::text('birthday', $birth, ['class' => 'form-control', 'required' => 'required']) !!}
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>
    </div>

    <!-- CP Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('bank_count', trans('app.common.bank_count') . ':') !!}
        {!! Form::text('bank_count', old('bank_count'), ['class' => 'form-control']) !!}
    </div>

@endif

<!-- Logo Field -->
<div class="form-group col-sm-6">
    <div class="row">
        @if ($user->img)
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-12">
                        {!! Form::label('img', trans('app.common.avatar') . ':') !!}
                    </div>
                </div>
            </div>
            <div class="col-xs-4">
                @if ($user->role == 'user')
                    <img src="{{ url('/') . '/img/user/' . $user->img }}" alt="Busvi" class="img-rounded w-100 item-img">
                @else
                    <img src="{{ url('/') . '/img/crew/' . $user->img }}" alt="Busvi" class="img-rounded w-100 item-img">
                @endif
            </div>
        @endif
        <div class="col-xs-6 {{ ($user->img) ? 'hidden' : '' }} add-img">
            <div class="row">
                @if ($user->img)
                    <div class="col-xs-5">
                        <a href="#" class="btn btn-default btn-cancel-img">
                            {{ trans('app.common.cancel') }}
                        </a>
                    </div>
                @endif
                <div class="col-xs-6">
                    {!! Form::file('img', ['class' => 'form-control']) !!}
                    <p class="text-info">
                        <small>Recomendamos tamaños cuadrados de un máximo de 400px x 400px en JPG</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Password -->
<div class="form-group col-sm-6">
    {!! Form::label('roles', 'Contraseña:') !!}
    <p>
        <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#modalEditPass">
            {{ trans('app.admin.users.modify_password') }}
        </a>
    </p>
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
    <a href="{!! str_replace(url('/'), '', url()->previous()) !!}" class="btn btn-default pull-right">
        {{ trans('app.common.cancel') }}
    </a>
</div>
