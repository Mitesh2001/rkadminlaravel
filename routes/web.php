<?php

use Illuminate\Support\Facades\Route;
use App\Models\Company;
use App\Models\Products;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
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

//Auth Routes
//Auth::routes();
/* Auth::routes();

Route::group(['namespace' => 'Front', 'middleware' => ['auth:web']], function () {
    Route::get('/home', 'HomeController@index')->name('home');
});

Route::get('/', function () {
    return view('welcome');
}); */
/* Admin Routes */
// Route::get('/password/reset/{token}', 'Admin\ResetPassword@showResetForm')->name('password.reset');

Route::group(['prefix' => 'rkadmin', 'namespace' => 'Admin', 'as' => 'admin.'], function () {
    Route::get('login', 'Login@index')->name('login');
    Route::post('login', 'Login@login')->name('login_process');
    Route::get('logout', 'Login@logout')->name('logout');

    Route::post('/password/email', 'ForgotPassword@sendResetLinkEmail')->name('password.email');
    /* Route::post('/password/email', function (Request $request) { 
		$status = Password::sendResetLink(
			$request->only('email')
		);

		return $status === Password::RESET_LINK_SENT
			? back()->with(['success' => __($status)])
			: back()->withErrors(['email' => __($status)]);

	})->name('password.email'); */

    Route::get('/password/reset', 'ForgotPassword@showLinkRequestForm')->name('password.request');
    //Route::post('/password/reset', 'ResetPassword@reset')->name('change_password');
    Route::post('/password/reset', 'ForgotPassword@resetPassword')->name('password.update');
    Route::get('/password/reset/{token}', 'ResetPassword@showResetForm')->name('password.reset');
	

    Route::group(['middleware' => ['auth:admin']], function () {

		Route::get('/email-and-sms-log', 'EmailAndSmsController@index');
		Route::get('/email-and-sms-log/data', 'EmailAndSmsController@anyData')->name('email_sms_log.data');
		
        Route::get('/', 'PagesController@dashboard')->name('/');
		Route::get('dashboard', 'PagesController@dashboard')->name('dashboard');
		Route::group(['prefix' => 'roles'], function () {
			Route::get('/data', 'RolesController@anyData')->name('roles.data');
			Route::patch('/update/{id}', 'RolesController@update');
		});
		Route::resource('roles', 'RolesController');
		
		Route::group(['prefix' => 'permissions'], function () {
			Route::get('/data', 'PermissionsController@anyData')->name('permissions.data');
			Route::patch('/update/{id}', 'PermissionsController@update');
		});
        Route::resource('permissions', 'PermissionsController');
		
		/**
         * Clients
         */
        Route::group(['prefix' => 'clients'], function () {
            Route::get('/data', 'ClientsController@anyData')->name('clients.data');
            /* Route::get('/taskdata/{external_id}', 'ClientsController@taskDataTable')->name('clients.taskDataTable');
            Route::get('/projectdata/{external_id}', 'ClientsController@projectDataTable')->name('clients.projectDataTable');
            Route::get('/leaddata/{external_id}', 'ClientsController@leadDataTable')->name('clients.leadDataTable');
            Route::get('/invoicedata/{external_id}', 'ClientsController@invoiceDataTable')->name('clients.invoiceDataTable');
            Route::post('/create/cvrapi', 'ClientsController@cvrapiStart');
            Route::post('/upload/{external_id}', 'DocumentsController@upload')->name('document.upload');
            Route::patch('/updateassign/{external_id}', 'ClientsController@updateAssign');
            Route::post('/updateassign/{external_id}', 'ClientsController@updateAssign'); */
            
        });
        Route::resource('clients', 'ClientsController');
		
		Route::get('clients/export/data','ClientsController@anyData');
        
        /* Route::group(['prefix' => 'products'], function () {
            Route::get('/data', 'ProductsController@anyData')->name('products.data');
            Route::patch('/update/{id}', 'ProductsController@update');
        });
        Route::resource('products', 'ProductsController'); */
		
		//Route::get('/products', array('as' => 'products', 'uses' => 'ProductsController@index()'));
		Route::get('/products/{companyId}', 'ProductsController@index')
		   ->name('products.index');
		Route::get('/services/{companyId}', 'ProductsController@index')
		   ->name('services.index');
		Route::get('/product-category/{companyId}', 'ProductCategoryController@index')
		   ->name('category.index');
		
		Route::get('/products/create/{companyId}', 'ProductsController@create')
		   ->name('products.create');
		Route::get('/services/create/{companyId}', 'ProductsController@create')
		   ->name('services.create');
		Route::get('/product-category/create/{companyId}', 'ProductCategoryController@create')
		   ->name('category.create');
		
		Route::get('/products/{id}/edit', 'ProductsController@edit')
		   ->name('products.edit');
		Route::get('/services/{id}/edit', 'ProductsController@edit')
		   ->name('services.edit');
		Route::get('/product-category/{id}/edit', 'ProductCategoryController@edit')
		   ->name('category.edit');

		Route::get('/master/products', 'ProductsController@index')
		   ->name('master.products.index');
		Route::get('/master/services', 'ProductsController@index')
		   ->name('master.services.index');
		Route::get('/master/product-category', 'ProductCategoryController@index')
		   ->name('master.category.index');
		Route::get('/master/products/create', 'ProductsController@create')
		   ->name('master.products.create');
		Route::get('/master/services/create', 'ProductsController@create')
		   ->name('master.services.create');
		Route::get('/master/product-category/create', 'ProductCategoryController@create')
		   ->name('master.category.create');
		
		Route::get('/master/products/{id}/edit', 'ProductsController@edit')
		   ->name('master.products.edit');
		Route::get('/master/services/{id}/edit', 'ProductsController@edit')
		   ->name('master.services.edit');
		Route::get('/master/product-category/{id}/edit', 'ProductCategoryController@edit')
		   ->name('master.category.edit');		
		
		Route::group(['prefix' => 'products'], function () {
			/* Route::get('/',function () {
				return view('admin.products.index')->with('product_type',1);
			})->name('products.index'); */
			//Route::get('/','ProductsController@index')->where('product_type', '1')->name('products.index');
			Route::get('/data/data', 'ProductsController@anyData')->name('products.data');
			/* Route::get('/create',function () {
				$clients = Company::pluck('company_name', 'id');
				$clients->prepend('Please Select client', '');
				return view('admin.products.create')->with('clients',$clients)->with('product_type',1);
			})->name('products.create'); */
			/* Route::get('/{id}/edit', function ($id) {
				// Only executed if {id} is numeric...
				$product = Products::find($id);
				//$clients = Client::pluck('name', 'id');
				$clients = Company::pluck('company_name', 'id');
				$clients->prepend('Please Select client', '');
				return view('admin.products.edit')
				->withProduct($product)
				->withClients($clients)->with('product_type',1);
			})->name('products.edit'); */
			//Route::get('/create', 'ProductsController@create')->name('products.create');
			Route::post('/store', 'ProductsController@store')->name('products.store');
			//Route::put($uri, $callback)->name('products.create');
			Route::patch('/update/{id}', 'ProductsController@update')->name('products.update');
			Route::patch('/assign', 'ProductsController@assignProduct')->name('products.assign');
		});
		Route::get('products/destroy/{id}', 'ProductsController@destroy')->name('products.destroy');
		Route::group(['prefix' => 'services'], function () {
			/* Route::get('/',function () {
				return view('admin.products.index')->with('product_type',2);
			})->name('services.index'); */
			//Route::get('/','ProductsController@index')->where('product_type', '2')->name('services.index');
			Route::get('/data', 'ProductsController@anyData')->name('services.data');
			//Route::get('/create', 'ProductsController@create')->name('services.create');
			/* Route::get('/create',function () {
				$clients = Company::pluck('company_name', 'id');
				$clients->prepend('Please Select client', '');
				return view('admin.products.create')->with('clients',$clients)->with('product_type',2);
			})->name('services.create'); */
			/* Route::get('/{id}/edit', function ($id) {
				// Only executed if {id} is numeric...
				$product = Products::find($id);
				//$clients = Client::pluck('name', 'id');
				$clients = Company::pluck('company_name', 'id');
				$clients->prepend('Please Select client', '');
				return view('admin.products.edit')
				->withProduct($product)
				->withClients($clients)->with('product_type',2);
			})->name('services.edit'); */
		});
		
		Route::group(['prefix' => 'product-category'], function () {
			Route::get('/data/data', 'ProductCategoryController@anyData')->name('category.data');
			Route::post('/store', 'ProductCategoryController@store')->name('category.store');
			Route::patch('/update/{id}', 'ProductCategoryController@update')->name('category.update');
		});
		Route::get('product-category/destroy/{id}', 'ProductCategoryController@destroy')->name('category.destroy');
			
		//Route::get('/services', array('as' => 'services', 'uses' => 'ProductsController@index()'));
		//Route::get('/products/create', array('as' => 'productcreate', 'uses' => 'ProductsController@create()'));
		//Route::get('/services/create', array('as' => 'servicecreate', 'uses' => 'ProductsController@create()'));
		//Route::resource('products', 'ProductsController');
        Route::get('company/{companyId}','ClientsController@companyShow');
        Route::group(['prefix' => 'employees'], function () {
			Route::get('/create/{companyId?}','EmployeesController@create')->name('employees.create1');
			Route::get('/{companyId}','EmployeesController@index')->name('employees.index1');
            Route::get('/data/data', 'EmployeesController@anyData')->name('employees.data');
            Route::patch('/update/{id}', 'EmployeesController@update')->name('employees.update1');
            Route::get('/permission/{id}', 'EmployeesController@getPermission')->name('employees.getPermission');
            Route::post('/permission', 'EmployeesController@storePermission')->name('employees.storePermission');
		});
		Route::get('invoice', 'EmployeesController@invoice')->name('invoice');
		Route::post('invoice/update', 'EmployeesController@invoiceUpdate')->name('invoiceupdate');
        Route::resource('employees', 'EmployeesController'); 
		Route::group(['prefix' => 'subscriptions'], function () {
			Route::get('/data', 'SubscriptionsController@anyData')->name('subscriptions.data');
			Route::get('/alldata', 'SubscriptionsController@allData')->name('subscriptions.allData');
			Route::get('/plan-details', 'SubscriptionsController@getPlanDetail')->name('subscriptions.plan.detail');
			Route::get('/company-details', 'SubscriptionsController@getCompanyDetail')->name('subscriptions.company.detail');
			Route::get('/plan-delete/{id}', 'SubscriptionsController@deletePlan');
			Route::get('/cancel/{id}', 'SubscriptionsController@cancel');
			Route::get('/all', 'SubscriptionsController@all')->name('subscriptions.all');
			// Route::get('/{companyId}', 'SubscriptionsController@index');
		});	
		Route::resource('subscriptions', 'SubscriptionsController'); 

		Route::get('commissions','CommissionController@index')->name('commissions.index');
		Route::get('/commissions/paid/{dealerDistributor}/{month}/{year}/{status}','CommissionController@makePaid');
		Route::get('/commissions/view/{dealerDistributor}/{month}/{year}/{status}','CommissionController@viewSubscription');
		Route::get('commissions/anyData', 'CommissionController@anyData')->name('commissions.data');

        // custom form 
		Route::get('custom-form/{companyId}','DynamicFormController@index')->name('custom-field.index');
		//Route::get('custom-form/{companyId}','DynamicFormController@anyData')->name('custom-field.allData');
		Route::get('custom-form/create/{companyId}','DynamicFormController@create')->name('custom-field.create');
		Route::post('custom-form/{companyId}','DynamicFormController@store')->name('custom-field.store');
		Route::get('custom-form/{companyId}/{moduleType}/edit','DynamicFormController@edit')->name('custom-field.edit');

        Route::resource('plan', 'PlansController', [
            'names' => [
                'index' => 'plan',
            ]
        ]);
		Route::get('plan/delete/{id}', 'PlansController@destroy');
		Route::get('plan-data', 'PlansController@anyData')->name('plan.data');
		// product category
		/* Route::resource('product-category','ProductCategoryController',[
			'names' => [
                'index' => 'product-category',
            ]
		]);
		Route::delete('product-category/destroy/{id}', 'ProductCategoryController@destroy');
		Route::get('product-category/data/data', 'ProductCategoryController@anyData')->name('product-category.data'); */
		Route::group(['prefix' => 'requisitions'], function () {
			Route::get('/','RequisitionController@index')->name('requisitions');
			Route::post('/status/','RequisitionController@changeStatus')->name('requisitions.status');
			Route::get('/data/data','RequisitionController@anydata')->name('requisitions.data');
		});

		// permission assgin
		Route::post('permission-assign','PermissionsController@permissionAssign')->name('permission.assign');

		// annocument
		Route::resource('announcement','AnnouncementController');
		Route::get('announcement/edit/{id}','AnnouncementController@edit');
		Route::get('announcement/delete/{id}','AnnouncementController@destroy');
		// noticeboard
		Route::resource('notice-board','NoticeBoardController');
		Route::get('notice-board/edit/{id}','NoticeBoardController@edit');
		Route::get('notice-board/get-company-wise-users/{id}','NoticeBoardController@getCompanyWiseUsers');
		Route::get('notice-board/delete/{id}','NoticeBoardController@destroy');
		//email template
		Route::get('emails/send/{id}','EmailsController@send')->name('emails.send');
		Route::get('emails/clientsdata', 'EmailsController@clientsdata')->name('emails.clientsdata');
		Route::post('emails/sendbulkemails', 'EmailsController@sendbulkemails')->name('emails.sendbulkemails');
		Route::get('emails/edit/{id}','EmailsController@edit');
		Route::resource('emails','EmailsController');
		
		Route::get('state/get','ClientsController@getState')->name('getstate');
		Route::get('city/get','ClientsController@getCity')->name('getcity');
		Route::get('postcode/get','ClientsController@getPostcode')->name('getpostcode');
		Route::get('cast/get','ClientsController@getCast')->name('getcast');
		Route::post('cast/add','ClientsController@addCast')->name('addCast');
		Route::get('companies/get','ClientsController@getCompanies')->name('getallcompanies');

		//SMS API Setting
		Route::resource('sms', 'SMSController');
		Route::post('sms-api-test', 'SMSController@testAPI')->name('sms.test_api');
		Route::post('sms-update-parameter', 'SMSController@updateParameters')->name('sms.updateParameters');

		// Global Settings
		Route::resource('settings', 'SettingController');
		
		Route::get('profile','EmployeesController@getProfilePage')->name('profile');
		Route::patch('profile/{id}', 'EmployeesController@profileupdate')->name('profileupdate');
    });
 
});

/* \DB::listen(function($sql) {
    \Log::info($sql->sql);
    \Log::info($sql->bindings);
    \Log::info($sql->time);
}); */
