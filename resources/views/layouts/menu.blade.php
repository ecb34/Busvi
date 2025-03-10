@if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'operator')
	{{-- Si el usuario es SUPERADMIN --}}

	<li class="">
	    <a href="{!! route('home') !!}">
	    	<i class="fa fa-home"></i><span>{{ trans('app.common.init') }}</span>
	    </a>
	</li>

	<li class="">
	    <a href="{!! route('comisiones.index') !!}" class="{{ Request::is('admin/comisiones*') ? 'active' : '' }}">
	    	<i class="fa fa-dollar"></i><span>{{ trans('app.common.comisiones') }}</span>
	    </a>
	</li>

	<li class="">
	    <a href="{!! route('categorias_evento.index') !!}" class="{{ Request::is('admin/categorias_evento*') ? 'active' : '' }}">
	    	<i class="fa fa-database"></i><span>{{ trans('app.common.categorias_evento') }}</span>
	    </a>
	</li>

	<li class="">
	    <a href="{!! route('users.index') !!}" class="{{ Request::is('admin/users*') ? 'active' : '' }}">
	    	<i class="fa fa-user"></i><span>{{ trans('app.common.administrators') }}</span>
	    </a>
	</li>

	<li class="">
	    <a href="{!! route('users.customers') !!}" class="{{ Request::is('admin/users*') ? 'active' : '' }}">
	    	<i class="fa fa-user-o" aria-hidden="true"></i><span>{{ trans('app.common.users') }}</span>
	    </a>
	</li>

	<li class="">
	    <a href="{!! route('sectors.index') !!}" class="{{ Request::is('admin/sectors*') ? 'active' : '' }}">
	    	<i class="fa fa-th-large" aria-hidden="true"></i><span>{{ trans('app.common.sectors') }}</span>
	    </a>
	</li>

	<li class="">
	    <a href="{!! route('services.index') !!}" class="{{ Request::is('admin/services*') ? 'active' : '' }}">
	    	<i class="fa fa-pencil-square-o" aria-hidden="true"></i><span>{{ trans('app.common.services') }}</span>
	    </a>
	</li>

	<li class="treeview {{ (Request::is('admin/companies*') || Request::is('admin/crew*') || Request::is('admin/calendar*')) ? 'active' : '' }}">
		<a href="#">
			<i class="fa fa-building" aria-hidden="true"></i>
			<span>Más {{ trans('app.common.services') }}</span>
			<span class="pull-right-container">
				<i class="fa fa-angle-left pull-right"></i>
			</span>
		</a>
		<ul class="treeview-menu">
			<li class="{{ Request::is('admin/companies*') ? 'active' : '' }}">
				<a href="{!! route('companies.index') !!}" class="{{ Request::is('admin/companies*') ? 'active' : '' }}">
			    	<i class="fa fa-building-o" aria-hidden="true"></i><span>{{ trans('app.common.companies') }}</span>
			    </a>
			</li>
			<li class="{{ Request::is('admin/crew*') ? 'active' : '' }}">
				<a href="{!! route('crew.index') !!}" class="{{ Request::is('admin/crew*') ? 'active' : '' }}">
				    <i class="fa fa-users" aria-hidden="true"></i><span>{{ trans('app.common.crews') }}</span>
				</a>
			</li>
			<li class="{{ Request::is('admin/calendar*') ? 'active' : '' }}">
				<a href="{!! route('calendar.index') !!}" class="{{ Request::is('admin/calendar*') ? 'active' : '' }}">
				    <i class="fa fa-calendar" aria-hidden="true"></i><span>{{ trans('app.common.calendar') }}</span>
				</a>
			</li>
		</ul>
	</li>

	<li class="">
	    <a href="{!! route('rates.index') !!}" class="{{ Request::is('admin/rates*') ? 'active' : '' }}">
	    	<i class="fa fa-star-half-o" aria-hidden="true"></i><span>{{ trans('app.common.rates') }}</span>
	    </a>
	</li>

	<? /*
	<li class="">
	    <a href="{!! route('subscriptions.index') !!}" class="{{ Request::is('admin/subscriptions*') ? 'active' : '' }}">
	    	<i class="fa fa-pencil" aria-hidden="true"></i><span>{{ trans('app.common.subscriptions') }}</span>
	    </a>
	</li>
	*/ ?>

	<li class="">
	    <a href="{!! route('web.index') !!}" class="{{ Request::is('admin/web*') ? 'active' : '' }}">
	    	<i class="fa fa-at" aria-hidden="true"></i><span>{{ trans('app.common.web') }}</span>
	    </a>
	</li>

@elseif (Auth::user()->role == 'admin' && Auth::user()->company->payed && ! Auth::user()->company->blocked)
	
	{{-- Si el usuario es ADMIN --}}

	@if (Auth::user()->company->payed)
		<li class="">
		    <a href="{!! route('home') !!}">
		    	<i class="fa fa-home"></i><span>{{ trans('app.common.init') }}</span>
		    </a>
		</li>

		<li class="treeview">
			<a href="#">
				<i class="fa fa-building" aria-hidden="true"></i>
				<span>{{ trans('app.common.companies') }}</span>
				<span class="pull-right-container">
					<i class="fa fa-angle-left pull-right"></i>
				</span>
			</a>
			<ul class="treeview-menu">
				<li class="{{ Request::is('admin/companies*') ? 'active' : '' }}">
					<a href="{!! route('companies.edit', Auth::user()->company) !!}" class="{{ Request::is('admin/companies*') ? 'active' : '' }}">
				    	<i class="fa fa-building-o" aria-hidden="true"></i><span>{{ trans('app.common.companies') }}</span>
				    </a>
				</li>
				<li class="{{ Request::is('admin/crew*') ? 'active' : '' }}">
					<a href="{!! route('crew.index') !!}" class="{{ Request::is('admin/crew*') ? 'active' : '' }}">
					    <i class="fa fa-users" aria-hidden="true"></i><span>{{ trans('app.common.crews') }}</span>
					</a>
				</li>

				@if (Auth::user()->company->type == 1 && Auth::user()->company->enable_events)
					<li class="{{ Request::is('admin/calendar*') ? 'active' : '' }}">
						<a href="{!! route('calendar.index') !!}" class="{{ Request::is('admin/calendar*') ? 'active' : '' }}">
						    <i class="fa fa-calendar" aria-hidden="true"></i><span>{{ trans('app.common.calendar') }}</span>
						</a>
					</li>
				@endif

			</ul>
		</li>

		@if (Auth::user()->company->type == 1 && Auth::user()->company->enable_reservas)
					
		<li class="treeview {{ Request::is('admin/reservas*') ? 'menu-open active' : '' }}">
			<a href="#">
				<i class="fa fa-book" aria-hidden="true"></i>
				<span>{{ trans('app.reservas.reservas') }}</span>
				<span class="pull-right-container">
					<i class="fa fa-angle-left pull-right"></i>
				</span>
			</a>
			<ul class="treeview-menu <?=Request::is('admin/reservas*') ? 'menu-open" style="display: block;' : '' ?>">
				<li class="{{ Request::is('reservas/calendario*') ? 'active' : '' }}">
					<a href="{!! action('Admin\ReservasController@getCalendario') !!}" class="{{ Request::is('reservas/calendario*') ? 'active' : '' }}">
				    	<i class="fa fa-calendar" aria-hidden="true"></i><span>{{ trans('app.reservas.calendario') }}</span>
				    </a>
				</li>
				<li class="{{ Request::is('reservas/listado*') ? 'active' : '' }}">
					<a href="{!! action('Admin\ReservasController@getReservas') !!}" class="{{ Request::is('reservas/listado*') ? 'active' : '' }}">
					    <i class="fa fa-check-square-o" aria-hidden="true"></i><span>{{ trans('app.reservas.listado_reservas') }}</span>
					</a>
				</li>
				<li class="{{ Request::is('reservas/turnos*') ? 'active' : '' }}">
					<a href="{!! action('Admin\ReservasController@getTurnos') !!}" class="{{ Request::is('reservas/turnos*') ? 'active' : '' }}">
					    <i class="fa fa-cog" aria-hidden="true"></i><span>{{ trans('app.reservas.configurar_turnos') }}</span>
					</a>
				</li>
				<li class="{{ Request::is('reservas/bloqueos*') ? 'active' : '' }}">
					<a href="{!! action('Admin\ReservasController@getBloqueos') !!}" class="{{ Request::is('reservas/bloqueos*') ? 'active' : '' }}">
					    <i class="fa fa-cog" aria-hidden="true"></i><span>{{ trans('app.reservas.bloquear_fechas') }}</span>
					</a>
				</li>
			</ul>
		</li>

		@endif

		@if (Auth::user()->company->type == 1 && Auth::user()->company->accept_eventos)
		 
		@endif

		<li class="">
		    <a href="{!! route('services.index') !!}" class="{{ Request::is('admin/services*') ? 'active' : '' }}">
		    	<i class="fa fa-pencil-square-o" aria-hidden="true"></i><span>{{ trans('app.common.services') }}</span>
		    </a>
		</li>

	@endif

@elseif (Auth::user()->role == 'crew' && Auth::user()->company->payed && ! Auth::user()->company->blocked)
	
	{{-- Si el usuario es PROFESIONAL --}}
	<li class="">
	    <a href="{!! route('home') !!}">
	    	<i class="fa fa-home"></i><span>{{ trans('app.common.init') }}</span>
	    </a>
	</li>

	<li class="">
	    <a href="{!! route('crew.edit', Auth::user()->id) !!}" class="{{ Request::is('admin/crew*') ? 'active' : '' }}">
	    	<i class="fa fa-pencil-square-o" aria-hidden="true"></i><span>{{ trans('app.admin.layout.profile') }}</span>
	    </a>
	</li>

@elseif (Auth::user()->role == 'user')

	<li class="">
	    <a href="{!! route('home') !!}">
	    	<i class="fa fa-home"></i><span>{{ trans('app.common.init') }}</span>
	    </a>
	</li>

	<li class="">
	    <a href="{!! route('users.edit', Auth::user()) !!}" class="{{ Request::is('admin/users*') ? 'active' : '' }}">
	    	<i class="fa fa-user-o" aria-hidden="true"></i><span>{{ trans('app.common.profile') }}</span>
	    </a>
	</li>

	<li class="{{ Request::is('admin/calendar*') ? 'active' : '' }}">
		<a href="{!! route('calendar.create') !!}" class="{{ Request::is('admin/calendar*') ? 'active' : '' }}">
		    <i class="fa fa-calendar" aria-hidden="true"></i><span>{{ trans('app.common.new_event') }}</span>
		</a>
	</li>

	<li class="{{ Request::is('admin_favorites*') ? 'active' : '' }}">
		<a href="{!! route('companies.admin_favorites') !!}" class="{{ Request::is('admin_favorites*') ? 'active' : '' }}">
		    <i class="fa fa-star" aria-hidden="true"></i><span>{{ trans('app.common.favourites') }}</span>
		</a>
	</li>

	<li class="treeview {{ Request::is('admin/reservas*') ? 'menu-open active' : '' }}">
		<a href="#">
			<i class="fa fa-book" aria-hidden="true"></i>
			<span>{{ trans('app.reservas.reservas') }}</span>
			<span class="pull-right-container">
				<i class="fa fa-angle-left pull-right"></i>
			</span>
		</a>
		<ul class="treeview-menu <?=Request::is('admin/reservas*') ? 'menu-open" style="display: block;' : '' ?>">
			
			<li class="{{ Request::is('admin/reservas') ? 'active' : '' }}">
				<a href="{!! action('Admin\ReservasController@getProximasReservas') !!}" class="{{ Request::is('admin/reservas') ? 'active' : '' }}">
				    <i class="fa fa-check-square-o" aria-hidden="true"></i><span>{{ trans('app.reservas.proximas_reservas') }}</span>
				</a>
			</li>

			<li class="{{ Request::is('admin/reservas/listado*') ? 'active' : '' }}">
				<a href="{!! action('Admin\ReservasController@getReservasPasadas') !!}" class="{{ Request::is('admin/reservas/pasadas*') ? 'active' : '' }}">
				    <i class="fa fa-check-square-o" aria-hidden="true"></i><span>{{ trans('app.reservas.reservas_pasadas') }}</span>
				</a>
			</li>

		</ul>
	</li>

	<li class="treeview {{ Request::is('admin/cheques_regalo*') ? 'menu-open active' : '' }}">
			<a href="#">
				<i class="fa fa-book" aria-hidden="true"></i>
				<span>{{ trans('app.cheques_regalo.cheques_regalo') }}</span>
				<span class="pull-right-container">
					<i class="fa fa-angle-left pull-right"></i>
				</span>
			</a>
			<ul class="treeview-menu <?=Request::is('admin/cheques_regalo*') ? 'menu-open" style="display: block;' : '' ?>">
				<li class="{{ Request::is('admin/cheques_regalo') ? 'active' : '' }}">
					<a href="{!! action('Admin\ChequeRegaloController@index') !!}" class="{{ Request::is('admin/cheques_regalo') ? 'active' : '' }}">
					    <i class="fa fa-gift" aria-hidden="true"></i><span>{{ trans('app.cheques_regalo.mis_cheques_regalo') }}</span>
					</a>
				</li>
				<li class="{{ Request::is('admin/cheques_regalo/pendientesPago') ? 'active' : '' }}">
					<a href="{!! action('Admin\ChequeRegaloController@pendientesPago') !!}" class="{{ Request::is('admin/cheques_regalo/pendientesPago') ? 'active' : '' }}">
					    <i class="fa fa-gift" aria-hidden="true"></i><span>{{ trans('app.cheques_regalo.cheques_pendientes_pago') }}</span>
					</a>
				</li>
				
			</ul>	
	</li>		

			
	
@endif

<li class="treeview {{ Request::is('admin/eventos*') ? 'menu-open active' : '' }}">
			<a href="#">
				<i class="fa fa-calendar" aria-hidden="true"></i>
				<span>{{ trans('app.eventos.eventos') }}</span>
				<span class="pull-right-container">
					<i class="fa fa-angle-left pull-right"></i>
				</span>
			</a>
			<ul class="treeview-menu <?=Request::is('admin/eventos*') ? 'menu-open" style="display: block;' : '' ?>">
				@if ((optional(Auth::user()->company)->type == 1 && optional(Auth::user()->company)->accept_eventos) || Auth::user()->role == 'superadmin')
		 			<li class="{{ Request::is('admin/eventos/enMiNegocio') ? 'active' : '' }}">
						<a href="{!! action('Admin\EventoController@enMiNegocio') !!}" class="{{ Request::is('admin/eventos/enMiNegocio') ? 'active' : '' }}">
						    <i class="fa fa-calendar-check-o" aria-hidden="true"></i><span>{{ trans('app.eventos.en_mi_negocio') }}</span>
						</a>
					</li>
				@endif
				<li class="{{ Request::is('admin/eventos') ? 'active' : '' }}">
					<a href="{!! action('Admin\EventoController@index') !!}" class="{{ Request::is('admin/eventos') ? 'active' : '' }}">
					    <i class="fa fa-calendar-check-o" aria-hidden="true"></i><span>{{ trans('app.eventos.disponibles') }}</span>
					</a>
				</li>
				<li class="{{ Request::is('admin/eventos/misEventos') ? 'active' : '' }}">
					<a href="{!! action('Admin\EventoController@misEventos') !!}" class="{{ Request::is('admin/eventos/misEventos') ? 'active' : '' }}">
					    <i class="fa fa-calendar-check-o" aria-hidden="true"></i><span>{{ trans('app.eventos.organizados_por_mi') }}</span>
					</a>
				</li>
				<li class="{{ Request::is('admin/eventos/asistire') ? 'active' : '' }}">
					<a href="{!! action('Admin\EventoController@asistire') !!}" class="{{ Request::is('admin/eventos/asistire') ? 'active' : '' }}">
					    <i class="fa fa-calendar-check-o" aria-hidden="true"></i><span>{{ trans('app.eventos.confirmado_asistencia') }}</span>
					</a>
				</li>
				
			</ul>	
	</li>

@if ((Auth::user()->role == 'admin' || Auth::user()->role == 'crew') && Auth::user()->company->payed && ! Auth::user()->company->blocked)
<?php
	$entradas_privadas = \App\Post::where('private', 1)->where('order', '>=', 0)->orderBy('order', 'asc')->get();
	if(count($entradas_privadas) > 0){
		foreach($entradas_privadas as $entrada){
?>
<li class="{{ Request::is('admin/info/'.$entrada->slug.'.html') ? 'active' : '' }}">
	<a href="{!! action('Admin\InfoController@getPrivatePost', [$entrada->slug]) !!}" class="{{ Request::is('admin/info/'.$entrada->slug.'.html') ? 'active' : '' }}">
		<i class="fa fa-info" aria-hidden="true"></i><span>{{ $entrada->title }}</span>
	</a>
</li>
<?php }} ?>
	<li class="{{ Request::is('admin/cheques_regalo/deNegocio') ? 'active' : '' }}">
		<a href="{!! action('Admin\ChequeRegaloController@deNegocio') !!}" class="{{ Request::is('admin/cheques_regalo/deNegocio') ? 'active' : '' }}">
		    <i class="fa fa-gift" aria-hidden="true"></i><span>{{ trans('app.cheques_regalo.deNegocio') }}</span>
		</a>
	</li>					
				
@endif
@if(Auth::user()->role == 'superadmin')
	<li class="{{ Request::is('admin/cheques_regalo/administracion') ? 'active' : '' }}">
		<a href="{!! action('Admin\ChequeRegaloController@administracion') !!}" class="{{ Request::is('admin/cheques_regalo/administracion') ? 'active' : '' }}">
		    <i class="fa fa-gift" aria-hidden="true"></i><span>{{ trans('app.cheques_regalo.administracion') }}</span>
		</a>
	</li>
	<li class="{{ Request::is('admin/eventos/administracion') ? 'active' : '' }}">
		<a href="{!! action('Admin\EventoController@administracion') !!}" class="{{ Request::is('admin/eventos/administracion') ? 'active' : '' }}">
		    <i class="fa fa-calendar-check-o" aria-hidden="true"></i><span>{{ trans('app.eventos.administracion') }}</span>
		</a>
	</li>
@endif

@if(Auth::user()->role == 'user')
<?php
	$entradas_privadas = \App\Post::where('private_user', 1)->where('order', '>=', 0)->orderBy('order', 'asc')->get();
	if(count($entradas_privadas) > 0){
		foreach($entradas_privadas as $entrada){
?>
<li class="{{ Request::is('admin/info/'.$entrada->slug.'.html') ? 'active' : '' }}">
	<a href="{!! action('Admin\InfoController@getPrivatePost', [$entrada->slug]) !!}" class="{{ Request::is('admin/info/'.$entrada->slug.'.html') ? 'active' : '' }}">
		<i class="fa fa-info" aria-hidden="true"></i><span>{{ $entrada->title }}</span>
	</a>
</li>
<?php }} ?>
@endif