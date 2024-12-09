<?php

use App\Modules\Reservation\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::group(array('Module'=>'Reservation', 'middleware' => ['web','auth','GlobalSecurity']), function () {
    Route::get('reservation', [ReservationController::class, 'reservationList']);
    Route::get('reservation/get-reservation-list', [ReservationController::class, 'getReservationList']);
    Route::get('reservation/make/{status_id}/{id}', [ReservationController::class, 'makeReservation']);
    Route::get('reservation/get-flight-code', [ReservationController::class, 'getFlightCode']);
    Route::get('reservation/show-pilgrim-list-modal', [ReservationController::class, 'showPilgrimListModal']);
    Route::get('reservation/get-pilgrim-list-flight-date',[ReservationController::class, 'getPilgrimByFlightDate']);
    Route::post('reservation/add-pilgrim-to-ticket-reservation',[ReservationController::class, 'addPilgrimToTicketReservation']);
    Route::get('reservation/get-reservation-done-list', [ReservationController::class, 'getReservationDoneList']);
    Route::post('reservation/remove-pilgrims-from-flight',[ReservationController::class, 'removePilgrimFromFlightDate']);
    Route::post('reservation/remove-pilgrims-from-flight-row',[ReservationController::class, 'removePilgrimFromFlightRow']);
    Route::post('reservation/add-all-pilgrim-to-ticket-reservation',[ReservationController::class, 'addAllPilgrimToTicketReservation']);
    Route::post('reservation/remove-all-pilgrim-to-ticket-reservation',[ReservationController::class, 'removeAllPilgrimToTicketReservation']);
    Route::post('reservation/complete-ticket-reservation',[ReservationController::class, 'completeTicketReservation']);
    Route::post('reservation/draft-ticket-reservation',[ReservationController::class, 'draftTicketReservation']);
    Route::post('reservation/ticket-reservation-cancel',[ReservationController::class, 'cancelReservation']);

    Route::post('reservation/get-pilgrim-list',[ReservationController::class, 'getPilgrimList']);
    Route::post('reservation/export-pilgrim-list/{id}',[ReservationController::class, 'exportPilgrimList']);

});
