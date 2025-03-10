<!-- Id Field 
<div class="form-group">
    {!! Form::label('id', trans('app.common.id') . ':') !!}
    <p>{!! $user->id !!}</p>
</div>-->

<!-- Name Field -->
<div class="form-group">
    {!! Form::label('name', trans('app.common.name') . ':') !!}
    <p>{!! $user->name !!}</p>
</div>

<!-- Username Field -->
<div class="form-group">
    {!! Form::label('username', trans('app.common.username') . ':') !!}
    <p>{!! $user->username !!}</p>
</div>

<!-- Email Field -->
<div class="form-group">
    {!! Form::label('email', trans('app.common.email') . ':') !!}
    <p>{!! $user->email !!}</p>
</div>

<!-- Password Field 
<div class="form-group">
    {!! Form::label('password', trans('app.common.password') . ':') !!}
    <p>{!! $user->password !!}</p>
</div>-->

<!-- Remember Token Field 
<div class="form-group">
    {!! Form::label('remember_token', 'Remember Token:') !!}
    <p>{!! $user->remember_token !!}</p>
</div>-->

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', trans('app.common.created_at') . ':') !!}
    <p>{!! $user->created_at->format('d-m-Y H:i:s') !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', trans('app.common.updated_at') . ':') !!}
    <p>{!! $user->updated_at->format('d-m-Y H:i:s') !!}</p>
</div>


