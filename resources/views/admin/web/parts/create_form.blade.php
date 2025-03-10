
    <!-- Title Field -->
    <div class="form-group col-xs-12">
        {!! Form::label('title', 'Título:') !!}
        {!! Form::text('title', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Slug Field -->
    <div class="form-group col-xs-12">
        {!! Form::label('slug', 'Slug:') !!}
        {!! Form::text('slug', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <!-- Order Field -->
    <div class="form-group col-xs-12">
        {!! Form::label('order', 'Orden:') !!}
        {!! Form::number('order', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>

    <div class="form-group col-xs-12">
        <label style="display: block;">Visibilidad:</label>
        <div class="checkbox" style="display: inline-block; margin-right: 15px;">
            <label><input type="checkbox" name="public" value="1" <?=isset($post) && !is_null($post) && $post->public ? 'checked="checked"' : ''?>> Público</label>
        </div>
        <div class="checkbox" style="display: inline-block; margin-right: 15px;">
            <label><input type="checkbox" name="private" value="1" <?=isset($post) && !is_null($post) && $post->private ? 'checked="checked"' : ''?>> Privado (negocio)</label>
        </div>
        <div class="checkbox" style="display: inline-block; margin-right: 15px;">
            <label><input type="checkbox" name="private_user" value="1" <?=isset($post) && !is_null($post) && $post->private_user ? 'checked="checked"' : ''?>> Privado (usuario)</label>
        </div>
    </div>

    <!-- Body Field -->
    <div class="form-group col-xs-12">
        {!! Form::label('body', 'Contenido:') !!}
        {!! Form::textarea('body', null, ['class' => 'form-control', 'id' => 'editor', 'row' => '150']) !!}
    </div>

    <!-- Submit Field -->
    <div class="form-group col-xs-12">
        {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
        <a href="{!! route('web.index') !!}" class="btn btn-default pull-right">Cancelar</a>
    </div>