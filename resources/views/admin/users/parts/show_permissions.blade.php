<!-- Permisos -->
<div class="form-group">
	@if(count($user->permissions) > 0)
	    @foreach($user->permissions as $rol)
	   	 <p>{!! $rol->name !!}</p>
	   	@endforeach 
	@else
		<p>{{ trans('app.admin.users.user_no_permissions') }}</p>   	
	@endif	
</div>
