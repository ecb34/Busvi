<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', trans('app.common.name') . ': *') !!}
    {!! Form::text('name', old('name'), ['class' => 'form-control', 'required' => 'required']) !!}
</div>

<!-- Surame Field -->
<div class="form-group col-sm-6">
    {!! Form::label('surname', trans('app.common.surname') . ': *') !!}
    {!! Form::text('surname', old('surname'), ['class' => 'form-control', 'required' => 'required']) !!}
</div>

<!-- Username Field -->
<div class="form-group col-sm-6">
    {!! Form::label('username', trans('app.common.username') . ': *') !!}
    {!! Form::text('username', old('username'), ['class' => 'form-control', 'required' => 'required']) !!}
</div>

@if (session('type_user'))
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

    <!-- Birthday Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('birthday', trans('app.common.date_birth') . ':') !!}
        <a href="#" data-toggle="tooltip" data-placement="top" title="{{ trans('app.common.tooltip_birthday') }}">
            <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
        </a>
        <div class='input-group date' id='datetimepicker1'>
            {!! Form::text('birthday', old('birthday'), ['class' => 'form-control']) !!}
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>
    </div>

    <!-- Genere Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('genere', trans('app.common.genere') . ':') !!}
        <div class="row">
            <div class="col-xs-4">
                <input class="form-check-input" type="radio" name="genere" id="genere1" value="1">
                {!! Form::label('genere1', trans('app.common.man')) !!}
            </div>
            <div class="col-xs-4">
                <input class="form-check-input" type="radio" name="genere" id="genere2" value="0">
                {!! Form::label('genere2', trans('app.common.woman')) !!}
            </div>
            <div class="col-xs-4">
                <input class="form-check-input" type="radio" name="genere" id="genere3" value="2">
                {!! Form::label('genere3', trans('app.common.others')) !!}
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
@endif

<!-- Phone Field -->
<div class="form-group col-sm-6">
    {!! Form::label('phone', trans('app.common.phone') . ': *') !!}
    {!! Form::text('phone', old('phone'), ['class' => 'form-control', 'required' => 'required']) !!}
</div>

<!-- Email Field -->
<div class="form-group col-sm-6">
    {!! Form::label('email', trans('app.common.email') . ': *') !!}
    {!! Form::email('email', null, ['class' => 'form-control', 'required' => 'required']) !!}
</div>

<!-- Password Field -->
<div class="form-group col-sm-6">
    {!! Form::label('password', trans('app.common.password') . ': *') !!}
    {!! Form::password('password', ['class' => 'form-control', 'required' => 'required']) !!}
    <p class="text-info">
    	<small>
    		5 carácteres como mínimo
    	</small>
    </p>
</div>

<!-- Password Field -->
<div class="form-group col-sm-6">
    {!! Form::label('confirm_password', trans('app.common.confirm_password') . ': *') !!}
    {!! Form::password('password_confirmation', ['class' => 'form-control', 'required' => 'required']) !!}
</div>

@if (session('type_user'))
    {!! Form::hidden('type_user', 'user') !!}
@else
    <!-- Roles Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('roles', 'Roles:') !!}
        {!! Form::select('roles', ['superadmin' => 'SuperAdmin', 'operator' => 'Operadora'], old('roles'), ['class' => 'form-control', 'placeholder' => trans('app.common.placeholder_roles'), 'name' => 'roles', 'required' => 'required']) !!}
    </div>
@endif

<!-- Img Field -->
<div class="form-group col-sm-6">
    {!! Form::label('img', trans('app.common.avatar') . ':') !!}
    {!! Form::file('img', ['accept'=>"image/x-png,image/gif,image/jpeg"]) !!}
    <p class="text-info">
        <small>Recomendamos tamaños cuadrados de un máximo de 400px x 400px en JPG</small>
    </p>
    {{-- {!! Form::file('img', null, ['class' => 'form-control', 'required' => 'required']) !!} --}}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    <?php $url = url()->previous(); ?>
    @if (session()->has('back'))
        <?php $url = route(session('back')); ?>
    @endif
    {!! Form::submit(trans('app.common.save'), ['class' => 'btn btn-primary']) !!}
    <a href="{!! $url !!}" class="btn btn-default pull-right">{{ trans('app.common.cancel') }}</a>
</div>