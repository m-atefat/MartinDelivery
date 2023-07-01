<?php

use App\Http\Controllers\Api\V1\Business\BusinessOrdersController;
use App\Http\Controllers\Api\V1\Delivery\DeliveryOrdersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('delivery')
        ->middleware(['ability:delivery'])
        ->controller(DeliveryOrdersController::class)
        ->group(function () {
            Route::prefix('orders')->group(function () {
                Route::get('pending', 'ordersInPendingStatus');
                Route::get('accepted', 'acceptedOrders');
                Route::patch('{uuid}/accept', 'assignOrder');
                Route::patch('{uuid}/go-to-source-location', 'updateOrderStatusToGoingToSourceLocation');
                Route::patch('{uuid}/go-to-destination-location', 'updateOrderStatusToGoingToDestinationLocation');
                Route::patch('{uuid}/done', 'updateOrderStatusToDone');
            });
        });

    Route::prefix('business')->middleware(['ability:business'])->group(function () {
        Route::prefix('orders')
            ->controller(BusinessOrdersController::class)
            ->group(function () {
                Route::post('/', 'createOrder');
                Route::patch('/{uuid}/cancel', 'cancelOrder');
            });
    });
});
