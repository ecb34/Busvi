<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return redirect('login');
// });
// use \App\Post;

/**
 * Auth routes
 */
Auth::routes();

/**
 * Admin routes & companyPayment
 */

Route::middleware(['auth', 'companyPayment'])->prefix('admin')->group(function ($locale) {
	Route::get('home', 'Admin\AdminController@home')->name('home');

	Route::resource('users', 'Admin\UserController');
		Route::post('ajaxUsersDestroy', [
				'as'	=> 'users.ajaxDestroy',
				'uses'	=> 'Admin\UserController@ajaxDestroy'
			]);
		Route::post('ajaxUpdatePass/{id}', [
				'as'	=> 'users.ajaxUpdatePass',
				'uses'	=> 'Admin\UserController@ajaxUpdatePass'
			]);
		Route::get('customers', [
				'as'	=> 'users.customers',
				'uses'	=> 'Admin\UserController@customers'
			]);
		Route::get('customers.xlsx', [
				'as'	=> 'users.excel',
				'uses'	=> 'Admin\UserController@excel'
			]);

	Route::resource('sectors', 'Admin\SectorsController');
		Route::post('ajaxSectorsDestroy', [
				'as'	=> 'sectors.ajaxDestroy',
				'uses'	=> 'Admin\SectorsController@ajaxDestroy'
			]);

	Route::resource('services', 'Admin\ServicesController');
		Route::post('ajaxServicesDestroy', [
				'as'	=> 'services.ajaxDestroy',
				'uses'	=> 'Admin\ServicesController@ajaxDestroy'
			]);

	Route::resource('companies', 'Admin\CompaniesController');
		Route::post('ajaxFavourite', [
				'as'	=> 'companies.ajaxFavourite',
				'uses'	=> 'Admin\CompaniesController@ajaxFavourite'
			]);
		Route::post('ajaxCompaniesDestroy', [
				'as'	=> 'companies.ajaxDestroy',
				'uses'	=> 'Admin\CompaniesController@ajaxDestroy'
			]);
		Route::get('companies.xlsx', [
				'as'	=> 'companies.excel',
				'uses'	=> 'Admin\CompaniesController@excel'
			]);


		
		Route::post('gallery/{id}', 'Admin\CompaniesController@gallery')
			 ->name('company.gallery');
		Route::post('removeImageGallery', 'Admin\CompaniesController@removeImageGallery')
			 ->name('companies.removeImageGallery');
		Route::post('importTags/{id}', 'Admin\CompaniesController@importTags')
			 ->name('companies.importTags');
		Route::post('block_company', 'Admin\CompaniesController@block')
			 ->name('companies.block');
		Route::post('getDown', 'Admin\CompaniesController@getDown')
			 ->name('companies.getDown');
		Route::get('admin_favorites', 'Admin\CompaniesController@adminFavorites')
			 ->name('companies.admin_favorites');
		Route::post('tags', 'Admin\CompaniesController@tags')
			 ->name('companies.tags');
		Route::get('setFavourite/{id}', 'Admin\CompaniesController@setFavourite')
			 ->name('companies.setFavourite');
		Route::post('orderGallery', 'Admin\CompaniesController@orderGallery')
			 ->name('companies.orderGallery');
		Route::post('offerGallery', 'Admin\CompaniesController@offerGallery')
			 ->name('companies.offerGallery');
		Route::post('editGallery', 'Admin\CompaniesController@editGallery')
			 ->name('companies.editGallery');

	Route::resource('rates', 'Admin\RatesController');
		Route::post('ajaxRatesDestroy', [
				'as'	=> 'rates.ajaxDestroy',
				'uses'	=> 'Admin\RatesController@ajaxDestroy'
			]);

	Route::resource('subscriptions', 'Admin\SubscriptionsController');

	Route::resource('calendar', 'Admin\CalendarController');
		Route::get('getCrew', [
				'as'	=> 'calendar.getCrew',
				'uses'	=> 'Admin\CalendarController@getCrew'
			]);
		Route::get('getCalendar', [
				'as'	=> 'calendar.getCalendar',
				'uses'	=> 'Admin\CalendarController@getCalendar'
			]);
		Route::post('ajaxCalendarDestroy', [
				'as'	=> 'calendar.ajaxDestroy',
				'uses'	=> 'Admin\CalendarController@ajaxDestroy'
			]);
		Route::post('dayTime', [
				'as'	=> 'calendar.dayTime',
				'uses'	=> 'Admin\CalendarController@dayTime'
			]);
		Route::post('ajaxUpdate', [
				'as'	=> 'calendar.ajaxUpdate',
				'uses'	=> 'Admin\CalendarController@ajaxUpdate'
			]);
		Route::get('goToCreate/{id}', [
				'as'	=> 'calendar.goToCreate',
				'uses'	=> 'Admin\CalendarController@goToCreate'
			]);
		Route::post('termSearch', 'Admin\CalendarController@termSearch')
			 ->name('calendar.termSearch');
		Route::post('search_special', 'Admin\CalendarController@searchSpecial')
			 ->name('calendar.search_special');
		Route::post('ajaxUpdateEvent/{id}', 'Admin\CalendarController@ajaxUpdateEvent')
			 ->name('calendar.ajaxUpdateEvent');
		Route::get('nextEvents/{id}', 'Admin\CalendarController@nextEvents')
			 ->name('calendar.nextEvents');

	Route::resource('crew', 'Admin\CrewController');
		Route::get('block-event/{id}', [
				'as'	=> 'crew.blockEvent',
				'uses'	=> 'Admin\CrewController@blockEvent'
			]);
		Route::post('add-block-event/{id}', [
				'as'	=> 'crew.addBlockEvent',
				'uses'	=> 'Admin\CrewController@addBlockEvent'
			]);
		Route::post('ajaxDestroyEventBlock', [
				'as'	=> 'crew.ajaxDestroyEventBlock',
				'uses'	=> 'Admin\CrewController@ajaxDestroyEventBlock'
			]);
		Route::get('ajaxGetServices', [
				'as'	=> 'crew.ajaxGetServices',
				'uses'	=> 'Admin\CrewController@ajaxGetServices'
			]);
		Route::post('addCrewFavourite', 'Admin\UserController@setFavoriteCrew')
			 ->name('crew.addCrewFavourite');
		Route::get('fichajes/{id}', 'Admin\CrewController@getFichajes');
		Route::post('fichaje', 'Admin\CrewController@postFichaje');
		Route::post('fichaje/delete', 'Admin\CrewController@postDeleteFichaje');
		Route::post('fichajes/informe', [
			'as'	=> 'crew.informeFichajes',
			'uses'	=> 'Admin\CrewController@postInformeFichajes'
		]);

	Route::resource('web', 'Admin\WebController');
		Route::post('ajaxDestroy', [
				'as'	=> 'web.ajaxDestroy',
				'uses'	=> 'Admin\WebController@ajaxDestroy'
			]);
		Route::post('ajaxUploadImage', [
				'as'	=> 'web.ajaxUploadImage',
				'uses'	=> 'Admin\WebController@ajaxUploadImage'
			]);
		Route::post('/upload_image', [
				'as' 	=> 'web.upload_image',
				'uses'	=> 'Admin\WebController@upload_image'
			]);

		Route::post('web/gallery_add', 'Admin\WebController@galleryAdd')
			 ->name('web.gallery_add');
		Route::post('web/gallery_delete', 'Admin\WebController@galleryDelete')
			 ->name('web.gallery_delete');
		Route::post('web/gallery_order', 'Admin\WebController@galleryOrder')
			 ->name('web.gallery_order');
		Route::post('web/gallery_edit', 'Admin\WebController@galleryEdit')
			 ->name('web.gallery_edit');
	 
});

/**
 * Admin routes
 */
Route::middleware(['auth'])->prefix('admin')->group(function ($locale) {
	
	Route::get('company/{id}/payment/', 'Admin\CompaniesController@payment')
		 ->name('companies.payment');
	Route::get('company/{id}/payment_premium/', 'Admin\CompaniesController@paymentPremium')
		 ->name('companies.payment_premium');
	Route::view('company/{id}/disabled', 'public.payment_crew')
		 ->name('companies.payment_crew');
	
	Route::get('info/{slug}.html', 'Admin\InfoController@getPrivatePost');

});

/* company admin routes */
Route::middleware(['auth', 'companyPayment', 'isCompanyAdmin', 'reservasEnabled'])->prefix('admin')->group(function ($locale) {
	
	Route::get('reservas/calendario', 'Admin\ReservasController@getCalendario');
	Route::get('reservas/calendario/eventos', 'Admin\ReservasController@getEventosCalendario');
	Route::post('reservas/reserva', 'Admin\ReservasController@postEstadoReserva');
	Route::get('reservas/reservas', 'Admin\ReservasController@getReservas');
	Route::get('reservas/reservas/datatables', 'Admin\ReservasController@getReservasDatatables');
	Route::get('reservas/turnos', 'Admin\ReservasController@getTurnos');
	Route::get('reservas/turnos/datatables', 'Admin\ReservasController@getTurnosDatatables');
	Route::get('reservas/turnos/nuevo', 'Admin\ReservasController@getNuevoTurno');
	Route::post('reservas/turnos/nuevo', 'Admin\ReservasController@postNuevoTurno');
	Route::post('reservas/turnos/eliminar', 'Admin\ReservasController@postEliminarTurno');
	Route::get('reservas/turnos/{id}', 'Admin\ReservasController@getTurno');
	Route::post('reservas/turnos/{id}', 'Admin\ReservasController@postTurno');
	Route::get('reservas/turnos/{id}/bloqueos/datatables', 'Admin\ReservasController@getBloqueosDatatable');
	Route::post('reservas/turnos/{id}/bloqueos/nuevo', 'Admin\ReservasController@postNuevoBloqueo');
	Route::post('reservas/turnos/{id}/bloqueos/eliminar', 'Admin\ReservasController@postEliminarBloqueo');
	Route::get('/reservas/bloqueos', 'Admin\ReservasController@getBloqueos');
	Route::post('/reservas/reservas/manual', 'Admin\ReservasController@postReservaManual');

});

/**
 * Paypal Express
 */
Route::get('paypal/{id}/express-checkout', 'PaypalController@expressCheckout')->name('paypal.express-checkout');
Route::get('paypal/{id}/express-checkout-success', 'PaypalController@expressCheckoutSuccess');
Route::post('paypal/notify', 'PaypalController@notify');

/**
 * Paypal Standard
 */
Route::post('paypal/{id}', 'PaymentController@payWithpaypal')->name('paywithpaypal');
Route::get('status/{id}', 'PaymentController@getPaymentStatus')->name('paypal.status');
Route::post('stripe/status/{id}', 'PaymentController@StripeStatus')->name('stripe.status');
Route::post('ajaxTypeVariableSession', 'PaymentController@ajaxTypeVariableSession')->name('ajaxTypeVariableSession');



Route::get('chequeRegaloPaypal/{id}', 'Admin\ChequeRegaloController@payWithpaypal')->name('chequeRegaloPaypal');
Route::get('chequeRegaloStatus/{id}', 'Admin\ChequeRegaloController@getPaymentStatus')->name('chequeRegaloPaypal.status');
Route::post('chequeRegaloStripe', 'Admin\ChequeRegaloController@payWithstripe')->name('chequeRegaloStripe');
Route::get('chequeRegalodeNegocio', 'Admin\ChequeRegaloController@deNegocio')->name('chequeRegalodeNegocio');


Route::get('eventoPaypal/{id}', 'Admin\EventoController@payWithpaypal')->name('eventoPaypal');
Route::get('eventoStatus/{id}', 'Admin\EventoController@getPaymentStatus')->name('eventoPaypal.status');
Route::post('eventoStripe', 'Admin\EventoController@payWithstripe')->name('eventoStripe');


/**
 * Respuestas de pagos
 */
// Route::view('paypal/response', 'public.success', ['menu' => Post::all()])->name('paypal.response');

/**
 * Public routes
 */
Route::get('/', [
		'as' 	=> 'home.index',
		'uses'	=> 'HomeController@index'
	]);

Route::get('contacto', [
		'as' => 'home.contacto',
		'uses' => 'HomeController@contacto'
	]);

Route::post('sendContactForm', [
		'as' => 'home.sendContactForm',
		'uses' => 'HomeController@sendContactForm'
	]);

Route::post('getLocation', [
		'as'	=> 'home.getLocation',
		'uses'	=> 'HomeController@getLocation'
	]);

Route::post('removeAddressSession', [
		'as'	=> 'home.removeAddressSession',
		'uses'	=> 'HomeController@removeAddressSession'
	]);

Route::get('company/{id}', [
		'as'	=> 'home.company',
		'uses'	=> 'HomeController@company'
	]);

Route::get('list/companies/{sector?}', [
		'as'	=> 'home.list',
		'uses'	=> 'HomeController@list'
	]);

Route::get('register/select_register_type', [
		'as'	=> 'home.select_register_type',
		'uses'	=> 'HomeController@select_register_type'
	]);

Route::get('register/user', [
		'as'	=> 'home.register',
		'uses'	=> 'HomeController@register'
	]);

Route::get('register/company', [
		'as'	=> 'home.register_company',
		'uses'	=> 'HomeController@register_company'
	]);

Route::post('make_register', [
		'as'	=> 'home.make_register',
		'uses'	=> 'HomeController@make_register'
	]);

Route::post('tags', [
		'as'	=> 'home.tags',
		'uses'	=> 'HomeController@tags'
	]);

Route::get('evento/{id}', [
		'as'	=> 'home.evento',
		'uses'	=> 'HomeController@evento'
	]);

Route::post('eventtags', [
		'as'	=> 'home.eventtags',
		'uses'	=> 'HomeController@eventtags'
	]);

// Route::get('user/dashboard', function () {
// 		dd('hola mundo');
// 	})->name('user.dashboard');

Route::post('home_login', 'HomeController@login')->name('home.login');

// reservas

Route::get('reservas/turnos-disponibles/{company_id}', 'Admin\ReservasController@getTurnosDisponibles');
Route::post('reserva', 'Admin\ReservasController@postReserva');

Route::middleware(['auth', 'isSuperAdmin'])->prefix('admin')->group(function ($locale) {
	Route::get('cheques_regalo/admin_datatable', 'Admin\ChequeRegaloController@chequesRegaloAdminDataTable');
	Route::get('cheques_regalo/administracion', 'Admin\ChequeRegaloController@administracion')->name('admin.cheques_regalo.administracion');
	Route::get('cheques_regalo/marcarChequeRegaloPagado/{id}', 'Admin\ChequeRegaloController@marcarChequeRegaloPagado')->name('admin.cheques_regalo.marcarChequeRegaloPagado');

});

Route::middleware(['auth', 'isSuperAdmin'])->prefix('admin')->group(function ($locale) {
	Route::get('eventos/marcarPagado', 'Admin\EventoController@marcarPagado');
	Route::get('eventos/admin_datatable', 'Admin\EventoController@eventosAdminDataTable');
	Route::get('eventos/administracion', 'Admin\EventoController@administracion')->name('admin.eventos.administracion');
	Route::get('eventos/marcarEventoPagado/{id}', 'Admin\EventoController@marcarEventoPagado')->name('admin.eventos.marcarEventoPagado');
	Route::resource('comisiones', 'Admin\ComisionesController');
	Route::resource('categorias_evento', 'Admin\CategoriasEventoController');

});


Route::middleware(['auth', 'isCustomer'])->prefix('admin')->group(function ($locale) {
	Route::get('reservas', 'Admin\ReservasController@getProximasReservas');
	Route::get('reservas/pasadas', 'Admin\ReservasController@getReservasPasadas');
	Route::get('reservas/datatables-clientes', 'Admin\ReservasController@getReservasClienteDatatables');
	Route::post('reservas/anular', 'Admin\ReservasController@postAnularReserva');

	Route::get('cheques_regalo/datatable', 'Admin\ChequeRegaloController@chequesRegaloDataTable');
	Route::get('cheques_regalo/pendientesDatatable', 'Admin\ChequeRegaloController@chequesRegaloAPagarDataTable');
	Route::get('cheques_regalo/excel', 'Admin\ChequeRegaloController@exportExcel');
	Route::post('cheques_regalo/pay/{id}', 'Admin\ChequeRegaloController@pay');
	Route::post('cheques_regalo/aceptarCheque', 'Admin\ChequeRegaloController@aceptarCheque');
	Route::get('cheques_regalo/marcarPagado', 'Admin\ChequeRegaloController@marcarPagado');
	Route::get('cheques_regalo/pendientesPago', 'Admin\ChequeRegaloController@pendientesPago')->name('admin.cheques_regalo.pendientes_pago');
	Route::get('cheques_regalo/create/{id?}', 'Admin\ChequeRegaloController@create')->name('admin.cheques_regalo.create');
	Route::resource('cheques_regalo', 'Admin\ChequeRegaloController');

});

Route::middleware(['auth'])->prefix('admin')->group(function ($locale) {
	Route::get('eventos/datatable', 'Admin\EventoController@eventosDisponiblesDataTable');
	Route::get('eventos/misEventosDatatable', 'Admin\EventoController@misEventosDatatable');
	Route::get('eventos/pendientesDatatable', 'Admin\EventoController@eventoAPagarDataTable');
	Route::post('eventos/pay/{id}', 'Admin\EventoController@pay');
	Route::get('eventos/create/{id?}', 'Admin\EventoController@create')->name('admin.eventos.create');
	Route::get('eventos/misEventos', 'Admin\EventoController@misEventos')->name('admin.eventos.mis_eventos');
	Route::get('eventos/asistire', 'Admin\EventoController@asistire')->name('admin.eventos.asistire');
	Route::get('eventos/asistireDatatable', 'Admin\EventoController@asistireDatatable')->name('admin.eventos.asistireDatatable');
	Route::get('eventos/pendientesPago', 'Admin\EventoController@pendientesPago')->name('admin.eventos.pendientes_pago');
	Route::post('eventos/apuntarse', 'Admin\EventoController@apuntarse')->name('admin.eventos.apuntarse');
	Route::get('eventos/enMiNegocio', 'Admin\EventoController@enMiNegocio')->name('admin.eventos.enMiNegocio');
	Route::get('eventos/enMiNegocioDatatable', 'Admin\EventoController@enMiNegocioDatatable')->name('admin.eventos.enMiNegocioDatatable');
	Route::post('eventos/validar', 'Admin\EventoController@validar')->name('admin.eventos.validar');
	Route::post('eventos/{id}/eliminarImagen', 'Admin\EventoController@eliminarImagen');
	Route::get('eventos/{id}/asistentesDatatable', 'Admin\EventoController@asistentesDatatable');
	Route::resource('eventos', 'Admin\EventoController');
});

Route::middleware(['auth' , 'isCompanyAdmin'])->prefix('admin')->group(function ($locale) {
	Route::post('eventos/aceptarEvento', 'Admin\EventoController@aceptarEvento');
});

Route::middleware(['auth'])->group(function ($locale) {
	Route::post('companies/ajaxShow', 'Admin\CompaniesController@ajaxShow')->name('companies.ajaxShow');
});	


/**
 * Exit
 */
Route::get('exit', function () {
	Auth::logout();
	//return redirect()->route('login');
	return redirect()->to('/');
})->name('exit');

Route::get('{slug}', [
	'as' 	=> 'home.show',
	'uses'	=> 'HomeController@show'
]);