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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Route::group([

//     'middleware' => 'api',
//     // 'namespace' => 'App\Http\Controllers',
//     'prefix' => 'auth'

// ], function ($router) {

//     Route::get('settings', 'SettingsController@index');
//     Route::post('login', 'AuthController@login');
//     Route::post('logout', 'AuthController@logout');
//     Route::post('refresh', 'AuthController@refresh');
//     Route::post('me', 'AuthController@me');

// });


// Route::group(['middleware' => 'jwt.auth'], function ($router) {
//     Route::get('customers', 'CustomersController@all');
//     Route::get('customers/{id}', 'CustomersController@get');
//     Route::post('customers/new', 'CustomersController@new');
// });



Route::group(['prefix' => 'auth'], function () {
    Route::post('/login','AuthController@login');
    Route::post('/check','AuthController@check');
    Route::post('/register','AuthController@register');
    Route::get('/activate/{token}','AuthController@activate');
    Route::post('/password','AuthController@password');
    Route::post('/validate-password-reset','AuthController@validatePasswordReset');
    Route::post('/reset','AuthController@reset');
});

Route::get('/configuration/variable','ConfigurationController@getConfigurationVariable');

Route::post('/check-status','DashboardController@checkStatus');

Route::group(['middleware' => ['auth:api']], function () {


    Route::get('/users','UserController@index');


    Route::post('/auth/refresh', 'AuthController@refresh');
    Route::post('/auth/me', 'AuthController@me');
    Route::post('/auth/logout','AuthController@logout');
    Route::post('/auth/lock','AuthController@lock');
    Route::get('/user/preference/pre-requisite','UserController@preferencePreRequisite');
    Route::post('/user/preference','UserController@preference');

    Route::post('/change-password','AuthController@changePassword');



    Route::get('/configuration','ConfigurationController@index');
    Route::post('/configuration','ConfigurationController@store');
    Route::post('/configuration/logo/{type}','ConfigurationController@uploadLogo');
    Route::delete('/configuration/logo/{type}/remove','ConfigurationController@removeLogo');
    Route::get('/fetch/lists','ConfigurationController@fetchList');

    Route::post('/backup','BackupController@store');
    Route::get('/backup','BackupController@index');
    Route::delete('/backup/{id}','BackupController@destroy');

    Route::get('/locale','LocaleController@index');
    Route::post('/locale','LocaleController@store');
    Route::get('/locale/{id}','LocaleController@show');
    Route::patch('/locale/{id}','LocaleController@update');
    Route::delete('/locale/{id}','LocaleController@destroy');
    Route::post('/locale/fetch','LocaleController@fetch');
    Route::post('/locale/translate','LocaleController@translate');
    Route::post('/locale/add-word','LocaleController@addWord');

    Route::get('/role','RoleController@index');
    Route::get('/role/{id}','RoleController@show');
    Route::post('/role','RoleController@store');
    Route::patch('/role/{id}','RoleController@update');
    Route::delete('/role/{id}','RoleController@destroy');

    Route::get('/permission','PermissionController@index');
    Route::get('/permission/assign/pre-requisite','PermissionController@preRequisite');
    Route::get('/permission/{id}','PermissionController@show');
    Route::post('/permission','PermissionController@store');
    Route::delete('/permission/{id}','PermissionController@destroy');
    Route::post('/permission/assign','PermissionController@assignPermission');


    Route::get('/email-template','EmailTemplateController@index');
    Route::post('/email-template','EmailTemplateController@store');
    Route::get('/email-template/{id}','EmailTemplateController@show');
    Route::patch('/email-template/{id}','EmailTemplateController@update');
    Route::delete('/email-template/{id}','EmailTemplateController@destroy');
    Route::get('/email-template/{category}/lists','EmailTemplateController@lists');
    Route::get('/email-template/{id}/content','EmailTemplateController@getContent');


    Route::get('/user/pre-requisite','UserController@preRequisite');
    Route::get('/user/profile/pre-requisite','UserController@profilePreRequisite');
    Route::get('/user','UserController@index');
    Route::get('/user/{id}','UserController@show');


    Route::post('/user','UserController@store');
    Route::post('/user/{id}/status','UserController@updateStatus');

    Route::patch('/user/{id}','UserController@update');
    Route::patch('/user/{id}/contact','UserController@updateContact');
    Route::patch('/user/{id}/force-reset-password','UserController@forceResetPassword');
    Route::patch('/user/{id}/email','UserController@sendEmail');


    Route::post('/user/profile/update','UserController@updateProfile');
    Route::post('/user/profile/avatar/{id}','UserController@uploadAvatar');
    
    Route::delete('/user/profile/avatar/remove/{id}','UserController@removeAvatar');
    Route::delete('/user/{uuid}','UserController@destroy');


    Route::get('/notification-log','NotificationLogController@index');
    Route::get('/notification-log/{id}','NotificationLogController@show');
    Route::delete('/notification-log/{id}','NotificationLogController@destroy');

    Route::get('/activity-log','ActivityLogController@index');
    Route::delete('/activity-log/{id}','ActivityLogController@destroy');

    Route::get('/category/pre-requisite','CategoryController@preRequisite');
    Route::get('/category','CategoryController@index');
    Route::get('/category/{id}','CategoryController@show');
    Route::post('/category','CategoryController@store');
    Route::post('/category/{id}','CategoryController@update');
    Route::delete('/category/{id}','CategoryController@destroy');

    Route::get('/tax','TaxController@index');
    Route::get('/tax/{id}','TaxController@show');
    Route::post('/tax','TaxController@store');
    Route::post('/tax/{id}','TaxController@update');
    Route::delete('/tax/{id}','TaxController@destroy');

    Route::get('/unit','UnitController@index');
    Route::get('/unit/{id}','UnitController@show');
    Route::post('/unit','UnitController@store');
    Route::post('/unit/{id}','UnitController@update');
    Route::delete('/unit/{id}','UnitController@destroy');

    Route::get('/currency','CurrencyController@index');
    Route::get('/currency/{id}','CurrencyController@show');
    Route::post('/currency','CurrencyController@store');
    Route::post('/currency/{id}','CurrencyController@update');
    Route::delete('/currency/{id}','CurrencyController@destroy');

    Route::get('/store','StoreController@index');
    Route::get('/store/{id}','StoreController@show');
    Route::post('/store','StoreController@store');
    Route::post('/store/{id}','StoreController@update');
    Route::delete('/store/{id}','StoreController@destroy');

    Route::get('/customer/pre-requisite','CustomerController@PreRequisite');
    Route::post('/customer/import-run','CustomerController@importCustomers');
    Route::get('/customer/{id}/repairs','CustomerController@getRepairs');
    Route::get('/customer','CustomerController@index');
    Route::get('/customer/{id}','CustomerController@show');
    Route::post('/customer','CustomerController@store');
    Route::post('/customer/{id}','CustomerController@update');
    Route::delete('/customer/{id}','CustomerController@destroy');

    Route::get('/biller','BillerController@index');
    Route::get('/biller/{id}','BillerController@show');
    Route::post('/biller','BillerController@store');
    Route::post('/biller/{id}','BillerController@update');
    Route::delete('/biller/{id}','BillerController@destroy');

    Route::get('/supplier','SupplierController@index');
    Route::get('/supplier/{id}','SupplierController@show');
    Route::post('/supplier','SupplierController@store');
    Route::post('/supplier/{id}','SupplierController@update');
    Route::delete('/supplier/{id}','SupplierController@destroy');


    Route::post('/repair/sign-repair','RepairController@signRepair');
    Route::patch('/repair/change-status','RepairController@changeStatus');
    Route::patch('/repair/assign','RepairController@assignRepair');
    Route::patch('/repair/send-email','RepairController@sendEmail');
    Route::get('/repair/pre-requisite','RepairController@preRequisite');
    Route::get('/repair/getUploadedFiles/{id}','AttachmentController@getUploadedFiles');
    Route::post('/repair/deleteUploadedFile','AttachmentController@deleteAttachment');
    Route::post('/repair/upload','AttachmentController@upload');
    Route::get('/repair','RepairController@index');
    Route::get('/repair/{id}','RepairController@show');
    Route::post('/repair','RepairController@store');
    Route::post('/repair/{id}','RepairController@update');
    Route::delete('/repair/{id}','RepairController@destroy');


    Route::post('/product/import-run','ProductController@importProducts');
    Route::post('/product/import-stock-run','ProductController@importProductStock');
    Route::get('/product/get-products-suggestions','ProductController@getProductSuggestionns');
    Route::get('/product/get-products','ProductController@getProducts');
    Route::get('/product/pre-requisite','ProductController@preRequisite');
    Route::get('/product/getSubCategory','ProductController@getSubCategory');
    Route::get('/product','ProductController@index');
    Route::get('/product/{id}','ProductController@show');
    Route::post('/product','ProductController@store');
    Route::post('/product/{id}','ProductController@update');
    Route::delete('/product/{id}','ProductController@destroy');
    
    Route::post('/product/{id}/update-stock','ProductController@addStock');


    Route::post('/status/updatePosition','StatusController@updatePosition');
    Route::get('/status/pre-requisite','StatusController@preRequisite');
    Route::get('/status','StatusController@index');
    Route::get('/status/{id}','StatusController@show');
    Route::post('/status','StatusController@store');
    Route::post('/status/{id}','StatusController@update');
    Route::delete('/status/{id}','StatusController@destroy');


    Route::get('/sms-gateway','SmsGatewayController@index');
    Route::get('/sms-gateway/{id}','SmsGatewayController@show');
    Route::post('/sms-gateway','SmsGatewayController@store');
    Route::post('/sms-gateway/{id}','SmsGatewayController@update');
    Route::delete('/sms-gateway/{id}','SmsGatewayController@destroy');

    Route::get('/report/get-stock-data','ReportController@getStockChartData');
    Route::get('/report/get-quantity-alerts','ReportController@getQuantityAlerts');
    Route::get('/report/finance-chart','ReportController@getFinanceData');

    Route::get('/payment/pre-requisite','PaymentController@preRequisite');
    Route::get('/payment','PaymentController@index');
    Route::get('/payment/{id}','PaymentController@show');
    Route::post('/payment','PaymentController@store');
    Route::post('/payment/{id}','PaymentController@update');
    Route::delete('/payment/{id}','PaymentController@destroy');
    
    Route::get('/event/pre-requisite','EventController@preRequisite');
    Route::get('/event','EventController@index');
    Route::get('/event/{id}','EventController@show');
    Route::post('/event','EventController@store');
    Route::post('/event/{id}','EventController@update');
    Route::delete('/event/{id}','EventController@destroy');

    Route::get('/dashboard/pre-requisite','DashboardController@preRequisite');

    Route::post('/send-sms','DashboardController@sendSms');
    Route::post('/send-email','DashboardController@sendEmail');

    Route::patch('/pos/send-email','PosController@sendEmail');
    Route::post('/pos/verifyVoucher','PosController@verifyVoucher');
    Route::post('/pos','PosController@store');
    
    Route::get('/custom-field','CustomFieldController@index');
    Route::get('/custom-field/{id}','CustomFieldController@show');
    Route::post('/custom-field','CustomFieldController@store');
    Route::patch('/custom-field/{id}','CustomFieldController@update');
    Route::delete('/custom-field/{id}','CustomFieldController@destroy');


    Route::get('/pos-register','PosRegisterController@index');
    Route::get('/pos-register/current-register-detail','PosRegisterController@registerDetails');
    Route::post('/pos-register/close-current-register','PosRegisterController@closeRegister');

    Route::get('/pos-register/register-detail/{id}','PosRegisterController@registerDetails');
    Route::post('/pos-register/close-register/{id}','PosRegisterController@closeRegister');
    
    Route::get('/pos-register/check-if-open','PosRegisterController@isOpen');
    Route::get('/pos-register/{id}','PosRegisterController@show');
    Route::post('/pos-register','PosRegisterController@store');


    Route::get('/voucher/pre-requisite','VoucherController@PreRequisite');
    Route::get('/voucher','VoucherController@index');
    Route::get('/voucher/{id}','VoucherController@show');
    Route::post('/voucher','VoucherController@store');
    Route::post('/voucher/{id}','VoucherController@update');
    Route::delete('/voucher/{id}','VoucherController@destroy');


    Route::get('/sale/get-suspended-bills','SaleController@getSuspendedSales');
    Route::get('/sale/get-suspended-bill/{id}','SaleController@getSuspendedBill');
    
    Route::get('/sale','SaleController@index');
    Route::get('/pos/view/{id}','PosController@view');
    Route::delete('/sale/{id}','SaleController@destroy');


    Route::get('/pos/load/{type}','PosController@load');
    

});
