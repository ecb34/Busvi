{!! Form::open(['route' => ['roles.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('roles.show', $id) }}" class='btn btn-default btn-xs'>
        <i class="glyphicon glyphicon-eye-open"></i>
    </a>
    @if(\App\Models\Role::find($id)->name == 'admin')
        <a href="#" class='btn btn-default btn-xs'>
            <i class="glyphicon glyphicon-ban-circle"></i>
        </a>
      
    @else
        <a href="{{ route('roles.edit', $id) }}" class='btn btn-default btn-xs'>
            <i class="glyphicon glyphicon-edit"></i>
        </a>
    @endif    
    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger btn-xs',
        'onclick' => "return confirm('Seguro?')"
    ]) !!}
</div>
{!! Form::close() !!}
