<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// User
Route::post('login', 'API\UserAPIController@login')->name('user.login');
Route::post('logout', 'API\UserAPIController@logout')->name('user.logout');
Route::post('password/email', 'Auth\ForgotPasswordController@getResetToken')->name('user.getResetToken');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('user.reset');
Route::post('check_login', 'API\UserAPIController@check_login')->name('user.check_login');
Route::post('password_reset', 'API\UserAPIController@password_reset')->name('user.password_reset');
Route::post('register', 'API\UserAPIController@register')->name('user.register');
Route::post('users/edit', 'API\UserAPIController@edit')->name('user.edit');
Route::post('users/update', 'API\UserAPIController@update')->name('user.update');
Route::post('users/update_password', 'API\UserAPIController@updatePassword')->name('user.update_password');
Route::post('users/sectors', 'API\UserAPIController@sectors')->name('user.sectors');
Route::post('users/company', 'API\UserAPIController@company')->name('user.company');
Route::post('users/companies', 'API\UserAPIController@companies')->name('user.companies');
Route::post('users/eventos', 'API\UserAPIController@eventos')->name('user.eventos');
Route::post('users/favourites', 'API\UserAPIController@favourites')->name('user.favourites');
Route::post('users/add_favourite', 'API\UserAPIController@addFavourite')->name('user.add_favourite');
Route::post('users/remove_favourite', 'API\UserAPIController@removeFavourite')->name('user.remove_favourite');
Route::post('users/event_list', 'API\UserAPIController@eventList')->name('user.event_list');
Route::post('users/history', 'API\UserAPIController@history')->name('user.history');
Route::post('users/moments', 'API\UserAPIController@moments')->name('user.moments');
Route::post('users/create_event', 'API\UserAPIController@createEvent')->name('user.create_event');
Route::post('users/update_event', 'API\UserAPIController@updateEvent')->name('user.update_event');
Route::post('users/delete_event', 'API\UserAPIController@deleteEvent')->name('user.delete_event');
Route::post('get_event_by_id', 'API\UserAPIController@getEventById')->name('user.get_event_by_id');
Route::post('users/get_down', 'API\UserAPIController@getDown')->name('user.get_down');
Route::post('users/page', 'API\UserAPIController@page')->name('user.page');

// Crew
Route::post('crew/info', 'API\CrewAPIController@info')->name('crew.info');
Route::post('crew/list_events', 'API\CrewAPIController@listEvents')->name('crew.list_events');
Route::post('crew/blocked_delete', 'API\CrewAPIController@blockedDelete')->name('crew.blocked_delete');
Route::post('crew/blocked_new', 'API\CrewAPIController@blockedNew')->name('crew.blocked_new');

// Chat
Route::post('chat/history', 'API\ChatController@history')->name('chat.history');
Route::post('chat/message', 'API\ChatController@message')->name('chat.message');
Route::post('chat/crew_list', 'API\ChatController@crewList')->name('chat.crew_list');

// Contadores
Route::post('count', 'API\CountController@postCount');

// Fichajes
Route::post('fichajes/listado', 'API\FichajeController@postListado');
Route::post('fichajes/inicio', 'API\FichajeController@postInicio');
Route::post('fichajes/fin', 'API\FichajeController@postFin');

// Reservas
Route::post('users/reservas/turnos_disponibles', 'API\ReservasController@postTurnosDisponibles');
Route::post('users/reservas/solicitar_reserva', 'API\ReservasController@postSolicitarReserva');
Route::post('users/reservas', 'API\ReservasController@postReservasUsuario');
Route::post('users/reservas/anular', 'API\ReservasController@postAnularReservaUsuario');
Route::post('admin/reservas', 'API\ReservasController@postReservasAdmin');
Route::post('admin/reserva/estado', 'API\ReservasController@postEstadoReservaAdmin');
Route::post('admin/reservas/turnos_disponibles', 'API\ReservasController@postTurnosDisponiblesAdmin');
Route::post('admin/reservas/solicitar_reserva', 'API\ReservasController@postSolicitarReservaAdmin');


// Cheques Regalo
Route::post('chequesRegalo/disponibles', 'API\ChequeRegaloAPIController@misChequesDisponibles');
Route::post('chequesRegalo/comprobar', 'API\ChequeRegaloAPIController@comprobar');
Route::post('chequesRegalo/consumir', 'API\ChequeRegaloAPIController@consumir');
Route::post('chequesRegalo/historico', 'API\ChequeRegaloAPIController@historicoConsumidos');

// Eventos
 Route::post('eventos/disponibles', 'API\EventosAPIController@misEventosDisponibles'); 
 Route::post('eventos/misEventos', 'API\EventosAPIController@EventosOrganizadosPorMi');
 Route::post('eventos/comprobar', 'API\EventosAPIController@comprobar');
 Route::post('eventos/consumir', 'API\EventosAPIController@consumir');
 Route::post('eventos/historico', 'API\EventosAPIController@historicoConsumidos');
 Route::post('eventos/negocio/misEventos', 'API\EventosAPIController@negocioMisEventos');
 Route::post('eventos/validar', 'API\EventosAPIController@validar');
