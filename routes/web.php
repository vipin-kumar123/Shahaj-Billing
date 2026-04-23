<?php

use App\Http\Controllers\backend\AccountController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\CustomerController;
use App\Http\Controllers\Backend\DocController;
use App\Http\Controllers\Backend\ExpensesController;
use App\Http\Controllers\Backend\InventoryController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\PurchaseController;
use App\Http\Controllers\Backend\PurchaseReturnController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SaleController;
use App\Http\Controllers\Backend\SaleReturnController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\SubcategoryController;
use App\Http\Controllers\Backend\SupplierController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\SaleReportController;
use App\Http\Controllers\Backend\PurchaseReportController;


Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::post('users/update-status/{id}', [UserController::class, 'updateStatus'])->name('user.isactive');

    /*************************CATEGORY*************************************/
    Route::prefix('categories')
        ->controller(CategoryController::class)
        ->group(function () {
            Route::get('/', 'index')->name('categories.index');
            Route::post('/save', 'store')->name('categories.store');
            Route::post('/edit', 'edit')->name('categories.edit');
            Route::post('/update', 'update')->name('categories.update');
            Route::post('/show', 'show')->name('categories.show');
            Route::post('/delete', 'delete')->name('categories.delete');

            Route::post('/status-update', 'updateStatus')->name('categories.status.update');
        });
    /*************************CATEGORY*************************************/

    /*************************SUBCATEGORY*************************************/
    Route::prefix('subcategories')
        ->controller(SubcategoryController::class)
        ->group(function () {
            Route::get('/', 'index')->name('subcategories.index');
            Route::post('/store', 'store')->name('subcategories.store');
            Route::post('/edit', 'edit')->name('subcategories.edit');
            Route::post('/update', 'update')->name('subcategories.update');
            Route::post('/status-update', 'statusUpdate')->name('subcategories.update.status');
            Route::post('/delete', 'deleteSub')->name('subcategories.delete');
            Route::post('/show', 'show')->name('subcategories.show');
        });
    /*************************SUBCATEGORY*************************************/

    /*************************PRODUCT*************************************/
    Route::prefix('products')->controller(ProductController::class)->group(function () {
        Route::post('/get-subcategories', 'getSubcategories')->name('products.subcategories');
        Route::post('/destroy', 'trash')->name('products.delete');
    });

    Route::resource('products', ProductController::class);
    /*************************PRODUCT*************************************/

    /*************************BRANDS*************************************/
    Route::prefix('brands')
        ->controller(BrandController::class)
        ->group(function () {
            Route::get('/', 'index')->name('brands.index');
            Route::post('/store', 'store')->name('brands.store');
            Route::post('/delete', 'delete')->name('brands.delete');
            Route::post('/edit', 'edit')->name('brands.edit');
            Route::post('/update', 'update')->name('brands.update');
            Route::post('/show', 'show')->name('brands.show');
        });
    /*************************BRANDS*************************************/

    /*************************CUSTOMERS*************************************/
    Route::resource('customers', CustomerController::class);
    Route::prefix('customers')
        ->controller(CustomerController::class)
        ->group(function () {
            Route::post('/status-change', 'isActive')->name('customers.status.change');
            Route::post('/delete', 'deleteData')->name('customers.delete.record');

            Route::post('/get-city', 'stateGetCity')->name('customers.stateGetCity');
        });
    /*************************CUSTOMERS*************************************/

    /*************************SUPPLIER*************************************/
    Route::prefix('supplier')
        ->controller(SupplierController::class)
        ->group(function () {
            Route::get('/', 'index')->name('supplier.index');
            Route::get('/create', 'create')->name('supplier.create');
            Route::post('/create', 'store')->name('supplier.store');
            Route::get('/edit/{id}', 'edit')->name('supplier.edit');
            Route::post('/edit/{id}', 'update')->name('supplier.update');

            Route::post('/get-city', 'stateGetCity')->name('supplier.stateGetCity');
            Route::post('/status-change', 'isActive')->name('supplier.isactive');
        });
    /*************************SUPPLIER*************************************/

    /*************************PROFILE*************************************/
    Route::prefix('profile')
        ->controller(ProfileController::class)
        ->group(function () {
            Route::get('/edit', 'edit')->name('profile.edit');
            Route::post('/edit', 'update')->name('profile.update');
            Route::post('/password', 'changePassword')->name('profile.changePassword');
        });
    /*************************PROFILE*************************************/

    /*************************PROFILE*************************************/
    Route::prefix('setting')
        ->controller(SettingsController::class)
        ->group(function () {
            Route::get('/app-settings', 'appSetting')->name('setting.appSetting');

            Route::post('/general/get', 'getGeneralData')->name('setting.getGeneralData');

            Route::post('/app-general', 'General')->name('setting.General');
            Route::post('/app-logo', 'logoUpdate')->name('setting.logo.update');
            //company route
            Route::post('/company-details', 'CompanyGetData')->name('setting.company.getAll.data');
            Route::post('/company', 'companyStore')->name('setting.company.store');
            Route::post('/get-cities', 'getCities')->name('setting.company.getCities');
        });
    /*************************PROFILE*************************************/

    /*************************PURCHASE CONTROLLER*************************/
    Route::prefix('purchase')
        ->controller(PurchaseController::class)
        ->group(function () {
            Route::get('/', 'index')->name('purchase.index');
            Route::get('/create', 'create')->name('purchase.create');
            Route::post('/create', 'store')->name('purchase.store');
            Route::get('/show/{id}', 'show')->name('purchase.show');
            Route::get('/edit/{id}', 'edit')->name('purchase.edit');
            Route::put('/edit/{id}', 'update')->name('purchase.update');
            //make payment route
            Route::post('/get-data', 'getPurchaseData')->name('purchase.getData');
            Route::put('/payment/{id}', 'makePayment')->name('purchase.makePayment');
            Route::post('/payment-history', 'paymentHistory')->name('purchase.paymentHistory');

            Route::post('/payment/delete', 'purchasePaymentDelete')->name('purchase.payment.delete');

            Route::post('/delete', 'delete')->name('purchase.delete');

            Route::get('/{id}/print', 'printInvoice')->name('purchase.print');
            Route::get('/{id}/pdf', 'GeneratePdf')->name('purchase.pdf');
        });
    /*************************PURCHASE CONTROLLER*************************************/

    /*************************PURCHASE RETURN CONTROLLER*******************************/
    Route::prefix('purchase/return')
        ->controller(PurchaseReturnController::class)
        ->group(function () {
            Route::get('/', 'index')->name('purchase.return.index');
            Route::get('/convert/{id}', 'create')->name('purchase.return.create');
            Route::post('/convert/{id}', 'store')->name('purchase.return.store');
            Route::get('/edit/{id}', 'edit')->name('purchase.return.edit');
            Route::post('/edit/{id}', 'update')->name('purchase.return.update');
            Route::get('/show/{id}', 'show')->name('purchase.return.show');

            //get purchase return data
            Route::post('/get-purchase-return-data', 'GetPurchaseReturnData')->name('purchase.return.GetPurchaseReturnData');
            Route::put('/purchase-return/payment/{id}', 'purchaseReturnPayment')->name('purchase.return.purchaseReturnPayment');

            Route::post('/purchase-return/payment-history', 'purchaseReturnHistory')
                ->name('purchase.return.payment.history');

            Route::post('/purchase-return/delete-payment', 'deleteReturnPayment')->name('purchase.return.delete.payment');


            Route::get('/{id}/print', 'printInvoice')->name('purchase.return.print');
            Route::get('/{id}/pdf', 'invoicePdf')->name('purchase.return.pdf');
        });
    /*************************PURCHASE RETURN CONTROLLER******************************/

    /*************************SALE CONTROLLER*******************************/
    Route::prefix('sale')
        ->controller(SaleController::class)
        ->group(function () {
            Route::get('/', 'index')->name('sale.index');
            Route::get('/create', 'create')->name('sale.create');
            Route::post('/create', 'store')->name('sale.store');
            Route::get('/show/{id}', 'show')->name('sale.show');
            Route::get('/edit/{id}', 'edit')->name('sale.edit');
            Route::put('/edit/{id}', 'update')->name('sale.update');

            Route::post('/delete', 'delete')->name('sale.delete');

            Route::post('/get-sale-data', 'GetSaleData')->name('sale.GetSaleData');
            Route::put('/receive-payment/{id}', 'ReceivePayment')->name('sale.receive.payment');
            Route::post('/receive-history', 'ReceiveHistory')->name('sale.receive.history');

            Route::post('/receive/payment/delete', 'deleteReceivePayment')->name('sale.delete.receive.payment');

            Route::get('/{id}/print', 'print')->name('sale.print');
            Route::get('/{id}/pdf', 'generatePdf')->name('sale.pdf');
        });
    /*************************SALE CONTROLLER******************************/

    /*************************SALE RETURN CONTROLLER*******************************/
    Route::prefix('sale/return')
        ->controller(SaleReturnController::class)
        ->group(function () {
            Route::get('/', 'index')->name('sale.return.index');
            Route::get('/convert/{id}', 'create')->name('sale.return.convert');
            Route::post('/convert/{id}', 'store')->name('sale.return.store');
            Route::get('/edit/{id}', 'edit')->name('sale.return.edit');
            Route::put('/edit/{id}', 'update')->name('sale.return.update');
            Route::get('/show/{id}', 'show')->name('sale.return.show');

            Route::post('/get-data', 'GetSaleReturnData')->name('sale.return.getData');
            Route::post('/refund-payment/{id}', 'refundPayment')->name('sale.return.refundPayment');

            Route::post('/refund-history', 'refundHistory')->name('sale.return.refundHistory');

            Route::post('/delete-refund', 'deleteSaleReturnRefund')->name('sale.return.delete.refund');

            Route::get('/{id}/print', 'returnPrint')->name('sale.return.print');
            Route::get('/{id}/pdf', 'GeneratePdf')->name('sale.return.pdf');
        });
    /*************************SALE RETURN CONTROLLER******************************/


    /*************************INVENTORY CONTROLLER******************************/
    Route::prefix('inventory')->group(function () {

        Route::get('/stock', [InventoryController::class, 'currentStock'])
            ->name('inventory.stock');

        Route::get('/ledger', [InventoryController::class, 'stockLedger'])
            ->name('inventory.ledger');

        Route::get('/adjustment', [InventoryController::class, 'adjustmentForm'])
            ->name('inventory.adjustment');

        Route::post('/get-product-stock', [InventoryController::class, 'productStock'])
            ->name('inventory.get.product.stock');

        Route::post('/adjustment', [InventoryController::class, 'storeAdjustment'])
            ->name('inventory.adjustment.store');
        Route::get('/low-stock', [InventoryController::class, 'lowStock'])
            ->name('inventory.low.stock');
    });
    /*************************INVENTORY CONTROLLER******************************/


    /*************************REPORTS CONTROLLER******************************/
    Route::prefix('reports')->group(function () {
        // invoice sale 
        Route::get('/sales', [SaleReportController::class, 'sales'])->name('reports.sales');
        Route::post('/sales', [SaleReportController::class, 'salesReport'])->name('reports.sales.submit');

        Route::get('/sales/items', [SaleReportController::class, 'itemSales'])->name('reports.item');
        Route::post('/sales/items', [SaleReportController::class, 'itemReport'])->name('reports.items.submit');

        Route::get('/sales/payment', [SaleReportController::class, 'paymentReport'])->name('reports.payment');
        Route::post('/sales/payment', [SaleReportController::class, 'salePaymentReport'])->name('reports.payment.submit');

        //purchase sale
        Route::get('/purchase', [PurchaseReportController::class, 'purchase'])->name('reports.purchase');
        Route::post('/purchase', [PurchaseReportController::class, 'purchaseReport'])->name('reports.purchase.submit');

        Route::get('/purchase/item', [PurchaseReportController::class, 'itemPurchase'])->name('reports.item.purchase');
        Route::post('/purchase/item', [PurchaseReportController::class, 'itemPurchaseReport'])->name('reports.item.submit');

        Route::get('/purchase/payment', [PurchaseReportController::class, 'purchasePayment'])->name('reports.purchase.payment');
        Route::post('/purchase/payment', [PurchaseReportController::class, 'purchasePaymentReport'])->name('reports.purchase.payment.submit');
    });
    /*************************REPORTS CONTROLLER******************************/

    /*************************REPORTS CONTROLLER******************************/
    Route::prefix('accounts')->name('accounts.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::get('/create', [AccountController::class, 'create'])->name('create');
        Route::post('/store', [AccountController::class, 'store'])->name('store');
        Route::post('/edit', [AccountController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [AccountController::class, 'update'])->name('update');
        Route::post('/delete', [AccountController::class, 'deleteData'])->name('destroy');
    });
    /*************************REPORTS CONTROLLER******************************/


    /*************************REPORTS CONTROLLER******************************/
    Route::prefix('expenses')->group(function () {
        // invoice sale 
        Route::get('/index', [ExpensesController::class, 'index'])->name('expenses.index');
        Route::get('/list', [ExpensesController::class, 'list'])->name('expenses.list');
        Route::get('/create', [ExpensesController::class, 'create'])->name('expenses.create');
        Route::post('/store', [ExpensesController::class, 'store'])->name('expenses.store');
        Route::get('/edit/{id}', [ExpensesController::class, 'edit'])->name('expenses.edit');
        Route::post('/edit/{id}', [ExpensesController::class, 'update'])->name('expenses.update');
    });
    /*************************REPORTS CONTROLLER******************************/



    /*************************DOC UPLOAD CONTROLLER***************************/
    Route::prefix('doc')
        ->controller(DocController::class)
        ->group(function () {
            Route::get('/', 'index')->name('doc.index');
            Route::post('/', 'store')->name('doc.convert');
        });
    /*************************DOC UPLOAD CONTROLLER******************************/


    Route::get('/test', function () {
        $password = "mySecret@123";

        $hash = password_hash($password, PASSWORD_DEFAULT);

        echo $hash;



        if (password_verify($password, $hash)) {
            echo "Password correct!";
        } else {
            echo "Invalid password!";
        }
    });
});
