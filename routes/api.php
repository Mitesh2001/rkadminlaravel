<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::group(['prefix' => 'v1', 'namespace' => 'V1'], function () {
	Route::post('register', 'Auth@register')->name('register');
	Route::post('login', 'Auth@authenticate')->name('login');
    Route::post('forgot-password', 'Auth@forgotpassword')->name('forgot-password');
    Route::post('reset-password', 'Auth@resetpassword')->name('reset-password');
	Route::group(['middleware' => ['jwt.verify']], function () {
		Route::post('update-profile', 'Auth@profileUpdate');
		Route::get('getuser', 'Auth@getAuthenticatedUser');
		Route::post('change-password', 'Auth@changePassword');
		Route::post('logout', 'Auth@logout');

		Route::get('department/all', 'DepartmentsController@getAllDepartments');
        Route::resource('department', 'DepartmentsController');

		Route::get('lead/all', 'LeadsController@getAllLeads');
        Route::resource('lead', 'LeadsController');

		Route::get('contacts/all', 'ContactsController@getAllContacts');
		Route::post('contacts/import', 'ContactsController@importContacts');
		Route::get('contacts/converttolead/{cid}', 'ContactsController@makeLeadFromContact');
        Route::resource('contacts', 'ContactsController');

		Route::get('products/all', 'ProductsController@getAllProducts');
        Route::resource('products', 'ProductsController');

		Route::get('employees/all', 'EmployeesController@getAllEmployees');
        Route::resource('employee', 'EmployeesController');

		Route::get('organization/all', 'OrganizationsController@getAllOrganizations');
        Route::resource('organization', 'OrganizationsController');

		Route::get('country/all', 'CountriesController@getAllCountries');
        Route::resource('country', 'CountriesController');

        Route::get('state/all/{id}', 'StatesController@getAllStates');
        Route::resource('state', 'StatesController');

		Route::get('states/get','StatesController@getState');
		Route::get('city/get','StatesController@getCity');
		Route::get('postcode/get','StatesController@getPostcode');

		Route::get('company-type/all', 'CompanyTypeController@getAllCompanyTypes');
        Route::resource('company-type', 'CompanyTypeController');

		Route::get('industry-type/all', 'IndustryTypeController@getAllIndustryTypes');
        Route::resource('industry-type', 'IndustryTypeController');

		Route::get('status/all/{type}', 'StatusController@getAllStatuses');
        Route::resource('status', 'StatusController');

		Route::get('leads-comments/all/{lid}', 'LeadsCommentsController@getAllLeadsComments');
        Route::resource('leads-comments', 'LeadsCommentsController');

		Route::get('permission/all', 'PermissionsController@getAllPermissions');
        Route::resource('permission', 'PermissionsController');

		Route::get('role/all', 'RolesController@getAllRoles');
        Route::resource('role', 'RolesController');
		
		Route::get('employee/all', 'EmployeesController@getAllEmployees');
        Route::resource('employee', 'EmployeesController');
				
		Route::resource('emailTemplate', 'EmailTemplates');
        Route::resource('smsTemplate', 'SmsTemplates');
		Route::post('smsTemplate/contacts/send','SmsTemplates@send');

		// duplicate email template
		Route::get('emailTemplate/{id}/duplicate','EmailTemplates@duplicate');
		// duplicate sms template
		Route::get('smsTemplate/{id}/duplicate','SmsTemplates@duplicate');

		// cast add or get
		Route::get('cast/all','CastsController@index');
		Route::post('cast/add','CastsController@store');

		// tele caller note
		Route::get('tele-caller-contact/all','TeleCallerContactController@index');
		Route::post('tele-caller-contact/add','TeleCallerContactController@store');
		Route::post('tele-caller-contact/remove','TeleCallerContactController@destroy');
		Route::post('tele-caller-contact/working-status','TeleCallerContactController@workingStatus');
		Route::post('tele-caller-contact/note/add','TeleCallerContactController@addNote');
		Route::post('tele-caller-contact/note/all','TeleCallerContactController@getNote');

		Route::get('tele-caller-contact/user','TeleCallerContactController@getTeleCallerUser');

		// custom field dynamic form
		Route::get('module/all','DynamicFormController@getModule');
		Route::get('dynamic-form/{id}','DynamicFormController@index');
		Route::put('dynamic-form/{formType}/{type}/{formId?}','DynamicFormController@store');
		Route::get('dynamic-form-value/{type}/{dynamicFormId}','DynamicFormController@getFormValue');
		Route::get('all-dynamic-form/{formType}','DynamicFormController@allDynamicForm');

		// follow up api for lead and contact
		Route::post('follow-up/add','LeadsController@addFollowUp');
		Route::post('follow-up/all','LeadsController@getFollowUp');

		// interested product store
		Route::post('interested-product/add','ContactsController@addInterestedProduct');

		// get all product category
		Route::get('product-category/all','ProductsController@getProductCategory');
		Route::post('product-category/add','ProductsController@addProductCategory');

		// lead assign
		Route::post('lead-assign','LeadsController@leadAssign');

		// get lead assign
		Route::get('lead-assign/all','LeadsController@getLeadAssgin');

		Route::get('convert-lead-to-customer/{leadId}','LeadsController@convertLeadToCustomer');

		Route::get('lead-lock-unlock','LeadsController@leadLockUnlock');

		// add lead stage
		Route::post('lead-stage','LeadsController@leadStage');
		// get lead stage
		Route::get('lead-stage/{id}','LeadsController@getLeadStage');
		// send email for lead
		Route::post('quick-email','LeadsController@sendEmail');
		// send email for lead
		Route::post('send-email/contact','LeadsController@sendEmail');
		Route::post('send-email/sendbulk','LeadsController@sendBulkEmils');
		Route::post('send-email/lead','LeadsController@sendEmail');

		Route::post('requisition/store','RequisitionController@store');
		Route::get('requisition/all','RequisitionController@index');

		// get contact category
		Route::get('contact-category/all','ContactsController@getContactCategory');

		Route::get('remove-interested-product/{id}','ContactsController@removeIntrestedProduct');

		// query
		Route::post('query-rule/add','QueryController@addRule');
		Route::get('query-rule/all','QueryController@getRule');
		Route::put('query-rule/{id}','QueryController@updateRule');
		Route::delete('query-rule/{id}','QueryController@deleteRule');

		// get query role
		Route::get('query-rule/{id}','QueryController@showRule');

		// get table and column name
		Route::get('table/all','QueryController@getTableName');
		Route::get('column/{tableName}','QueryController@getColumnList');
		Route::get('column-structure/{tableName}','QueryController@columnStructure');

		// follow up assign
		Route::post('follow-up-assign','LeadsController@followupAssign');
		Route::get('follow-up/{id}','LeadsController@showFollowup');

		// get summary report
		Route::post('summary-report','QueryController@summaryReport');
		Route::get('groupby-column/{tableName}','QueryController@groupByColumn');

		// get lead assign log
		Route::post('assign-log','LeadsController@getAssignLog');

		// add user permission
		Route::post('user-permission/add','PermissionsController@userPermission');
		Route::get('user-permission/all','PermissionsController@getUserPermission');

		// dashbpard api
		Route::get('dashboard','DashboardController@index');
		Route::get('invoice-download/{id}','DashboardController@invoiceDownload');

		// event api
		Route::get('events','EventController@index');
		Route::post('event/add','EventController@store');
		Route::put('event/{id}','EventController@update');
		Route::get('event/{id}','EventController@show');
		Route::get('event-delete/{id}','EventController@delete');

		// announcement
		Route::post('announcement-history','DashboardController@announcementHistory');
		Route::get('email-and-sms-log', 'EmailAndSmsController@index');

		Route::get('announcement/all', 'AnnouncementController@all');
		Route::get('notice/all', 'NoticeController@all');
	});
});