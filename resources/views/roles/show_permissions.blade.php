<!-- Permisos -->
<div class="form-group">
    @if(count($role->permissions) > 0)
        @foreach($role->permissions as $permission)
         <p>{!! $permission->name !!}</p>
        @endforeach 
    @else
        <p>Este Rol no tiene permisos asignados</p>      
    @endif  
</div>


