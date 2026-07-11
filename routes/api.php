<?php

use App\Http\Controllers\PickupRequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PackingListController;
use App\Http\Controllers\Staff\StaffOrderController;
use App\Http\Controllers\WebhookShippoController;
use App\Http\Controllers\WebhookG7Controller;
use App\Http\Controllers\Webhook17trackController;
use App\Http\Controllers\WebhookMyibController;
use App\Http\Controllers\User\UserOrderController;
use App\Http\Controllers\User\UserPackageGroupController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

// Customer-only APIs
Route::middleware(['jwt.verify', 'jwt.role:user'])->group(function () {
    Route::prefix('customer')->group(function () {
        Route::post('/order/create', [UserOrderController::class, 'storeApi']);
        Route::post('/save-tracking-webhook-url', [UserOrderController::class, 'saveWebhookUrl']);
        Route::post('/get-order-detail', [UserOrderController::class, 'getOrderDetail']);
        Route::post('/create-package-group', [UserPackageGroupController::class, 'apiCreateProduct']);
    });
});

// Staff warehouse ops (PDA label + package)
Route::middleware(['jwt.verify', 'jwt.role:admin,staff,picker,packer,receiver'])->group(function () {
    Route::get('/auth/user-profile', [AuthController::class, 'userProfile']);

    Route::get('/orders/package', [StaffOrderController::class, 'getOrderPackageApi'])
        ->name('orders.labels.getOrderPackageApi');

    Route::post('/orders/create-label', [StaffOrderController::class, 'createLabelPdaApi'])
        ->name('orders.labels.createLabelPdaApi');

    Route::prefix('pickup')->name('pickup.')->group(function () {
        Route::put('/{pickup_id}/start', [PickupRequestController::class, 'start'])->name('start');
        Route::put('/scan', [PickupRequestController::class, 'scan'])->name('scan');
        Route::put('/{pickup_id}/finish', [PickupRequestController::class, 'finish'])->name('finish');
        Route::put('/{pickup_id}/order-journeys', [PickupRequestController::class, 'getPickupOrderJourneyByID'])->name('orders');
        Route::get('/list', [PickupRequestController::class, 'list'])->name('list');
    });

    Route::prefix('util')->name('util.')->group(function () {
        Route::get('/packinglist/{packing_code}', [PackingListController::class, 'packinglist_search'])->name('packinglist');
        Route::get('/bill/{bill_code}', [PackingListController::class, 'bill_search'])->name('bill');
    });

    Route::prefix('packing-list')->name('packing.')->group(function () {
        Route::get('/list', [PackingListController::class, 'list'])->name('list');
        Route::post('/store', [PackingListController::class, 'store'])->name('store');
        Route::put('/{picking_list_id}/start', [PackingListController::class, 'start'])->name('start');
        Route::put('/scan', [PackingListController::class, 'scan'])->name('scan');
        Route::put('/finish', [PackingListController::class, 'finishApi'])->name('finish');
        Route::put('/receive-scan', [PackingListController::class, 'receive'])->name('receive-scan');
        Route::put('/receive-finish', [PackingListController::class, 'receiveFinish'])->name('receive-finish');
        Route::get('/list-inbound', [PackingListController::class, 'listInboud'])->name('list-inbound');
    });

    Route::prefix('v1')->group(function () {
        Route::post('/check-tracking-exist', [StaffOrderController::class, 'checkTrackingExist']);
        Route::post('/update-tracking-info-by-order-id', [StaffOrderController::class, 'updateTrackingInfoByOrderId']);
        Route::post('/get-label-url-by-order-id', [StaffOrderController::class, 'getLabelUrlByOrderId']);
    });
});

// Profile for any authenticated JWT (customer or staff)
Route::middleware(['jwt.verify'])->group(function () {
    Route::get('/auth/me', [AuthController::class, 'userProfile']);
});

// Webhooks — require shared secret (header Secure-Token / X-Webhook-Token / ?token=)
Route::post('/webhook-shippo', [WebhookShippoController::class, 'handle_data'])
    ->name('webhook.shippo')
    ->middleware('webhooksecure');

Route::post('/myib-webhook', [WebhookMyibController::class, 'handleData'])
    ->name('webhook.myib')
    ->middleware('webhooksecure');

Route::post('/webhook-label-g7', [WebhookG7Controller::class, 'handleData'])
    ->name('webhook.label.g7')
    ->middleware('webhooksecure');

Route::post('/webhook-17track', [Webhook17trackController::class, 'handleData'])
    ->name('webhook.17track')
    ->middleware('webhooksecure');

// Client check endpoints — require JWT staff (no longer public)
Route::middleware(['jwt.verify', 'jwt.role:admin,staff,picker,packer,receiver,user'])->group(function () {
    Route::prefix('client')->name('client.')->group(function () {
        Route::get('/packinglist/{packing_code}', [PackingListController::class, 'packinglist_search'])
            ->name('check.packinglist');
        Route::get('/bill/{bill_code}', [PackingListController::class, 'bill_search'])
            ->name('check.bill');
    });
});

Route::any('{any}', function () {
    return response()->json([
        'status' => 'error',
        'message' => 'Resource not found'
    ], 404);
})->where('any', '.*');
