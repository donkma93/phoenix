<?php

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminInventoryController;
use App\Http\Controllers\Admin\AdminInvoiceController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminPackageController;
use App\Http\Controllers\Admin\AdminPackageGroupController;
use App\Http\Controllers\Admin\AdminPartnerController;
use App\Http\Controllers\Admin\AdminPricingController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminRequestController;
use App\Http\Controllers\Admin\AdminScannerController;
use App\Http\Controllers\Admin\AdminStoreFulfillController;
use App\Http\Controllers\Admin\AdminToteController;
use App\Http\Controllers\Admin\AdminUnitPriceController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminWarehouseAreaController;
use App\Http\Controllers\Admin\AdminWarehouseController;
use App\Http\Controllers\GuestBaseController;
use App\Http\Controllers\PackingListController;
use App\Http\Controllers\PickupRequestController;
use App\Http\Controllers\PricesController;
use App\Http\Controllers\Staff\StaffBaseController;
use App\Http\Controllers\Staff\StaffCategoryController;
use App\Http\Controllers\Staff\StaffDashboardController;
use App\Http\Controllers\Staff\StaffInventoryController;
use App\Http\Controllers\Staff\StaffInvoiceController;
use App\Http\Controllers\Staff\StaffMessengerController;
use App\Http\Controllers\Staff\StaffOrderController;
use App\Http\Controllers\Staff\StaffOrderTrackingController;
use App\Http\Controllers\Staff\StaffPackageController;
use App\Http\Controllers\Staff\StaffPackageGroupController;
use App\Http\Controllers\Staff\StaffProductController;
use App\Http\Controllers\Staff\StaffRequestController;
use App\Http\Controllers\Staff\StaffScannerController;
use App\Http\Controllers\Staff\StaffSettingController;
use App\Http\Controllers\Staff\StaffStoreFulfillController;
use App\Http\Controllers\Staff\StaffToteController;
use App\Http\Controllers\Staff\StaffWarehouseAreaController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserEpacketController;
use App\Http\Controllers\User\UserInventoryController;
use App\Http\Controllers\User\UserInvoiceController;
use App\Http\Controllers\User\UserMessengerController;
use App\Http\Controllers\User\UserOrderController;
use App\Http\Controllers\User\UserPackageGroupController;
use App\Http\Controllers\User\UserPricingController;
use App\Http\Controllers\User\UserRequestController;
use App\Http\Controllers\User\UserSettingController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerifyAccountController;
use App\Http\Controllers\Staff\Order2Controller;

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

Route::get('/generate-barcode-pdf', [PickupRequestController::class, 'generateBarcodePDF']);

Route::get('language/{locale}', function ($locale) {
    App::setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
})->name('lang.change');

Route::get('/', function () {
    $locale = app()->getLocale();
    return redirect()->route('home', ['locale' => $locale]);
})->name('client.home');

Route::get('/{locale}/', function ($locale) {
    if (!in_array($locale, ['en', 'vn'])) {
        return redirect()->route('home', ['locale' => 'en']);
    }
    App::setLocale($locale);
    $user = auth()->user();

    return view('client.home', compact('user'));
})->name('home');

Route::get('/{locale}/terms-of-use', function ($locale) {
    if (!in_array($locale, ['en', 'vn'])) {
        return redirect()->route('term', ['locale' => 'en']);
    }

    App::setLocale($locale);

    return view('term');
})->name('term');

Route::get('/{locale}/verify-account', [VerifyAccountController::class, 'verifyAcc'])->name('verify.account');

Route::post('/pricing-request', [GuestBaseController::class, 'sendRequest'])->name('pricingRequest');

Route::post('/get-states-by-country-id', [StaffOrderController::class, 'getStatesByCountryId'])->name('getStatesByCountryId');
Route::post('/get-cities-by-state-id', [StaffOrderController::class, 'getCitiesByStateId'])->name('getCitiesByStateId');

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard')
            ->middleware('role:admin');
        Route::get('/order-overview', [AdminDashboardController::class, 'board'])->name('orderOverview')
            ->middleware('role:admin');
        // Route::get('/live-shipping', [AdminDashboardController::class, 'liveShipping'])->name('liveShipping')
        //     ->middleware('role:admin');
        Route::get('/export-staff-order-overview/{type}', [AdminDashboardController::class, 'exportExcel'])->name('staffOrderOverview')
            ->middleware('role:admin');

        Route::get('/user/list', [AdminUserController::class, 'list'])->name('user.list')
            ->middleware('role:admin');
        Route::get('/user/new', [AdminUserController::class, 'new'])->name('user.new')
            ->middleware('role:admin');
        Route::get('/user/{id}', [AdminUserController::class, 'profile'])->name('user.profile')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:admin');

        Route::post('/user/createUser', [AdminUserController::class, 'createUser'])->name('user.createUser')
            ->middleware('role:admin');
        Route::post('/user/update', [AdminUserController::class, 'updateUser'])->name('user.update')
            ->middleware('role:admin');

        Route::get('/warehouse/list', [AdminWarehouseController::class, 'list'])->name('warehouse.list')
            ->middleware('role:admin');
        Route::get('/warehouse/new', [AdminWarehouseController::class, 'new'])->name('warehouse.new')
            ->middleware('role:admin');
        Route::get('/warehouse/{id}', [AdminWarehouseController::class, 'detail'])->name('warehouse.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:admin');
        Route::post('/warehouse/addUnitPrice', [AdminWarehouseController::class, 'addUnitPrice'])->name('warehouse.addUnitPrice')
            ->middleware('role:admin');
        Route::post('/warehouse/create', [AdminWarehouseController::class, 'create'])->name('warehouse.create')
            ->middleware('role:admin');
        Route::post('/warehouse/delete', [AdminWarehouseController::class, 'delete'])->name('warehouse.delete')
            ->middleware('role:admin');
        Route::post('/warehouse/deleteUnitPrice', [AdminWarehouseController::class, 'deleteUnitPrice'])->name('warehouse.deleteUnitPrice')
            ->middleware('role:admin');

        Route::get('/warehouse-area/list', [AdminWarehouseAreaController::class, 'list'])->name('warehouseArea.list')
            ->middleware('role:admin');
        Route::get('/warehouse-area/new', [AdminWarehouseAreaController::class, 'new'])->name('warehouseArea.new')
            ->middleware('role:admin');
        Route::get('/warehouse-area/{id}', [AdminWarehouseAreaController::class, 'detail'])->name('warehouseArea.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:admin');
        Route::post('/warehouse-area/create', [AdminWarehouseAreaController::class, 'create'])->name('warehouseArea.create')
            ->middleware('role:admin');
        Route::post('/warehouse-area/update', [AdminWarehouseAreaController::class, 'updateArea'])->name('warehouseArea.update')
            ->middleware('role:admin');

        Route::get('/package/list', [AdminPackageController::class, 'list'])->name('package.list')
            ->middleware('role:admin');
        Route::get('/package/history', [AdminPackageController::class, 'history'])->name('package.history')
            ->middleware('role:admin');
        Route::get('/package/{id}', [AdminPackageController::class, 'detail'])->name('package.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:admin');
        Route::get('/package/history/{id}', [AdminPackageController::class, 'historyDetail'])->name('package.history-detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:admin');
        Route::post('/package/delete', [AdminPackageController::class, 'delete'])->name('package.delete')
            ->middleware('role:admin');

        Route::get('/package-group/list', [AdminPackageGroupController::class, 'list'])->name('package-group.list')
            ->middleware('role:admin');
        Route::get('/package-group/{id}', [AdminPackageGroupController::class, 'detail'])->name('package-group.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:admin');
        Route::post('/package-group/update', [AdminPackageGroupController::class, 'update'])->name('package-group.update')
            ->middleware('role:admin');
        Route::post('/package-group/delete', [AdminPackageGroupController::class, 'delete'])->name('package-group.delete')
            ->middleware('role:admin');
        Route::post('/package-group/compare', [AdminPackageGroupController::class, 'compare'])->name('package-group.compare')
            ->middleware('role:admin');
        Route::get('/package-group-history/list', [AdminPackageGroupController::class, 'history'])->name('package-group-history.list')
            ->middleware('role:admin');
        Route::get('/package-group-history/{id}', [AdminPackageGroupController::class, 'historyDetail'])->name('package-group-history.detail')
            ->middleware('role:admin');

        Route::get('/request/list', [AdminRequestController::class, 'list'])->name('request.list')
            ->middleware('role:admin');
        Route::get('/request/{id}', [AdminRequestController::class, 'detail'])->name('request.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:admin');

        Route::get('/unit-price/list', [AdminUnitPriceController::class, 'list'])->name('unit-price.list')
            ->middleware('role:admin');
        Route::get('/unit-price/{id}', [AdminUnitPriceController::class, 'detail'])->name('unit-price.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:admin');
        Route::post('/unit-price/create', [AdminUnitPriceController::class, 'create'])->name('unit-price.create')
            ->middleware('role:admin');
        Route::post('/unit-price/update', [AdminUnitPriceController::class, 'update'])->name('unit-price.update')
            ->middleware('role:admin');

        Route::get('/scanner', [AdminScannerController::class, 'index'])->name('scanner')
            ->middleware('role:admin');
        Route::post('/check', [AdminScannerController::class, 'checkBarcode'])->name('scanner.check')
            ->middleware('role:admin');

        Route::get('/invoice/list', [AdminInvoiceController::class, 'list'])->name('invoice.list')
            ->middleware('role:admin');
        Route::get('/invoice/{id}', [AdminInvoiceController::class, 'detail'])->name('invoice.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:admin');
        Route::post('/invoice/send', [AdminInvoiceController::class, 'send'])->name('invoice.send')
            ->middleware('role:admin');

        Route::get('/orders', [AdminOrderController::class, 'list'])->name('orders.list')
            ->middleware('role:admin');
        Route::get('/orders/{id}', [AdminOrderController::class, 'detail'])->name('orders.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:admin');


        Route::get('/product', [AdminProductController::class, 'list'])->name('product.list')
            ->middleware('role:admin');
        Route::get('/product/{id}', [AdminProductController::class, 'detail'])->name('product.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:admin');
        Route::post('/product/upload-image', [AdminProductController::class, 'uploadImage'])->name('product.uploadImage')
            ->middleware('role:admin');

        Route::get('/category', [AdminCategoryController::class, 'list'])->name('category.list')
            ->middleware('role:admin');

        Route::get('/inventory', [AdminInventoryController::class, 'list'])->name('inventory.list')
            ->middleware('role:admin');
        Route::get('/inventory/{id}', [AdminInventoryController::class, 'detail'])->name('inventory.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:admin');
        Route::post('/inventory/update', [AdminInventoryController::class, 'update'])->name('inventory.update')
            ->middleware('role:admin');

        Route::get('/store', [AdminStoreFulfillController::class, 'list'])->name('storeFulfill.list')
            ->middleware('role:admin');
        Route::get('/store/{id}', [AdminStoreFulfillController::class, 'detail'])->name('storeFulfill.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:admin');
        Route::get('/store/new', [AdminStoreFulfillController::class, 'new'])->name('storeFulfill.new')
            ->middleware('role:admin');
        Route::post('/store/create', [AdminStoreFulfillController::class, 'create'])->name('storeFulfill.create')
            ->middleware('role:admin');
        Route::post('/store/delete', [AdminStoreFulfillController::class, 'delete'])->name('storeFulfill.delete')
            ->middleware('role:admin');

        Route::get('/tote', [AdminToteController::class, 'list'])->name('tote.list')
            ->middleware('role:admin');
        Route::get('/tote/{id}', [AdminToteController::class, 'detail'])->name('tote.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:admin');
        Route::get('/tote/new', [AdminToteController::class, 'new'])->name('tote.new')
            ->middleware('role:admin');
        Route::post('/tote/create', [AdminToteController::class, 'create'])->name('tote.create')
            ->middleware('role:admin');
        Route::post('/tote/update', [AdminToteController::class, 'update'])->name('tote.update')
            ->middleware('role:admin');
        Route::post('/tote/delete', [AdminToteController::class, 'delete'])->name('tote.delete')
            ->middleware('role:admin');

        Route::get('/notification', [AdminBaseController::class, 'notification'])->name('notification')
            ->middleware('role:admin');

        Route::get('/pricing/list', [AdminPricingController::class, 'list'])->name('pricing.list')
            ->middleware('role:admin');
        Route::post('/pricing/update', [AdminPricingController::class, 'update'])->name('pricing.update')
            ->middleware('role:admin');

        // Partner
        Route::get('/partner', [AdminPartnerController::class, 'list'])->name('partner.list')
            ->middleware('role:admin');
        Route::get('/partner/{id}', [AdminPartnerController::class, 'detail'])->name('partner.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:admin');
        Route::get('/partner/new', [AdminPartnerController::class, 'new'])->name('partner.new')
            ->middleware('role:admin');
        Route::post('/partner/create', [AdminPartnerController::class, 'create'])->name('partner.create')
            ->middleware('role:admin');
        Route::post('/partner/update', [AdminPartnerController::class, 'update'])->name('partner.update')
            ->middleware('role:admin');
        Route::post('/partner/delete', [AdminPartnerController::class, 'delete'])->name('partner.delete')
            ->middleware('role:admin');
    });

    // Staff
    Route::prefix('staff')->name('staff.')->group(function () {

        Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/setting/password', [StaffSettingController::class, 'password'])->name('setting.password')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/setting/profile', [StaffSettingController::class, 'profile'])->name('setting.profile')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/setting/change-password', [StaffSettingController::class, 'changePassword'])->name('setting.changePassword')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/setting/update-profile', [StaffSettingController::class, 'updateProfile'])->name('setting.updateProfile')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/request/list', [StaffRequestController::class, 'listRequest'])->name('request.list')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/request/{id}', [StaffRequestController::class, 'requestDetail'])->name('request.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/request/update', [StaffRequestController::class, 'updateRequest'])->name('request.update')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/request/updatePackage', [StaffRequestController::class, 'updatePackage'])->name('request.updatePackage')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/request/addPackage', [StaffRequestController::class, 'addPackage'])->name('request.addPackage')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/request/get-package', [StaffRequestController::class, 'getPackage'])->name('request.getPackage')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/request/check-package', [StaffRequestController::class, 'checkPackage'])->name('request.checkPackage')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/request/save-package', [StaffRequestController::class, 'savePackage'])->name('request.savePackage')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/request/updateTime', [StaffRequestController::class, 'updateTime'])->name('request.updateTime')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/export-staff-order/{datefrom}/{dateto}', [StaffOrderController::class, 'exportExcel'])->name('staffOrderExport')
            ->middleware('role:picker,packer,receiver,staff');

        Route::get('/package/list', [StaffPackageController::class, 'list'])->name('package.list')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/package/{id}', [StaffPackageController::class, 'detail'])->name('package.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/package/new', [StaffPackageController::class, 'new'])->name('package.new')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/package/outbound', [StaffPackageController::class, 'outbound'])->name('package.outbound')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/package/save', [StaffPackageController::class, 'savePackageIds'])->name('package.save')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/package/updatePackageStatus', [StaffPackageController::class, 'updatePackageStatus'])->name('package.updatePackageStatus')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/package/create', [StaffPackageController::class, 'create'])->name('package.create')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/package/group', [StaffPackageController::class, 'getGroup'])->name('package.getGroup')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/package/update', [StaffPackageController::class, 'update'])->name('package.update')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/package-group/{id}', [StaffPackageGroupController::class, 'detail'])->name('package-group.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/package-group/add', [StaffPackageGroupController::class, 'addPackage'])->name('package-group.add')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/package-group/list', [StaffPackageGroupController::class, 'list'])->name('package-group.list')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/package-group/update', [StaffPackageGroupController::class, 'updateGroup'])->name('package-group.update')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/package-group/new', [StaffPackageGroupController::class, 'new'])->name('package-group.new')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/package-group/create', [StaffPackageGroupController::class, 'create'])->name('package-group.create')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/package-group/createProduct', [StaffPackageGroupController::class, 'createProduct'])->name('package-group.createProduct')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        /*
        Route::get('/user/list', [StaffUserController::class, 'list'])->name('user.list')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/user/{id}', [StaffUserController::class, 'profile'])->name('user.profile')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/user/update', [StaffUserController::class, 'update'])->name('user.update')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        */
        Route::get('/user/list', [AdminUserController::class, 'list'])->name('user.list')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/user/new', [AdminUserController::class, 'new'])->name('user.new')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/user/{id}', [AdminUserController::class, 'profile'])->name('user.profile')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::post('/user/createUser', [AdminUserController::class, 'createUser'])->name('user.createUser')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::post('/user/update', [AdminUserController::class, 'updateUser'])->name('user.update')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::post('/user/change-price', [PricesController::class, 'changePrice'])->name('user.changePrice')
            ->middleware('role:staff');


        Route::get('/order-tracking/list', [StaffOrderTrackingController::class, 'list'])->name('order-tracking.list')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/order-tracking/print', [StaffOrderTrackingController::class, 'print'])->name('order-tracking.print')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket,user');


        Route::get('/notification', [StaffBaseController::class, 'notification'])->name('notification')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/scanner', [StaffScannerController::class, 'index'])->name('scanner')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/check', [StaffScannerController::class, 'checkBarcode'])->name('scanner.check')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/warehouse-area/list', [StaffWarehouseAreaController::class, 'list'])->name('warehouseArea.list')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/warehouse-area/{id}', [StaffWarehouseAreaController::class, 'detail'])->name('warehouseArea.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/warehouse-area/update', [StaffWarehouseAreaController::class, 'updateArea'])->name('warehouseArea.update')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/invoice/list', [StaffInvoiceController::class, 'list'])->name('invoice.list')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/invoice/{id}', [StaffInvoiceController::class, 'detail'])->name('invoice.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/messenger', [StaffMessengerController::class, 'index'])->name('messenger')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/messenger/detail', [StaffMessengerController::class, 'detail'])->name('messenger.detail')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/messenger/new', [StaffMessengerController::class, 'getNewMessage'])->name('messenger.new')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/messenger/send', [StaffMessengerController::class, 'sendMessage'])->name('messenger.send')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/messenger/get', [StaffMessengerController::class, 'getChatBox'])->name('messenger.get')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/orders', [StaffOrderController::class, 'list'])->name('orders.list')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');


        // Route::get('/orders/print', [StaffOrderController::class, 'orderPrintMultiple'])->name('orders.orderPrintMultiple')
        // ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/orders/{id}', [StaffOrderController::class, 'detail'])->name('orders.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/orders/{orderId}/labels/create', [StaffOrderController::class, 'createLabel'])->name('orders.labels.create')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::post('/orders/{orderId}/labels', [StaffOrderController::class, 'storeLabel'])->name('orders.labels.store')
            ->where(['id' => '[0-9]*'])->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/orders/{orderId}/rates/create', [StaffOrderController::class, 'createRate'])->name('orders.rates.create')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/orders/{orderId}/rates', [StaffOrderController::class, 'storeRate'])->name('orders.rates.store')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/orders/new/{id}', [StaffOrderController::class, 'new'])->name('orders.new')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/orders/update', [StaffOrderController::class, 'create'])->name('orders.create')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/orders-csv', [StaffOrderController::class, 'storeCSV'])->name('orders.storeCSV')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/orders/update-status', [StaffOrderController::class, 'updateStatus'])->name('orders.updateStatus')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/orders/update-order', [StaffOrderController::class, 'updateOrder'])->name('orders.updateOrder')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/orders/update-package', [StaffOrderController::class, 'updatePackage'])->name('orders.updatePackage')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/orders/delete-label/{order_id}', [StaffOrderController::class, 'deleteLabel'])->name('delete.label')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:staff,admin');
        Route::get('/orders/delete-order/{order_id}', [StaffOrderController::class, 'deleteOrder'])->name('delete.order')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:staff,admin');
        Route::get('/orders/hold-order/{order_id}', [StaffOrderController::class, 'holdOrder'])->name('hold.order')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:staff,admin');
        Route::get('/orders/resume-order/{order_id}', [StaffOrderController::class, 'resumeOrder'])->name('resume.order')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:staff,admin');

        Route::get('/product', [StaffProductController::class, 'list'])->name('product.list')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/product/{id}', [StaffProductController::class, 'detail'])->name('product.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        // Route::get('/product/new', [StaffProductController::class, 'new'])->name('product.new')
        // ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        // Route::post('/product/create', [StaffProductController::class, 'create'])->name('product.create')
        // ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/product/update', [StaffProductController::class, 'update'])->name('product.update')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/product/create-sku', [StaffProductController::class, 'createSKU'])->name('product.createSKU')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/product/create-component', [StaffProductController::class, 'createKitComponent'])->name('product.createKitComponent')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/product/update-component', [StaffProductController::class, 'updateKitComponent'])->name('product.updateKitComponent')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/product/delete-component', [StaffProductController::class, 'deleteKitComponent'])->name('product.deleteKitComponent')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/category', [StaffCategoryController::class, 'list'])->name('category.list')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/category/new', [StaffCategoryController::class, 'new'])->name('category.new')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/category/create', [StaffCategoryController::class, 'create'])->name('category.create')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/category/update', [StaffCategoryController::class, 'update'])->name('category.update')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/inventory', [StaffInventoryController::class, 'list'])->name('inventory.list')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/inventory/{id}', [StaffInventoryController::class, 'detail'])->name('inventory.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/inventory/updateAvailable', [StaffInventoryController::class, 'updateAvailable'])->name('inventory.updateAvailable')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/inventory/update', [StaffInventoryController::class, 'update'])->name('inventory.update')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/inventory/update-history', [StaffInventoryController::class, 'updateHistory'])->name('inventory.updateHistory')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/store', [StaffStoreFulfillController::class, 'list'])->name('storeFulfill.list')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/store/{id}', [StaffStoreFulfillController::class, 'detail'])->name('storeFulfill.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/tote', [StaffToteController::class, 'list'])->name('tote.list')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/tote/{id}', [StaffToteController::class, 'detail'])->name('tote.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/tote/new', [StaffToteController::class, 'new'])->name('tote.new')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/tote/create', [StaffToteController::class, 'create'])->name('tote.create')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::post('/tote/update', [StaffToteController::class, 'update'])->name('tote.update')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        // Packing List and Pickup Request
        Route::get('/pickup', [PackingListController::class, 'pickupIndex'])->name('pickup.index')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');;
        Route::get('/pickup/{pickup_id}/show', [PackingListController::class, 'pickupShow'])->name('pickup.show')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');
        Route::get('/pickup/orderPrintMultiple', [PackingListController::class, 'orderPrintMultiple'])->name('pickup.orderPrintMultiple')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/packing-list/outbound', [PackingListController::class, 'outbound'])->name('packing.outbound')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/paking-list/{packing_id}/show', [PackingListController::class, 'finishView'])->name('packing.show')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::post('/packing-list/update-master-bill', [PackingListController::class, 'finishPackingListWithMasterBill'])->name('packing.finish')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/packing-list/inbound', [PackingListController::class, 'inbound'])->name('packing.inbound')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::post('/orders/labels/create-via-g7', [StaffOrderController::class, 'createLabelG7'])->name('orders.labels.create.g7')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::get('/labels/import-excel', [StaffOrderController::class, 'createLabelExcelView'])->name('labels.import.excel')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::post('/labels/import-excel-g7', [StaffOrderController::class, 'importLabelG7'])->name('labels.import.excel.g7')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::post('/labels/import-excel-shippo', [StaffOrderController::class, 'importLabelShippo'])->name('labels.import.excel.shippo')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::post('/orders/labels/create-label-other', [StaffOrderController::class, 'createLabelOther'])->name('orders.labels.create.other')
            ->middleware('role:picker,packer,receiver,staff,staff-epacket');

        Route::post('/orders/upload-files', [StaffOrderController::class, '_uploadFiles'])->name('orders.uploadFiles')
            ->middleware('role:staff');

        Route::get('/prices/list/{id}', [PricesController::class, 'tableDetail'])->name('prices.list')
            ->middleware('role:staff');

        Route::get('/price-table/list', [PricesController::class, 'createPriceTable'])->name('priceTable.list')
            ->middleware('role:staff');

        Route::post('/price-table/new', [PricesController::class, 'storePriceTable'])->name('priceTable.store')
            ->middleware('role:staff');

        Route::get('/prices/add', [PricesController::class, 'addPrices'])->name('prices.add')
            ->middleware('role:staff');

        Route::post('/prices/store', [PricesController::class, 'storePrices'])->name('prices.store')
            ->middleware('role:staff');

        Route::post('/prices/delete', [PricesController::class, 'deletePrice'])->name('prices.delete')
            ->middleware('role:staff');

        Route::post('/prices-table/delete', [PricesController::class, 'deletePricesTable'])->name('pricesTable.delete')
            ->middleware('role:staff');

        Route::post('/prices-table/change-status', [PricesController::class, 'changeStatusTable'])->name('pricesTable.changeStatus')
            ->middleware('role:staff');

        Route::post('/import-prices-excel', [StaffOrderController::class, 'importPricesExcel'])->name('importPricesExcel')
            ->middleware('role:staff');

        Route::get('/order-create-new', [Order2Controller::class, 'create'])->name('order2.create')
            ->middleware('role:staff,admin');

        Route::post('/order-create-new', [Order2Controller::class, 'store'])->name('order2.store')
            ->middleware('role:staff,admin');

        Route::get('/add-order-details/{id}', [Order2Controller::class, 'addDetails'])->name('order2.addDetails')
            ->middleware('role:staff,admin');

        Route::post('/add-order-details/{id}', [Order2Controller::class, 'storeDetails'])->name('order2.storeDetails')
            ->middleware('role:staff,admin');

        Route::get('/order-details/{id}', [Order2Controller::class, 'orderDetails'])->name('order2.details')
            ->middleware('role:staff,admin');

        Route::post('/order-details/{id}', [Order2Controller::class, 'updateOrder'])->name('order2.update')
            ->middleware('role:staff,admin');

        Route::get('/get-suggestion-shipper/{keyword}', [Order2Controller::class, 'getSuggestionShipper'])
            ->middleware('role:staff,admin');

        Route::get('/get-suggestion-receiver/{keyword}', [Order2Controller::class, 'getSuggestionReceiver'])
            ->middleware('role:staff,admin');

        Route::get('/list-orders', [Order2Controller::class, 'listOrders'])->name('order2.list')
            ->middleware('role:staff,admin');

        Route::get('/report-orders', [Order2Controller::class, 'report'])->name('order2.report')
            ->middleware('role:staff,admin');
    });

    // User
    Route::prefix('user')->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard')
            ->middleware('role:user,user-epacket');

        Route::get('/dashboard/export', [UserDashboardController::class, 'exportOrderCSV'])->name('dashboard.export')
            ->middleware('role:user,user-epacket');

        Route::get('/dashboard/export-condition', [UserDashboardController::class, 'exportOrderByCondition'])->name('dashboard.exportCondition')
            ->middleware('role:user,user-epacket');

        // e-packet
        Route::get('/pickups', [PickupRequestController::class, 'index'])->name('pickup.index')
            ->middleware('role:user,user-epacket');

        Route::get('/pickup-print', [PickupRequestController::class, 'pickupDetailPrint'])->name('pickup.detail.print')
            ->middleware('role:user,user-epacket');

        Route::get('/pickup-create', [PickupRequestController::class, 'create'])->name('pickup.create')
            ->middleware('role:user,user-epacket');

        Route::post('/pickups', [PickupRequestController::class, 'store'])->name('pickup.store')
            ->middleware('role:user,user-epacket');

        Route::get('/pickup/{pickup_id}', [PickupRequestController::class, 'show'])->name('pickup.show')
            ->middleware('role:user,user-epacket');

        Route::delete('/pickup/{pickup_id}/destroy', [PickupRequestController::class, 'destroy'])->name('pickup.destroy')
            ->middleware('role:user,user-epacket');

        Route::get('/requests', [UserRequestController::class, 'index'])->name('requests.index')
            ->middleware('role:user,user-epacket');


        Route::get('/requests/{userRequestId}', [UserRequestController::class, 'show'])->name('requests.show')
            ->where(['userRequestId' => '[0-9]*'])
            ->middleware('role:user,user-epacket');

        Route::get('/requests/{userRequestId}/edit', [UserRequestController::class, 'edit'])->name('requests.edit')
            ->where(['userRequestId' => '[0-9]*'])
            ->middleware('role:user,user-epacket');

        Route::put('/requests', [UserRequestController::class, 'update'])->name('requests.update')
            ->middleware('role:user,user-epacket');

        Route::get('/requests/create', [UserRequestController::class, 'create'])->name('requests.create')
            ->middleware('role:user,user-epacket');

        Route::post('/requests', [UserRequestController::class, 'store'])->name('requests.store')
            ->middleware('role:user,user-epacket');

        Route::get('/requests/add-package/create', [UserRequestController::class, 'createAddPackage'])->name('requests.add_package.create')
            ->middleware('role:user,user-epacket');

        Route::post('/requests/add-package', [UserRequestController::class, 'storeAddPackage'])->name('requests.add_package.store')
            ->middleware('role:user,user-epacket');

        Route::get('/requests/outbound/create', [UserRequestController::class, 'createOutbound'])->name('requests.outbound.create')
            ->middleware('role:user,user-epacket');

        Route::post('/requests/outbound', [UserRequestController::class, 'storeOutbound'])->name('requests.outbound.store')
            ->middleware('role:user,user-epacket');

        Route::get('/requests/notify', [UserRequestController::class, 'notifyList'])->name('requests.notify.index')
            ->middleware('role:user,user-epacket');

        Route::get('/requests/notify/{id}', [UserRequestController::class, 'notify'])->name('requests.notify')
            ->middleware('role:user,user-epacket');

        Route::post('/requests/cancel', [UserRequestController::class, 'cancel'])->name('requests.cancel')
            ->middleware('role:user,user-epacket');

        Route::get('/setting/profile', [UserSettingController::class, 'showProfile'])->name('setting.profile.index')
            ->middleware('role:user,user-epacket');

        Route::put('/setting/profile', [UserSettingController::class, 'updateProfile'])->name('setting.profile.update')
            ->middleware('role:user,user-epacket');

        Route::get('/setting/password', [UserSettingController::class, 'editPassword'])->name('setting.password.index')
            ->middleware('role:user,user-epacket');

        Route::put('/setting/password', [UserSettingController::class, 'updatePassword'])->name('setting.password.update')
            ->middleware('role:user,user-epacket');

        Route::get('/package_groups', [UserPackageGroupController::class, 'index'])->name('package_groups.index')
            ->middleware('role:user,user-epacket');

        Route::get('/package_groups/{packageId}', [UserPackageGroupController::class, 'show'])->name('package_groups.show')
            ->where(['packageId' => '[0-9]*'])
            ->middleware('role:user,user-epacket');

        Route::get('/package_groups/create', [UserPackageGroupController::class, 'create'])->name('package_groups.create')
            ->middleware('role:user,user-epacket');

        Route::post('/package_groups', [UserPackageGroupController::class, 'store'])->name('package_groups.store')
            ->middleware('role:user,user-epacket');
        Route::post('/package_groups/upload-image', [UserPackageGroupController::class, 'uploadImage'])->name('package_groups.uploadImage')
            ->middleware('role:user,user-epacket');
        Route::post('/package_groups/create-kit-component', [UserPackageGroupController::class, 'createKitComponent'])->name('package_groups.createKitComponent')
            ->middleware('role:user,user-epacket');
        Route::post('/package_groups/update-component', [UserPackageGroupController::class, 'updateKitComponent'])->name('package_groups.updateKitComponent')
            ->middleware('role:user,user-epacket');
        Route::post('/package_groups/delete-component', [UserPackageGroupController::class, 'deleteKitComponent'])->name('package_groups.deleteKitComponent')
            ->middleware('role:user,user-epacket');

        Route::get('/invoice/list', [UserInvoiceController::class, 'list'])->name('invoice.list')
            ->middleware('role:user,user-epacket');
        Route::get('/invoice/{id}', [UserInvoiceController::class, 'detail'])->name('invoice.detail')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:user,user-epacket');

        Route::get('/chat_box', [UserMessengerController::class, 'getChatBox'])->name('chat_box')
            ->middleware('role:user,user-epacket');
        Route::post('/post_message', [UserMessengerController::class, 'update'])->name('post_message')
            ->middleware('role:user,user-epacket');
        Route::get('/new_message', [UserMessengerController::class, 'getNewMessage'])->name('new_message')
            ->middleware('role:user,user-epacket');

        // Fulfill order
        Route::get('/orders', [UserOrderController::class, 'index'])->name('orders.index')
            ->middleware('role:user,user-epacket');

        Route::post('/orders/upload-files', [UserOrderController::class, 'uploadFiles'])->name('orders.uploadFiles')
            ->middleware('role:user');
        Route::post('/orders/update-tracking-info', [UserOrderController::class, 'uploadTrackingInfo'])->name('orders.uploadTrackingInfo')
            ->middleware('role:user');

        Route::get('/orders/{id}', [UserOrderController::class, 'show'])->name('orders.show')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:user,user-epacket');

        Route::get('/orders/create', [UserOrderController::class, 'create'])->name('orders.create')
            ->middleware('role:user,user-epacket');

        Route::post('/orders', [UserOrderController::class, 'store'])->name('orders.store')
            ->middleware('role:user,user-epacket');

        Route::get('/export-user-sku', [UserOrderController::class, 'exportSKUExcel'])->name('userSkuExport')
            ->middleware('role:user,user-epacket');

        Route::post('/orders-csv', [UserOrderController::class, 'storeCSV'])->name('orders.storeCSV')
            ->middleware('role:user,user-epacket');

        Route::get('/inventory', [UserInventoryController::class, 'list'])->name('inventories.list')
            ->middleware('role:user,user-epacket');
        Route::get('/inventory/{id}', [UserInventoryController::class, 'show'])->name('inventories.show')
            ->where(['id' => '[0-9]*'])
            ->middleware('role:user,user-epacket');
        Route::get('/inventory/remind', [UserInventoryController::class, 'remind'])->name('inventories.remind')
            ->middleware('role:user,user-epacket');
        Route::post('/inventory/updateIncomming', [UserInventoryController::class, 'updateIncomming'])->name('inventories.updateIncomming')
            ->middleware('role:user,user-epacket');
        Route::post('/inventory/update-list-remind', [UserInventoryController::class, 'updateListRemind'])->name('inventories.updateListRemind')
            ->middleware('role:user,user-epacket');
        Route::post('/inventory/update-remind', [UserInventoryController::class, 'updateRemind'])->name('inventories.updateRemind')
            ->middleware('role:user,user-epacket');

        Route::get('/inventory-print', [UserInventoryController::class, 'inventoryPrint'])->name('inventory.print')
            ->middleware('role:user,user-epacket');

        // Pricing request
        Route::get('/pricing-request', [UserPricingController::class, 'index'])->name('pricing.index')
            ->middleware('role:user,user-epacket');
        Route::post('/pricing-request/create', [UserPricingController::class, 'create'])->name('pricing.create')
            ->middleware('role:user,user-epacket');

        Route::get('/export-user-order/{datefrom}/{dateto}', [UserOrderController::class, 'exportExcel'])->name('userOrderExport')
            ->middleware('role:user,user-epacket');
    });
});

require __DIR__ . '/auth.php';
