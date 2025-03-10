<!-- Permisos -->
<div class="form-group">
    @if(count($permisos) > 0)  
        @foreach($permisos as $permiso)
        	<div class="form-group col-sm-6">
			    {!! Form::label($permiso->id, $permiso->name) !!}
			     @if(isset($role) &&  ($role->hasPermissionTo($permiso->name) or $role->name == 'admin'))
			    	{!! Form::checkbox('permiso_seleccionado[]',$permiso->id,'true',['form' =>'roleForm']) !!}
			    @else
			    	{!! Form::checkbox('permiso_seleccionado[]',$permiso->id,null,['form' =>'roleForm']) !!}
			    @endif	
			</div>        
        @endforeach 
    @else
        <p>No hay permisos dados de alta en el sistema</p>      
    @endif  
</div>


