<!-- Roles -->
<div class="form-group">
	@if(count($user->roles) > 0)
	
	    @foreach($user->roles as $rol)
	   	 <p>{!! $rol->name !!}</p>
	   	@endforeach 
	@else
		<p>{{ trans('app.common.user_no_roles') }}</p>   	
	@endif	
</div>
