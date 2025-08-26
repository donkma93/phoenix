<?php

use App\Http\Controllers\PickupRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PackingListController;
use App\Http\Controllers\Staff\StaffOrderController;
use App\Models\PickupRequest;
use App\Http\Controllers\WebhookShippoController;
use App\Http\Controllers\WebhookG7Controller;
use App\Http\Controllers\Webhook17trackController;
use App\Http\Controllers\User\UserOrderController;
use App\Http\Controllers\User\UserPackageGroupController;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

Route::middleware(['jwt.verify'])->group(function () {
    // User profile
    Route::get('/auth/user-profile', [AuthController::class, 'userProfile']);

    // Get order package info
    Route::get('/orders/package', [StaffOrderController::class, 'getOrderPackageApi'])
        ->name('orders.labels.getOrderPackageApi');

    // Create Label PDA
    Route::post('/orders/create-label', [StaffOrderController::class, 'createLabelPdaApi'])
        ->name('orders.labels.createLabelPdaApi');


    Route::prefix('customer')->group(function () {
        Route::post('/order/create', [UserOrderController::class, 'storeApi']);
        Route::post('/save-tracking-webhook-url', [UserOrderController::class, 'saveWebhookUrl']);
        Route::post('/get-order-detail', [UserOrderController::class, 'getOrderDetail']);
        Route::post('/create-package-group', [UserPackageGroupController::class, 'apiCreateProduct']);
    });
});

Route::middleware(['jwt.verify'])->group(function () {

    Route::prefix('pickup')->name('pickup.')->group(function () {
        // Pickup start
        Route::put('/{pickup_id}/start', [PickupRequestController::class, 'start'])
            ->name('start');

        // Pickup scan
        Route::put('/scan', [PickupRequestController::class, 'scan'])
            ->name('scan');

        // Pickup finish
        Route::put('/{pickup_id}/finish', [PickupRequestController::class, 'finish'])
            ->name('finish');

        Route::put('/{pickup_id}/order-journeys', [PickupRequestController::class, 'getPickupOrderJourneyByID'])
            ->name('orders');

        // Pickup list
        Route::get('/list', [PickupRequestController::class, 'list'])
            ->name('list');
    });


    Route::prefix('util')->name('util.')->group(function () {

        Route::get('/packinglist/{packing_code}', [PackingListController::class, 'packinglist_search'])
            ->name('packinglist')
            ->middleware('role:picker,packer,receiver,staff');

        Route::get('/bill/{bill_code}', [PackingListController::class, 'bill_search'])
            ->name('bill')
            ->middleware('role:picker,packer,receiver,staff,user,user-epacket');
    });




    Route::prefix('packing-list')->name('packing.')->group(function () {
        Route::get('/list', [PackingListController::class, 'list'])
            ->name('list')
            ->middleware('role:picker,packer,receiver,staff');

        Route::post('/store', [PackingListController::class, 'store'])
            ->name('store')
            ->middleware('role:picker,packer,receiver,staff');

        Route::put('/{picking_list_id}/start', [PackingListController::class, 'start'])
            ->name('start')
            ->middleware('role:picker,packer,receiver,staff');

        Route::put('/scan', [PackingListController::class, 'scan'])
            ->name('scan')
            ->middleware('role:picker,packer,receiver,staff');

        Route::put('/finish', [PackingListController::class, 'finishApi'])
            ->name('finish')
            ->middleware('role:picker,packer,receiver,staff');

        Route::put('/receive-scan', [PackingListController::class, 'receive'])
            ->name('receive-scan')
            ->middleware('role:picker,packer,receiver,staff');

        Route::put('/receive-finish', [PackingListController::class, 'receiveFinish'])
            ->name('receive-finish')
            ->middleware('role:picker,packer,receiver,staff');

        Route::get('/list-inbound', [PackingListController::class, 'listInboud'])
            ->name('list-inbound')
            ->middleware('role:picker,packer,receiver,staff');
    });
});

Route::middleware(['jwt.verify'])->group(function () {
    Route::prefix('v1')->group(function () {
        Route::post('/check-tracking-exist', [StaffOrderController::class, 'checkTrackingExist']);
        Route::post('/update-tracking-info-by-order-id', [StaffOrderController::class, 'updateTrackingInfoByOrderId']);
        Route::post('/get-label-url-by-order-id', [StaffOrderController::class, 'getLabelUrlByOrderId']);
    });
});


Route::post('/webhook-shippo', [WebhookShippoController::class, 'handle_data'])->name('webhook.shippo');

Route::post('/webhook-label-g7', [WebhookG7Controller::class, 'handleData'])
    ->name('webhook.label.g7')
    ->middleware('webhooksecure');

Route::post('/webhook-17track', [Webhook17trackController::class, 'handleData'])->name('webhook.17track');


Route::prefix('client')->name('client.')->group(function () {

    Route::get('/packinglist/{packing_code}', [PackingListController::class, 'packinglist_search'])
        ->name('check.packinglist');

    Route::get('/bill/{bill_code}', [PackingListController::class, 'bill_search'])
        ->name('check.bill');
});


Route::any('{any}', function () {
    return response()->json([
        'status' => 'error',
        'message' => 'Resource not found'
    ], 404);
})->where('any', '.*');
