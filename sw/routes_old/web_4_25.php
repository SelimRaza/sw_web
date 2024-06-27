<?php

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


/*Route::group([
    'middleware' => 'my_db'
], function () {*/

Route::get('/', function () {
    if (Auth::check()) {
        return Redirect::route('home');
    }
    return Redirect::route('login')->withInput()->with('message', 'Please Login to access restricted area.');
});
Auth::routes();
//Route::get('/home', 'HomeController@index')->name('home');

//filtersales-group

//promotion start
Route::post('promotion/filterItem', 'Promotion\PromotionController@filterItem');
/*Route::post('promotion/mappingRemove/{id}', 'Promotion\PromotionController@zoneMappingDelete');
Route::get('promotion/showZone/{id}', 'Promotion\PromotionController@zoneMappingShow');*/
Route::match(['GET', 'POST'], 'promotion/addZone/{id}', 'Promotion\PromotionController@zoneMappingAdd');
Route::match(['GET', 'POST'], 'promotion/reportFilter', 'Promotion\PromotionController@promotionFilter');

Route::resource('promotion', 'Promotion\PromotionController');
Route::resource('district', 'MasterData\DistrictController');

/*------------------- promotion special start -----------------*/
Route::match(['GET', 'POST'], 'promotion_sp', 'Promotion\PromotionController@promotionSP');
Route::match(['GET', 'POST'], 'promotion_sp/filter/report', 'Promotion\PromotionController@promotionSPFilter');
Route::match(['GET', 'POST'], 'promotion_sp/show/{id}', 'Promotion\PromotionController@promotionSpShow');
Route::match(['GET', 'POST'], 'promotion_sp/extend_date/{id}', 'Promotion\PromotionController@promotionExtendDate');
Route::match(['GET', 'POST'], 'promotion_sp/extend_date_save', 'Promotion\PromotionController@promotionExtendDateSave');
Route::match(['GET', 'POST'], 'promotion_sp/p_copy/{id}', 'Promotion\PromotionController@promotionCopyAs');
Route::match(['GET', 'POST'], 'promotion_sp/inactive/{id}', 'Promotion\PromotionController@promotionInactive');
/*------------------- promotion special end -----------------*/

Route::get('promotion/create/new', 'Promotion\PromotionController@promotionNewCreate');
Route::get('promotion/store/new', 'Promotion\PromotionController@promotionNewStore');
Route::post('promotion/store/new', 'Promotion\PromotionController@promotionNewStore');

Route::POST('promotion/create/exist','Promotion\PromotionController@addingPromotionFromExistingPromotion')->name('promotion.create.exist');
Route::POST('/promotion/filterPrice&CalcDisc','Promotion\PromotionController@showItemPriceAndDisc');
/*------------------- promotion end -----------------*/





Route::resource('category', 'MasterData\CategoryController');
Route::match(['GET', 'POST'],'passReset', 'MasterData\CategoryController@passReset');

Route::post('data_upload/categoryMasterUpload', 'MasterData\CategoryController@categoryMasterUploadInsert');
Route::get('data_upload/categoryUploadFormat', 'MasterData\CategoryController@categoryFormatGen');



Route::resource('sub-category', 'MasterData\SubCategoryController');


//================Channel Controller start=============
Route::resource('channel', 'MasterData\ChannelController');
Route::post('channel/file/insert', 'MasterData\ChannelController@channelFileUpload');
Route::get('channel/format/download', 'MasterData\ChannelController@channelFormatGen');
//================Channel Controller end=============

//================Sub Channel Controller start=============
Route::resource('sub_channel', 'MasterData\SubChannelController');
Route::post('sub_channel/file/insert', 'MasterData\SubChannelController@subChannelFileUpload');
Route::get('sub_channel/format/download', 'MasterData\SubChannelController@subChannelFormatGen');
//================Sub Channel Controller end=============

//================Outlet Category Controller start=============
Route::resource('outlet_grade', 'MasterData\OutletGradeController');
Route::post('outlet_grade/file/insert', 'MasterData\OutletGradeController@outletGradeFileUpload');
Route::get('outlet_grade/format/download', 'MasterData\OutletGradeController@outletGradeFormatGen');
//================Outlet Category Controller end=============


//================Return Reason Controller start=============
Route::resource('return_reason', 'MasterData\ReturnReasonController');
Route::post('return_reason/file/insert', 'MasterData\ReturnReasonController@returnReasonFileUpload');
Route::get('return_reason/format/download', 'MasterData\ReturnReasonController@returnReasonFormatGen');
//================Return Reason Controller end=============


//================Vehicle Controller start=============
Route::resource('vehicle', 'MasterData\VehicleController');
Route::post('vehicle/file/insert', 'MasterData\VehicleController@vehicleFileUpload');
Route::get('vehicle/format/download', 'MasterData\VehicleController@vehicleFormatGen');
//================Vehicle Controller end=============


//================No Order reason Controller start=============
Route::resource('no_order_reason', 'MasterData\NoOrderReasonController');
Route::post('nor/file/insert', 'MasterData\NoOrderReasonController@norFileUpload');
Route::get('nor/format/download', 'MasterData\NoOrderReasonController@norFormatGen');
//================No Delivery reason Controller end=============

//================No Delivery reason Controller start=============
Route::resource('no_delivery_reason', 'MasterData\NoDeliveryReasonController');
Route::post('ndr/file/insert', 'MasterData\NoDeliveryReasonController@ndrFileUpload');
Route::get('ndr/format/download', 'MasterData\NoDeliveryReasonController@ndrFormatGen');
//================No Delivery reason Controller end=============

//================Order Cancel Reason Controller start=============
Route::resource('cancel_order_reason', 'MasterData\CancelOrderReasonController');
Route::post('ocr/file/insert', 'MasterData\CancelOrderReasonController@ocrFileUpload');
Route::get('ocr/format/download', 'MasterData\CancelOrderReasonController@ocrFormatGen');
//================Order Cancel Reason Controller end=============

//================bank type Controller start=============
Route::resource('bank', 'MasterData\BankController');
Route::post('bank/file/insert', 'MasterData\BankController@bankListFileUpoad');
Route::get('bank/format/download', 'MasterData\BankController@bankListFormatGen');
//================bank type Controller end=============

//================Note type Controller=============
Route::resource('notetype', 'MasterData\NoteTypeController');
Route::post('role/file/insert', 'MasterData\NoteTypeController@roleMasterFileUpoad');
Route::get('noteType/format/download', 'MasterData\NoteTypeController@noteTypeFormatGen');
//================Note type Controller=============

Route::resource('outofstock', 'MasterData\OutOfStockController');

Route::get('test', 'TestController@index');
Route::get('test/home', 'TestController@home');
Route::get('test/menu', 'TestController@menu');

//================Demo Controller=============

Route::get('demo', 'Demo\DemoController@index');
Route::get('/json/load/company/group_name','Demo\DemoController@jsonLoadCompanyGroupName');
Route::get('/load/company/gorup','Demo\DemoController@loadComapnyGroup');
Route::get('/show/gorup/user/{id}','Demo\DemoController@showGroupUser');
Route::get('/employee/group_permission/{id}','Demo\DemoController@employeeGroupPermission');
Route::get('/json/assign_emp/group_zoon_permission','Demo\DemoController@assignEmpGroupZonePermission');


//================End==============================
Route::get('printer/order/{cont_id}/{order_id}', 'PrintData\PrintController@orderPrint');
Route::get('printer/salesInvoice/{cont_id}/{order_id}', 'PrintData\PrintController@salesInvoicePrint');

Route::get('export/svClassOrder/{country_id}/{manager_id}/{start_date}/{end_date}', 'DataExport\MobileDataExportController@svClassData');

Route::get('printer/return/{cont_id}/{order_id}', 'PrintData\PrintController@returnPrint');
Route::get('printer/returnInvoice/{cont_id}/{order_id}', 'PrintData\PrintController@returnInvoicePrint');
Route::get('printer/collectionPrint/{cont_id}/{collection_id}', 'PrintData\PrintController@collectionPrint');

Route::resource('dashboardPermission', 'Setting\DashboardPermissionController');
Route::resource('dashboard1Permission', 'Setting\Dashboard1PermissionController');
Route::get('setting/process', 'Setting\ProcessRunController@index');
Route::post('setting/process', 'Setting\ProcessRunController@store');
Route::get('/home', 'TestController@home')->name('home');
Route::post('load/top10/data','TestController@getTopData');
Route::post('load/bottom10/data','TestController@getBottomData');
//=====================Master data role controller start =========//
Route::resource('role', 'MasterData\RoleController');
//file upload
Route::post('role/file/insert', 'MasterData\RoleController@roleMasterFileUpoad');
Route::get('role/format/download', 'MasterData\RoleController@roleFormatGen');
//=====================Master data role controller end =========//
//=====================Dig Down Approach=========//
 Route::post('/load_under_employee','TestController@loadUnderEmployee');
 Route::get('get_user_basis_data/{id}','TestController@dashboardLoadAccordingToUser')->name('get_user_basis_data');
 //====================Bottom Lebel data Show(like-off SR)
 Route::get('getOffSRList/{id}','TestController@getOffSRList');
 Route::get('getCatWiseOutVisit/{id}/{date}','Report\CommonReportController@getCatWiseOutVisit');
 Route::get('getVisitedOutletDetails/{id}/{date}/{cat_id}','Report\CommonReportController@getVisitedOutletDetails');
 Route::get('getCatWiseOutVisit/{id}','TestController@getCatWiseOutVisitDashboard');
 Route::get('getVisitedOutletDetailsDashboard/{id}/{cat_id}','TestController@getVisitedOutletDetailsDashboard');

Route::post('groupEmployee/filterEmployeeByGroup', 'GroupDataSetup\GroupEmployeeController@filterEmployeeByGroup');
Route::put('groupEmployee/{id}/reset', 'GroupDataSetup\GroupEmployeeController@reset');
Route::get('groupEmployee/{id}/passChange', 'GroupDataSetup\GroupEmployeeController@passChange');
Route::put('groupEmployee/{id}/change', 'GroupDataSetup\GroupEmployeeController@change');
Route::resource('groupEmployee', 'GroupDataSetup\GroupEmployeeController');


Route::get('employee/aemp_rpln_delete/{id}', 'MasterData\EmployeeController@aempRplnDelete');
Route::post('employee/aemp_rpln_add/{id}', 'MasterData\EmployeeController@aempRplnAdd');
Route::get('employee/aemp_dlrm_delete/{id}', 'MasterData\EmployeeController@aempDlrmDelete');
Route::post('employee/aemp_dlrm_add/{id}', 'MasterData\EmployeeController@aempDlrmAdd');
Route::get('employee/aemp_slgp_delete/{id}', 'MasterData\EmployeeController@aempSlgpDelete');
Route::post('employee/aemp_slgp_add/{id}', 'MasterData\EmployeeController@aempSlgpAdd');
Route::get('employee/aemp_acmp_delete/{id}', 'MasterData\EmployeeController@aempAcmpDelete');
Route::post('employee/aemp_acmp_add/{id}', 'MasterData\EmployeeController@aempAcmpAdd');

Route::get('employee/aemp_slgp_zone_delete/{id}', 'MasterData\EmployeeController@aempSlgpZoneDelete');
Route::post('employee/aemp_slgp_zone_add/{id}', 'MasterData\EmployeeController@aempSlgpZoneAdd');


Route::get('employee/profileEdit', 'MasterData\EmployeeController@profileEdit');
Route::put('employee/profileEdit/{id}', 'MasterData\EmployeeController@profileUpdate');

Route::post('employee/filterEmployeeByGroup', 'MasterData\EmployeeController@filterEmployeeByGroup');
Route::put('employee/{id}/reset', 'MasterData\EmployeeController@filterEmployeeByGroup');
Route::put('employee/{id}/reset', 'MasterData\EmployeeController@reset');
Route::get('employee/{id}/passChange', 'MasterData\EmployeeController@passChange');
Route::put('employee/{id}/change', 'MasterData\EmployeeController@change');
//Route::get('employee/employeeUploadFormat', 'MasterData\EmployeeController@employeeUploadFormat');
Route::get('employee/employeeUploadFormat', 'MasterData\EmployeeController@employeeUploadFormatGen');
Route::get('employee/employeeUpload', 'MasterData\EmployeeController@employeeUpload');
Route::post('employee/employeeUpload', 'MasterData\EmployeeController@employeeUploadInsert');
Route::get('/show_password','MasterData\EmployeeController@showPassword');
Route::get('/json/load/password_details','MasterData\EmployeeController@jsonShowPassword');

//==============================Master Data Upload  Start=========================


Route::get('upload_setup_data', 'MasterData\EmployeeController@masterdataUpload');

Route::get('depot/depotFormat', 'Depot\DepotController@depotFormatGen');
Route::get('pjp/routePlanUploadFormatGenerate', 'GroupDataSetup\PJPController@routePlanUploadFormatGen');


//==============================Master Data Upload  End=========================

//==============================sr activity Data  Start=========================
Route::get('order_summary_report', 'Report\OrderSummaryController@orderSummaryReport');
Route::get('load/filter/order_summary_report/filter', 'Report\OrderSummaryController@orderSummaryReportFilter');
Route::post('load/filter/order_summary_report/filter', 'Report\OrderSummaryController@orderSummaryReportFilter');
Route::post('/json/get/market-wise/outlet', 'Report\OrderSummaryController@loadMarketWiseOutlet');
Route::get('/json/get/market-wise/outlet', 'Report\OrderSummaryController@loadMarketWiseOutlet');

Route::post('/json/get/market-wise/item', 'Report\ItemReportController@itemClassMarket');
Route::get('/json/get/market-wise/item', 'Report\ItemReportController@itemClassMarket');


Route::get('weekly_order_summary', 'Report\WeeklyOrderSummaryController@weeklyorderSummaryReport');

Route::get('report', 'Report\CommonReportController@weeklyorderSummaryReport');
//**********************Test Report user wise***********************
//Route::get('report_cm', 'Report\CommonReportController@newCommon');
Route::get('getTrackingRecord', 'Report\CommonReportController@getTrackingRecord');
Route::post('getUserWiseReport', 'Report\CommonReportController@getUserWiseReport');

Route::post('getGvtTrackingRecord', 'Report\CommonReportController@getTrackingRecordGvt');
Route::post('getGvtDeeperSalesData', 'Report\CommonReportController@getGvtDeeperSalesData');
    //Route Outlet
Route::get('getDeviationData', 'Report\CommonReportController@getDeviationData');
Route::post('getDeviationDeeperData', 'Report\CommonReportController@getDeviationDeeperData');
Route::post('getDeviationDeeperData/{stage}', 'Report\CommonReportController@getDateWiseDeviationData');
Route::get('getNoteTaskReport', 'Report\CommonReportController@getNoteTaskReport');
Route::post('/getUserAndDateWiseTaskNote', 'Report\CommonReportController@getUserAndDateWiseTaskNote');
Route::get('getTaskDetails/{id}/{date}', 'Report\CommonReportController@getTaskDetails');
Route::get('getSrMovementSummary', 'Report\CommonReportController@getSrMovementSummary');
Route::post('getSrMovementSummaryDeeparData', 'Report\CommonReportController@getSrMovementSummaryDeeparData');
    //Outlet Visit
//Route::get('getDeviationData/company', 'Report\CommonReportController@getCompany');
Route::post('getDeviationData/outletVisit/{stage}', 'Report\CommonReportController@getDeviationOutletVisit');
Route::get('getDeviationData/outletVisit/{stage}', 'Report\CommonReportController@getDeviationOutletVisit');
Route::post('/report/getSRList', 'Report\CommonReportController@getSRList');
Route::post('/getHistoryReportData', 'Report\CommonReportController@getHistoryReportData');
Route::get('getWardWiseVisitDetails/{id}/{date}', 'Report\CommonReportController@getWardWiseVisitDetails');
Route::post('showWardWiseVisitOutletDetails', 'Report\CommonReportController@showWardWiseVisitOutletDetails');
Route::post('showCatWiseOutlet', 'Report\CommonReportController@showCatWiseOutlet');
Route::post('showAllVisitedOutletList', 'Report\CommonReportController@showAllVisitedOutletList');
Route::post('getMap', 'Report\CommonReportController@getMapData');
Route::post('getEmpAttendanceReport', 'Report\CommonReportController@getEmpAttendanceReport');
Route::post('export_emp_attendance_data', 'Report\CommonReportController@expEmpAttendanceData');
Route::post('getGeneratedReportList', 'Report\CommonReportRequestController@getGeneratedReportList');
Route::get('gvtDataProcess', 'Report\CommonReportController@gvtDataProcess');
//**********************Test Report user wise End***********************
Route::get('load/filter/common_sr_activity_filter/demo2', 'Report\CommonReportController@commonReportFilter');
Route::post('load/filter/common_sr_activity_filter/demo2', 'Report\CommonReportController@commonReportFilter');


Route::get('load/filter/common_sr_activity_filter/demo99', 'Report\CommonReportController@commonReportFilter99');
Route::post('load/filter/common_sr_activity_filter/demo99', 'Report\CommonReportController@commonReportFilter99');

Route::get('load/filter/common_sr_activity_filter/orderDetails', 'Report\CommonReportController@commonReportFilterOrderDetails');
Route::post('load/filter/common_sr_activity_filter/orderDetails', 'Report\CommonReportController@commonReportFilterOrderDetails');


Route::get('load/filter/common_sr_productivity/filter', 'Report\CommonReportController@commonReportFilter');
Route::post('load/filter/common_sr_productivity/filter', 'Report\CommonReportController@commonReportFilter');

Route::get('load/filter/common_sr_productivity/filter', 'Report\CommonReportController@commonReportFilter');
Route::post('load/filter/common_sr_productivity/filter', 'Report\CommonReportController@commonReportFilter');
Route::post('load/order_summary_report/common/filter', 'Report\CommonReportController@orderSummaryReportFilter');

Route::get('load/filter/classWiseReport/filter', 'Report\CommonReportController@classWiseReport');
Route::post('load/filter/classWiseReport/filter', 'Report\CommonReportController@classWiseReport');
Route::post('commonReportRequest', 'Report\CommonReportRequestController@commonReportRequestQueryGenerate');



Route::get('sr_summary_report', 'Report\SRSummaryController@orderSummaryReport');
Route::get('load/filter/sr_summary/demo2', 'Report\SRSummaryController@srActivitySummaryFilterdemo_1');
Route::Post('load/filter/sr_summary/demo2', 'Report\SRSummaryController@srActivitySummaryFilterdemo_1');
Route::get('sr_summary_report/map/{sr_id}/{date}', 'Report\SRSummaryController@mapShow');

Route::get('load/filter/sr_productivity/filter', 'Report\ActivityReportController@srProductivitySummaryFilter');
Route::post('load/filter/sr_productivity/filter', 'Report\ActivityReportController@srProductivitySummaryFilter');




Route::get('sr_activity', 'Report\ActivityReportController@srActivitySummary');
Route::Post('load/filter/sr_activity_filter', 'Report\ActivityReportController@srActivitySummaryFilter');
Route::get('load/filter/sr_activity_filter/demo', 'Report\ActivityReportController@srActivitySummaryFilterdemoo');
Route::Post('load/filter/sr_activity_filter/demo', 'Report\ActivityReportController@srActivitySummaryFilterdemo');
Route::get('load/filter/sr_activity_filter/demo2', 'Report\ActivityReportController@srActivitySummaryFilterdemo_1');
Route::Post('load/filter/sr_activity_filter/demo2', 'Report\ActivityReportController@srActivitySummaryFilterdemo_1');
Route::Post('load/filter/sr_activity_filter/demo3', 'Report\ActivityReportController@srActivitySummaryFilterdemo_2');
Route::get('load/filter/sr_activity_filter/demo3', 'Report\ActivityReportController@srActivitySummaryFilterdemo_2');

Route::get('sr_productivity', 'Report\ActivityReportController@srProductivitySummary');
Route::get('load/filter/sr_productivity/filter', 'Report\ActivityReportController@srProductivitySummaryFilter');
Route::post('load/filter/sr_productivity/filter', 'Report\ActivityReportController@srProductivitySummaryFilter');

Route::get('non_productive_report', 'Report\ActivityReportController@srNonProductivitySummary');
Route::get('load/filter/non_productive_sr/filter', 'Report\ActivityReportController@srNonProductivitySummaryFilter');
Route::post('load/filter/non_productive_sr/filter', 'Report\ActivityReportController@srNonProductivitySummaryFilter');


Route::get('item_order_summary', 'Report\ItemReportController@itemOrderSummary');
Route::get('load/filter/item_order_summary', 'Report\ItemReportController@itemOrderSummaryFilter');
Route::post('load/filter/item_order_summary', 'Report\ItemReportController@itemOrderSummaryFilter');

Route::get('unsold_item_summary', 'Report\ItemReportController@unsoldItemSummary');
Route::get('load/filter/unsold_item_summary', 'Report\ItemReportController@unsoldItemSummaryFilter');
Route::post('load/filter/unsold_item_summary', 'Report\ItemReportController@unsoldItemSummaryFilter');

Route::get('order_summary_srvclass', 'Report\ItemReportController@srClassWiseOrderSummary');
Route::get('load/filter/srvclassSummary', 'Report\ItemReportController@srClassWiseOrderSummaryFilter');
Route::post('load/filter/srvclassSummary', 'Report\ItemReportController@srClassWiseOrderSummaryFilter');

//Route::post('data_export/dataE', 'Report\ItemReportController@dataExportAttendanceData');
Route::post('data_export/dataE', 'Report\ItemReportController@dataExportAttendanceData2');
//Route::post('load/filter/srvclassorderSummary', 'Report\ItemReportController@srClassWiseOrderSummaryFilter');

//Route::post('load/filter/unsold_item_summary', 'Report\ItemReportController@unsoldItemSummaryFilter');

Route::get('i_map_view', 'Report\ActivityMapController@summary');


Route::get('order_report_vc_cooler', 'Report\ActivityReportController@orderReportVcCooler');
Route::get('load/filter/order_report_vc_cooler/filter', 'Report\ActivityReportController@orderReportVcCoolerFilter');
Route::post('load/filter/order_report_vc_cooler/filter', 'Report\ActivityReportController@orderReportVcCoolerFilter');


//==============================sr activity Data  End=========================


//==============================Route Search=========================

Route::get('get/employee/routeSearch/view', 'MasterData\EmployeeController@employeeRouteRearchView');
Route::get('json/get/employee/route', 'MasterData\EmployeeController@jsonGetEmployeeRoute');
Route::get('/json/delete/employee/route','MasterData\EmployeeController@jsonDeteteEmployeeRoute');


//==============================End Route Search=====================


//==============================Route Like=========================

Route::get('employee/get/routeLike/view','MasterData\EmployeeController@routeLikeView');
Route::get('/json/load/company_wise/group','MasterData\EmployeeController@jsonLoadCompanyGroup');
Route::get('/json/load/region_wise/zone','MasterData\EmployeeController@jsonLoadZoonName');
Route::get('/json/load/group_zoon_wise/user','MasterData\EmployeeController@jsonLoadGroupZoonWiseUser');
Route::get('/json/order_detail','MasterData\EmployeeController@jsonLoadGroup');

Route::post('/json/submit/routeLike','MasterData\EmployeeController@jsonSubmitRouteLike');

//==============================End=====================

Route::get('employee/employeeHrisUploadFormat', 'MasterData\EmployeeController@employeeHrisUploadFormatGen');
Route::get('employee/employeeHrisUpload', 'MasterData\EmployeeController@employeeHrisUpload');
Route::post('employee/employeeHrisUpload', 'MasterData\EmployeeController@employeeHrisUploadInsert');
Route::post('employee/employeehrisCreate', 'MasterData\EmployeeController@employeehrisCreate');


Route::resource('employee', 'MasterData\EmployeeController');
Route::post('employee/filter/empdetails', 'MasterData\EmployeeController@employeeFilter');
Route::get('employee/filter/empdetails', 'MasterData\EmployeeController@employeeFilter');

Route::get('temp_user', 'MasterData\TempEmployeeController@newEmployee');
Route::post('newemployee/filter/empdetails', 'MasterData\TempEmployeeController@newEmployeeFilterDetails');



Route::get('company/emp_delete/{id}', 'MasterData\CompanyController@empDelete');
Route::get('company/emp_add/{id}', 'MasterData\CompanyController@empAdd');
Route::get('company/employee/{id}', 'MasterData\CompanyController@empEdit');
Route::post('company/companyEmployeeMappingUpload', 'MasterData\CompanyController@companyEmployeeMappingUploadInsert');
Route::get('company/companyEmployeeMappingUploadFormat', 'MasterData\CompanyController@companyEmployeeMappingUploadFormatGen');
Route::resource('company', 'MasterData\CompanyController');


Route::get('companySiteMapping/uploadFormat', 'MasterData\CompanySiteMappingController@uploadFormat');
Route::post('companySiteMapping/filter', 'MasterData\CompanySiteMappingController@companySiteMappingFilter');
Route::post('companySiteMapping/uploadInsert', 'MasterData\CompanySiteMappingController@uploadInsert');
Route::resource('companySiteMapping', 'MasterData\CompanySiteMappingController');


Route::get('hr-policy/emp_delete/{id}', 'HR\HRPolicyController@empDelete');
Route::get('hr-policy/emp_add/{id}', 'HR\HRPolicyController@empAdd');
Route::get('hr-policy/employee/{id}', 'HR\HRPolicyController@empEdit');
Route::get('hr-policy/employeeUploadFormat/{id}', 'HR\HRPolicyController@hrEmployeeUploadFormatGen');
Route::post('hr-policy/employeeUpload', 'HR\HRPolicyController@hrPolicyEmpUploadInsert');
Route::resource('hr-policy', 'HR\HRPolicyController');

Route::post('division/filterDivision', 'GroupDataSetup\DivisionContm_amimtroller@filterDivision');
Route::resource('division', 'GroupDataSetup\DivisionController');

//==============================Market controller start=========================

Route::resource('market', 'GovtHeirarchy\MarketController');
Route::post('data_upload/marketMasterUpload', 'GovtHeirarchy\MarketController@marketMasterUploadInsert');
Route::get('data_upload/marketUploadFormat', 'GovtHeirarchy\MarketController@marketUploadFormatGen');

//==============================Market controller end=========================

//==============================Ward controller start=========================
Route::resource('ward', 'GovtHeirarchy\WardController');
Route::post('data_upload/wardMasterUpload', 'GovtHeirarchy\WardController@wardMasterUploadInsert');
Route::get('data_upload/wardUploadFormat', 'GovtHeirarchy\WardController@wardUploadFormatGen');

//==============================Ward controller end=========================

//==============================Thana controller start=========================
Route::resource('thana', 'GovtHeirarchy\ThanaController');

Route::post('data_upload/thanaMasterUpload', 'GovtHeirarchy\ThanaController@thanaMasterUploadInsert');
Route::get('data_upload/thanaUploadFormat', 'GovtHeirarchy\ThanaController@thanaUploadFormatGen');
//==============================Thana controller end=========================

//==============================district controller start=========================
Route::resource('district', 'GovtHeirarchy\DistrictController');

Route::post('data_upload/districtMasterUpload', 'GovtHeirarchy\DistrictController@districtMasterUploadInsert');
Route::get('data_upload/districtUploadFormat', 'GovtHeirarchy\DistrictController@districtUploadFormatGen');

//==============================district controller end=========================


/*region controller start*/

Route::post('region/filterRegion', 'GroupDataSetup\RegionController@filterRegion');
Route::resource('region', 'GroupDataSetup\RegionController');
//file upload
Route::get('regionMasterUpload', 'GroupDataSetup\RegionController@regionMasterUploadInsert');
Route::post('regionMasterUpload', 'GroupDataSetup\RegionController@regionMasterUploadInsert');
Route::get('regionUploadFormat', 'GroupDataSetup\RegionController@regionFormatGen');
Route::post('region/regionUploadFormat', 'GroupDataSetup\RegionController@regionFormatGen');

/*region controller finish */



/*zone controller start */

Route::post('zone/filterZone', 'GroupDataSetup\ZoneController@filterZone');
Route::resource('zone', 'GroupDataSetup\ZoneController');
//file upload
Route::get('data_upload/zoneMasterUpload', 'GroupDataUpload\ZoneController@zoneMasterUpload');
Route::post('data_upload/zoneMasterUpload', 'GroupDataSetup\ZoneController@zoneMasterUploadInsert');
Route::get('data_upload/zoneUploadFormat', 'GroupDataSetup\ZoneController@zoneUploadFormatGen');

/*zone controller end */

/*base controller start */
Route::post('base/filterBase', 'GroupDataSetup\BaseController@filterBase');
Route::resource('base', 'GroupDataSetup\BaseController');

Route::get('data_upload/baseSiteFormat', 'GroupDataUpload\BaseSiteMappingUploadController@baseSiteFormatGen');
Route::get('data_upload/baseSiteUpload', 'GroupDataUpload\BaseSiteMappingUploadController@baseSiteUpload');
Route::post('data_upload/baseSiteUpload', 'GroupDataUpload\BaseSiteMappingUploadController@baseSiteInsert');


Route::get('data_upload/baseMasterUpload', 'GroupDataSetup\BaseController@baseMasterUpload');
Route::post('data_upload/baseMasterUpload', 'GroupDataSetup\BaseController@baseMasterUploadInsert');
Route::get('data_upload/baseUploadFormat', 'GroupDataSetup\BaseController@baseUploadFormatGen');


/*base controller end */


Route::get('site/siteFormat', 'MasterData\SiteController@siteFormatGen');
Route::get('site/siteUpload', 'MasterData\SiteController@siteUpload');
Route::post('site/siteUpload', 'MasterData\SiteController@siteInsert');
Route::post('site/filterDistrict', 'MasterData\SiteController@filterDistrict');
Route::post('site/filterThana', 'MasterData\SiteController@filterThana');
Route::post('site/filterWard', 'MasterData\SiteController@filterWard');
Route::post('site/filterMarket', 'MasterData\SiteController@filterMarket');
Route::post('site/filterSubChannel', 'MasterData\SiteController@filterSubChannel');
Route::resource('site', 'MasterData\SiteController');

Route::get('depot/emp_delete/{id}', 'Depot\DepotController@empDelete');
Route::get('depot/emp_add/{id}', 'Depot\DepotController@empAdd');
Route::get('depot/employee/{id}', 'Depot\DepotController@empEdit');
Route::get('depot/stock/{id}', 'Depot\DepotController@depotStock');
Route::post('depot/depotEmployeeMappingUpload', 'Depot\DepotController@depotEmployeeMappingUploadInsert');
Route::get('depot/depotEmployeeMappingUploadFormat', 'Depot\DepotController@depotEmployeeMappingUploadFormatGen');

Route::get('ThanSR/empThanAdd', 'GovtHeirarchy\SRThanaMappingController@empThanAdd');
Route::post('ThanSR/mapping', 'GovtHeirarchy\SRThanaMappingController@empThanaMapping');
Route::post('ThanSR/depotEmployeeMappingUpload', 'GovtHeirarchy\SRThanaMappingController@depotEmployeeMappingUploadInsert');
Route::get('ThanSR/depotEmployeeMappingUploadFormat', 'GovtHeirarchy\SRThanaMappingController@depotEmployeeMappingUploadFormatGen');
Route::post('ThanSR/dataExportThanSRMappingInfotData', 'GovtHeirarchy\SRThanaMappingController@dataExportSRThanaMappingData');


Route::get('depot/depotFormat', 'Depot\DepotController@depotFormatGen');
Route::post('depot/depotUpload', 'Depot\DepotController@depotInsert');
Route::resource('depot', 'Depot\DepotController');
Route::post('/depot/filter/DepotDetails', 'Depot\DepotController@filterDepotDetails');
Route::get('/depot/filter/DepotDetails', 'Depot\DepotController@filterDepotDetails');
Route::get('/depot/create/dealer/login', 'Depot\DepotController@dealerLoginCreate');
Route::post('/depot/create/dealer/login', 'Depot\DepotController@dealerLoginCreate');


Route::get('/depot/create/dealer/login/test', 'Depot\DepotController@testSgsm');
Route::post('/depot/create/dealer/login/test', 'Depot\DepotController@testSgsm');
Route::get('/depot/create/dealer/login/test/hris', 'Depot\DepotController@testSgsmHrisUser');


Route::resource('divisions', 'GovtHeirarchy\GovtDivisionController');

Route::post('mrr/mrrVerify', 'Depot\MRRController@mrrVerify');
Route::post('mrr/mrrUpload', 'Depot\MRRController@mrrUpload');
Route::get('mrr/mrrUploadFormat', 'Depot\MRRController@mrrFormatGen');
Route::resource('mrr', 'Depot\MRRController');


Route::post('trip/unloadVerify/{id}', 'Depot\TripController@unloadVerify');
Route::get('trip/unloadProduct/{id}', 'Depot\TripController@unloadProduct');
Route::post('trip/loadVerify/{id}', 'Depot\TripController@loadVerify');
Route::get('trip/loadProduct/{id}', 'Depot\TripController@loadProduct');
Route::get('trip/product/{id}', 'Depot\TripController@product');
Route::get('trip/order/{id}', 'Depot\TripController@order');
Route::post('trip/orderEdit/{id}', 'Depot\TripController@orderEdit');
Route::get('trip/grv/{id}', 'Depot\TripController@grv');
Route::post('trip/grvAssign/{id}', 'Depot\TripController@grvAssign');
Route::post('trip/productAssign/{id}', 'Depot\TripController@productAssign');
Route::post('trip/tripClose/{id}', 'Depot\TripController@tripClose');
Route::post('trip/tripStatusChange/{id}/{trip_id}', 'Depot\TripController@tripStatusChange');
Route::post('trip/tripGStatusChange/{id}/{trip_id}', 'Depot\TripController@tripGStatusChange');
Route::resource('trip', 'Depot\TripController');
Route::post('trip/filterOrderDetails', 'Depot\TripController@filterOrderDetails');
Route::post('trip/filterGrvDetails', 'Depot\TripController@filterGrvDetails');


Route::post('mrr/mrrUpload', 'Depot\MRRController@mrrUpload');
Route::get('mrr/mrrUploadFormat', 'Depot\MRRController@mrrFormatGen');
Route::resource('mrr', 'Depot\MRRController');

Route::post('group_site/filterRegion', 'GroupDataSetup\GroupSiteController@filterRegion');
Route::post('group_site/filterZone', 'GroupDataSetup\GroupSiteController@filterZone');
Route::post('group_site/filterBase', 'GroupDataSetup\GroupSiteController@filterBase');
Route::post('group_site/filterSite', 'GroupDataSetup\GroupSiteController@filterSite');
Route::post('group_site/filterRoute', 'GroupDataSetup\GroupSiteController@filterRoute');
Route::get('group_site/{id}/{group_id}/edit', 'GroupDataSetup\GroupSiteController@edit');
Route::resource('group_site', 'GroupDataSetup\GroupSiteController');

Route::post('route/filterRoute', 'GroupDataSetup\RouteController@filterRoute');
Route::get('route/site_assign', 'GroupDataSetup\RouteController@siteAssign');
Route::get('route/site_add/{id}', 'GroupDataSetup\RouteController@siteAdd');
Route::get('route/route_site/{id}', 'GroupDataSetup\RouteController@showSite');
Route::get('route/route_site_delete/{id}', 'GroupDataSetup\RouteController@siteRouteDelete');
Route::get('route/routeSiteMappingUploadFormatGen/{id}', 'GroupDataSetup\RouteController@routeSiteMappingUploadFormatGen');
Route::post('route/routeSiteMappingUpload', 'GroupDataSetup\RouteController@routeSiteMappingUploadInsert');
Route::get('route/routeMasterUpload', 'GroupDataSetup\RouteController@routeMasterUpload');
Route::post('route/routeMasterUpload', 'GroupDataSetup\RouteController@routeMasterUploadInsert');
Route::get('route/routeUploadFormat', 'GroupDataSetup\RouteController@routeUploadFormatGen');

Route::post('route/filter/details', 'GroupDataSetup\RouteController@routeFilter');
Route::resource('route', 'GroupDataSetup\RouteController');


Route::get('pjp/empRouteFormat', 'GroupDataSetup\PJPController@empRouteFormat');
Route::post('pjp/empRouteFormatGen', 'GroupDataSetup\PJPController@empRouteFormatGen');
Route::get('pjp/empRouteUpload', 'GroupDataSetup\PJPController@empRouteUpload');
Route::post('pjp/empRouteUpload', 'GroupDataSetup\PJPController@empRouteInsert');
Route::get('pjp/route_add/{id}', 'GroupDataSetup\PJPController@routeAdd');
Route::get('pjp/route_site/{id}/{emp_id}', 'GroupDataSetup\PJPController@showSite');
Route::get('pjp/site_add/{id}', 'GroupDataSetup\PJPController@siteAdd');
Route::post('pjp/bulk_site_add/{id}', 'GroupDataSetup\PJPController@bulkSiteAdd');
Route::get('pjp/route_site_delete/{id}', 'GroupDataSetup\PJPController@siteRouteDelete');
Route::resource('pjp', 'GroupDataSetup\PJPController');


Route::post('menu/filterMenu', 'MasterData\MenuController@filterMenu');
Route::resource('menu', 'MasterData\MenuController');
//========================= product-group start ===============
Route::resource('product-group', 'MasterData\ProductGroupController');

//file upload

Route::post('dataUploads/productGroupUpload', 'MasterData\ProductGroupController@productGroupUploadInsert');
Route::match(['GET', 'POST'],'dataUploads/productGroupUploadFormat', 'MasterData\ProductGroupController@productGroupFormatGen');

//========================= product-group end ===============


//========================= product-class start ===============
Route::resource('product-class', 'MasterData\ProductClassController');

Route::post('dataUpload/productClassUpload', 'MasterData\ProductClassController@productClassUploadInsert');
Route::match(['GET', 'POST'],'dataUpload/productClassUploadFormat', 'MasterData\ProductClassController@productClassFormatGen');

//========================= product-class end ===============

Route::resource('display-program', 'GroupDataSetup\DisplayProgramController');
Route::get('display-program/condition/{id}', 'GroupDataSetup\DisplayProgramController@condition');
Route::get('display-program/empAssign/{id}', 'GroupDataSetup\DisplayProgramController@empAssign');
Route::get('display-program/siteAssign/{id}', 'GroupDataSetup\DisplayProgramController@siteAssign');
Route::get('display-program/siteAssignAdd/{id}', 'GroupDataSetup\DisplayProgramController@siteAssignAdd');
Route::get('display-program/siteInactive/{id}', 'GroupDataSetup\DisplayProgramController@siteInactive');
Route::get('display-program/empAssignAdd/{id}', 'GroupDataSetup\DisplayProgramController@empAssignAdd');
Route::get('display-program/emp_delete/{id}', 'GroupDataSetup\DisplayProgramController@empDelete');
Route::get('display-program/sku_delete/{id}', 'GroupDataSetup\DisplayProgramController@skuDelete');
Route::get('display-program/display_sku/{id}', 'GroupDataSetup\DisplayProgramController@displaySku');
Route::get('display-program/condition_sku/{id}', 'GroupDataSetup\DisplayProgramController@conditionSku');
Route::get('display-program/offer_sku/{id}', 'GroupDataSetup\DisplayProgramController@offerSku');

Route::get('department/emp_delete/{id}', 'MasterData\DepartmentController@empDelete');
Route::get('department/emp_add/{id}', 'MasterData\DepartmentController@empAdd');
Route::get('department/employee/{id}', 'MasterData\DepartmentController@empEdit');
Route::get('department/deptEmployeeMappingUploadFormat', 'MasterData\DepartmentController@deptEmployeeMappingUploadFormatGen');
Route::post('department/deptEmployeeMappingUpload', 'MasterData\DepartmentController@deptEmployeeMappingUploadInsert');
Route::resource('department', 'MasterData\DepartmentController');

Route::get('sales-group/category/{id}', 'MasterData\SalesGroupController@categoryEdit');
Route::get('sales-group/category_add/{id}', 'MasterData\SalesGroupController@categoryAdd');
Route::get('sales-group/category_delete/{id}', 'MasterData\SalesGroupController@categoryDelete');
Route::get('sales-group/sku/{id}', 'MasterData\SalesGroupController@skuEdit');
Route::get('sales-group/employee/{id}', 'MasterData\SalesGroupController@empEdit');
Route::get('sales-group/sku_add/{id}', 'MasterData\SalesGroupController@skuAdd');
Route::get('sales-group/emp_add/{id}', 'MasterData\SalesGroupController@empAdd');
Route::get('sales-group/emp_delete/{id}', 'MasterData\SalesGroupController@empDelete');
Route::get('sales-group/sku_delete/{id}', 'MasterData\SalesGroupController@skuDelete');
Route::get('sales-group/groupSKUMappingUploadFormatGen', 'MasterData\SalesGroupController@groupSKUMappingUploadFormatGen');
Route::post('sales-group/groupSkuMappingUpload', 'MasterData\SalesGroupController@groupSkuMappingUploadInsert');
Route::get('sales-group/groupEmpMappingUploadFormatGen', 'MasterData\SalesGroupController@groupEmployeeMappingUploadFormatGen');
Route::post('sales-group/groupEmpMappingUpload', 'MasterData\SalesGroupController@groupEmployeeMappingUploadInsert');
Route::resource('sales-group', 'MasterData\SalesGroupController');
Route::post('sales-group/filter/Details', 'MasterData\SalesGroupController@filterDetails');
Route::get('sales-group/filter/Details', 'MasterData\SalesGroupController@filterDetails');

Route::get('company-data/employee/{id}', 'CompanyDataSetup\CompanyDataController@empEdit');
Route::get('company-data/emp_add/{id}', 'CompanyDataSetup\CompanyDataController@empAdd');
Route::get('company-data/emp_delete/{id}', 'CompanyDataSetup\CompanyDataController@empDelete');
Route::post('company-data/companyEmployeeMappingUpload', 'CompanyDataSetup\CompanyDataController@companyEmployeeMappingUploadInsert');
Route::get('company-data/companyEmployeeMappingUploadFormat', 'CompanyDataSetup\CompanyDataController@companyEmployeeMappingUploadFormatGen');

Route::get('company-data/site/{id}', 'CompanyDataSetup\CompanyDataController@siteEdit');
Route::get('company-data/site_add/{id}', 'CompanyDataSetup\CompanyDataController@siteAdd');
Route::get('company-data/site_delete/{id}', 'CompanyDataSetup\CompanyDataController@siteDelete');
Route::post('company-data/companySiteMappingUpload', 'CompanyDataSetup\CompanyDataController@companySiteMappingUploadInsert');
Route::get('company-data/companySiteMappingUploadFormat', 'CompanyDataSetup\CompanyDataController@companySiteMappingUploadFormatGen');

Route::resource('company-data', 'CompanyDataSetup\CompanyDataController');

Route::get('price_list/sku_delete/{id}', 'MasterData\PriceListController@skuDelete');
Route::post('price_list/sku_add/{id}', 'MasterData\PriceListController@skuAdd');
Route::get('price_list/sku/{id}', 'MasterData\PriceListController@skuEdit');
Route::get('price_list/sku_item/{id}/{price_id}', 'MasterData\PriceListController@priceListItemEdit');
Route::post('price_list/sku_item_edit/{id}', 'MasterData\PriceListController@priceListItemEditSubmit');
Route::get('price_list/skuUploadFormat/{id}', 'MasterData\PriceListController@skuUploadFormatGen');
Route::post('price_list/skuUpload', 'MasterData\PriceListController@skuUploadInsert');
Route::resource('price_list', 'MasterData\PriceListController');

Route::get('company_price_list/sku_delete/{id}', 'CompanyDataSetup\CompanyPriceListController@skuDelete');
Route::get('company_price_list/sku_add/{id}', 'CompanyDataSetup\CompanyPriceListController@skuAdd');
Route::get('company_price_list/sku/{id}', 'CompanyDataSetup\CompanyPriceListController@skuEdit');
Route::get('company_price_list/skuUploadFormat/{id}', 'CompanyDataSetup\CompanyPriceListController@skuUploadFormatGen');
Route::post('company_price_list/skuUpload', 'CompanyDataSetup\CompanyPriceListController@skuUploadInsert');
Route::resource('company_price_list', 'CompanyDataSetup\CompanyPriceListController');

Route::get('sales-group-data/sku/{id}', 'GroupDataSetup\SalesGroupDataController@skuEdit');
Route::get('sales-group-data/employee/{id}', 'GroupDataSetup\SalesGroupDataController@empEdit');
Route::get('sales-group-data/sku_add/{id}', 'GroupDataSetup\SalesGroupDataController@skuAdd');
Route::get('sales-group-data/emp_add/{id}', 'GroupDataSetup\SalesGroupDataController@empAdd');
Route::get('sales-group-data/emp_delete/{id}', 'GroupDataSetup\SalesGroupDataController@empDelete');
Route::get('sales-group-data/sku_delete/{id}', 'GroupDataSetup\SalesGroupDataController@skuDelete');
Route::resource('sales-group-data', 'GroupDataSetup\SalesGroupDataController');


Route::resource('holiday_type', 'HR\HolidayTypeController');

Route::get('holiday/policy_delete/{id}', 'HR\HolidayController@policyDelete');
Route::get('holiday/policy_add/{id}', 'HR\HolidayController@policyAdd');
Route::get('holiday/policy/{id}', 'HR\HolidayController@policyMapping');
Route::get('holiday/policyUploadFormat/{id}', 'HR\HolidayController@policyUploadFormatGen');
Route::post('holiday/policyUpload', 'HR\HolidayController@policyUploadInsert');
Route::resource('holiday', 'HR\HolidayController');

Route::resource('leave', 'HR\LeaveController');
Route::resource('iom', 'HR\IOMController');

Route::resource('attendance_type', 'MasterData\AttendanceTypeController');

Route::resource('post', 'Blog\BlogController');
Route::resource('training', 'Blog\TrainingController');
Route::post('sku/filterSKU', 'MasterData\SKUController@filterSKU');

//=====================sku Controller start =================
Route::resource('sku', 'MasterData\SKUController');
Route::post('new-sku-list', 'MasterData\SKUControllerNew@newSku');
Route::get('new-sku-list', 'MasterData\SKUControllerNew@newSku');


//=====================sku Controller end  =================

//=====================Market Open Controller=================
Route::post('group_sku/filterSKU', 'GroupDataSetup\GroupSKUController@filterSKU');
Route::resource('group_sku', 'GroupDataSetup\GroupSKUController');

Route::post('new_sku', 'MasterData\SKUControllerNew@newSku');

Route::resource('display', 'GroupDataSetup\DisplayProgramController');

Route::get('data_upload/siteFormat', 'MasterDataUpload\SiteUploadController@siteFormatGen');
Route::get('data_upload/siteUpload', 'MasterDataUpload\SiteUploadController@siteUpload');
Route::post('data_upload/siteUpload', 'MasterDataUpload\SiteUploadController@siteInsert');

Route::get('data_upload/routeSiteFormat', 'GroupDataUpload\RouteSiteUploadController@routeSiteFormat');
Route::post('data_upload/routeSiteFormat', 'GroupDataUpload\RouteSiteUploadController@routeSiteFormatGen');
Route::get('data_upload/routeSiteUpload', 'GroupDataUpload\RouteSiteUploadController@routeSiteUpload');
Route::post('data_upload/routeSiteUpload', 'GroupDataUpload\RouteSiteUploadController@routeSiteInsert');



Route::get('data_upload/groupEmployeeMappingUpload', 'GroupDataUpload\GroupEmployeeMappingUploadController@groupEmployeeMappingUpload');
Route::post('data_upload/groupEmployeeMappingUpload', 'GroupDataUpload\GroupEmployeeMappingUploadController@groupEmployeeMappingUploadInsert');
Route::get('data_upload/groupEmployeeMappingUploadFormat', 'GroupDataUpload\GroupEmployeeMappingUploadController@groupEmployeeMappingUploadFormatGen');

Route::get('data_export/dataExport', 'DataExport\DataExportController@dataExport');
Route::post('data_export/dataExportGroupData', 'DataExport\DataExportController@dataExportGroupData');
Route::post('data_export/dataExportEmpData', 'DataExport\DataExportController@dataExportEmpData');
Route::post('data_export/dataExportDashData', 'DataExport\DataExportController@dataExportDashData');
Route::post('data_export/dataExportOrderData', 'DataExport\DataExportController@dataExportOrderData');
//Route::post('data_export/dataExportProductivityData', 'DataExport\DataExportController@dataExportProductivityData');
Route::post('data_export/dataExportAttendanceData', 'DataExport\DataExportController@dataExportAttendanceData');
Route::post('data_export/dataExportNonProductiveSRListData', 'DataExport\DataExportController@dataExportNonProductiveSRListData');
Route::post('data_export/dataExportOutletSummaryReportData', 'DataExport\DataExportController@dataExportOutletSummaryReportData');
Route::post('data_export/dataExportOutletDetailsReportData', 'DataExport\DataExportController@dataExportOutletDetailsReportData');
Route::post('data_export/dataExportUserInfoReportData', 'DataExport\DataExportController@dataExportUserInfoReportData');
Route::post('data_export/dataExportGroupZoneWiseOrderSummaryData', 'DataExport\DataExportController@dataExportGroupZoneWiseOrderSummaryData');
Route::post('data_export/newlyOpenedOutlet', 'DataExport\DataExportController@dataExportNewlyOpendedOutlet');
//Route::get('data_upload/companyEmployeeMappingUpload', 'CompanyDataUpload\CompanyEmployeeMappingUploadController@companyEmployeeMappingUpload');


Route::get('data_upload/groupSkuMappingUpload', 'GroupDataUpload\GroupSKUMappingUploadController@groupSkuMappingUpload');
Route::post('data_upload/groupSkuMappingUpload', 'GroupDataUpload\GroupSKUMappingUploadController@groupSkuMappingUploadInsert');
Route::get('data_upload/groupSKUMappingUploadFormat', 'GroupDataUpload\GroupSKUMappingUploadController@groupSKUMappingUploadFormatGen');

Route::get('data_upload/empRouteFormat', 'GroupDataUpload\RoutePlanUploadController@empRouteFormat');
Route::post('data_upload/empRouteFormat', 'GroupDataUpload\RoutePlanUploadController@empRouteFormatGen');
Route::get('data_upload/empRouteUpload', 'GroupDataUpload\RoutePlanUploadController@empRouteUpload');
Route::post('data_upload/empRouteUpload', 'GroupDataUpload\RoutePlanUploadController@empRouteInsert');


Route::get('data_upload/routeMasterUpload', 'GroupDataUpload\RouteMasterUploadController@routeMasterUpload');
Route::post('data_upload/routeMasterUpload', 'GroupDataUpload\RouteMasterUploadController@routeMasterUploadInsert');
Route::get('data_upload/routeUploadFormat', 'GroupDataUpload\RouteMasterUploadController@routeUploadFormatGen');

Route::get('data_upload/skuMasterUpload', 'MasterDataUpload\SKUMasterUploadController@skuMasterUpload');
Route::post('data_upload/skuMasterUpload', 'MasterDataUpload\SKUMasterUploadController@skuMasterUploadInsert');
Route::get('data_upload/skuUploadFormat', 'MasterDataUpload\SKUMasterUploadController@skuUploadFormatGen');




Route::get('data_upload/regionMasterUpload', 'GroupDataUpload\RegionMasterUploadController@regionMasterUpload');
Route::post('data_upload/regionMasterUpload', 'GroupDataUpload\RegionMasterUploadController@regionMasterUploadInsert');
Route::get('data_upload/regionUploadFormat', 'GroupDataUpload\RegionMasterUploadController@regionUploadFormatGen');

Route::post('mobile_menu/filterMenu', 'MasterData\MobileMenuController@filterMenu');
Route::resource('mobile_menu', 'MasterData\MobileMenuController');

Route::get('attendance/summary', 'Report\AttendanceReportController@summary');
Route::post('attendance/filterAttendanceSymmary', 'Report\AttendanceReportController@filterAttendanceSymmary');
Route::get('attendance/summaryDetails/{id}/{date}', 'Report\AttendanceReportController@summaryDetails');
Route::get('attendance/summaryLocation/{id}/{date}', 'Report\AttendanceReportController@summaryLocation');
Route::get('attendance/summaryLocationAll/{id}/{date}', 'Report\AttendanceReportController@summaryLocAttenNote');

Route::get('attendance/testReport', 'Report\TestReport@summary');

Route::get('mistiri/report', 'Report\MistiriReportController@summary');
Route::post('mistiri/dataExport', 'Report\MistiriReportController@dataExportMistiriData');
/*Route::post('mistiri/Approved', 'Report\MistiriReportController@Approved');
Route::post('mistiri/show', 'Report\MistiriReportController@show');
Route::post('mistiri/edit', 'Report\MistiriReportController@edit');*/
Route::resource('mistiri', 'Report\MistiriReportController');


Route::get('note/summary', 'Report\NoteReportController@summary');
Route::POST('note/filterNoteSummary', 'Report\NoteReportController@filterNoteSummary');
Route::post('load/filter/note_details/filter', 'Report\NoteReportController@filterNoteDetails');
Route::get('load/filter/note_details/filter', 'Report\NoteReportController@filterNoteDetails');
Route::get('note/summaryDetails/{id}/{date}', 'Report\NoteReportController@summaryDetails');
Route::get('note/summaryLocation/{id}/{date}', 'Report\NoteReportController@summaryLocation');

Route::get('location/summary', 'Report\LocationReportController@summary');
Route::post('location/filterLocationSummary', 'Report\LocationReportController@filterLocationSummary');
Route::get('location/summaryLocation/{id}/{date}', 'Report\LocationReportController@summaryLocation');


Route::get('current_location/current_position', 'Report\CurrentLocationController@current_map');

Route::get('activity/summary', 'Report\ActivityReportController@summary');
Route::post('activity/filterActivitySummary', 'Report\ActivityReportController@filterActivitySymmary');


Route::get('block/maintainBlock', 'BlockOrder\BlockOrderController@maintainBlock');
Route::post('block/filterMaintainBlock', 'BlockOrder\BlockOrderController@filterMaintainBlock');
Route::get('block/specialRelease/{id}', 'BlockOrder\BlockOrderController@specialRelease');
Route::post('block/specialReleaseAction/{id}', 'BlockOrder\BlockOrderController@specialReleaseAction');
Route::get('block/overDueRelease/{id}', 'BlockOrder\BlockOrderController@overDueRelease');
Route::post('block/overDueReleaseAction/{id}', 'BlockOrder\BlockOrderController@overDueReleaseAction');
Route::get('block/creditRelease/{id}', 'BlockOrder\BlockOrderController@creditRelease');
Route::post('block/creditReleaseAction/{id}', 'BlockOrder\BlockOrderController@creditReleaseAction');

Route::resource('specialBudget', 'BlockOrder\SpecialBudgetController');


Route::get('target/upload', 'Target\TargetUploadController@upload');
Route::get('target/uploadFormat', 'Target\TargetUploadController@uploadFormat');
Route::post('target/uploadFormatGen', 'Target\TargetUploadController@uploadFormatGen');
Route::post('target/uploadInsert', 'Target\TargetUploadController@uploadInsert');
Route::get('target', 'Target\TargetUploadController@target');
Route::post('filterTarget', 'Target\TargetUploadController@filterTarget');
Route::get('target/bySalesMan/{supervisor_id}/{year}/{month_id}', 'Target\TargetUploadController@bySalesMan');
Route::get('target/byItem/{supervisor_id}/{year}/{month_id}', 'Target\TargetUploadController@byItem');
Route::get('target/byCategory/{supervisor_id}/{year}/{month_id}', 'Target\TargetUploadController@byCategory');


Route::get('collection/maintainCollection', 'Collection\CollectionController@maintainCollection');
Route::post('collection/filterCollection', 'Collection\CollectionController@filterCollection');
Route::get('collection/maintainView/{id}', 'Collection\CollectionController@viewCollection');
Route::get('collection/verify/{id}', 'Collection\CollectionController@verify');
Route::post('collection/verifyUpdate/{id}', 'Collection\CollectionController@verifyUpdate');
Route::get('collection/chequeVerify/{id}', 'Collection\CollectionController@chequeVerify');
Route::post('collection/chequeVerifyUpdate/{id}', 'Collection\CollectionController@chequeVerifyUpdate');


Route::get('order_report/orderSummary', 'Depot\OrderReportController@orderSummary');
Route::post('order_report/filterOrderSummary', 'Depot\OrderReportController@filterOrderSummary');
Route::post('order_report/pushToRoutePlan', 'Depot\OrderReportController@pushToRoutePlan');

Route::get('grv_report/grvSummary', 'Depot\GRVReportController@grvSummary');
Route::post('grv_report/filterGRVSummary', 'Depot\GRVReportController@filterGRVSummary');
Route::post('grv_report/pushToRoutePlan', 'Depot\GRVReportController@pushToRoutePlan');

Route::get('activity/mySummary', 'Report\MyActivityReportController@mySummary');
Route::post('activity/filterMyActivitySummary', 'Report\MyActivityReportController@filterMyActivitySummary');
Route::get('activity/mySummaryBy/{id}/{date}', 'Report\MyActivityReportController@mySummaryBy');
Route::get('activity/attendanceSummaryDetails/{id}/{date}', 'Report\MyActivityReportController@attendanceSummaryDetails');
Route::get('activity/noteSummaryDetails/{id}/{date}', 'Report\MyActivityReportController@noteSummaryDetails');
Route::get('activity/summaryLocationAll/{id}/{date}', 'Report\MyActivityReportController@summaryLocAttenNote');


Route::get('activity_status/summary', 'Report\StatusReportController@summary');
Route::get('activity_status/activeSummaryDetails/{id}/{start_date}/{end_date}', 'Report\StatusReportController@activeSummaryDetails');
Route::get('activity_status/inactiveSummaryDetails/{id}/{start_date}/{end_date}', 'Report\StatusReportController@inactiveSummaryDetails');
Route::post('activity_status/filterActivityStatusSymmary', 'Report\StatusReportController@filterActivityStatusSymmary');

Route::get('group_status/summary', 'Report\GroupStatusController@summary');
Route::get('group_status/activeSummaryDetails/{id}/{start_date}/{end_date}', 'Report\GroupStatusController@activeSummaryDetails');
Route::get('group_status/inactiveSummaryDetails/{id}/{start_date}/{end_date}', 'Report\GroupStatusController@inactiveSummaryDetails');
Route::post('group_status/filterActivityStatusSymmary', 'Report\GroupStatusController@filterActivityStatusSymmary');

Route::resource('geoCode', 'MasterData\GeoCodeController');


Route::get('self_account/employee/{id}', 'SelfAccount\SelfAccountController@empEdit');
Route::get('self_account/emp_add/{id}', 'SelfAccount\SelfAccountController@empAdd');
Route::get('self_account/emp_delete/{id}', 'SelfAccount\SelfAccountController@empDelete');

Route::get('self_account/cash_move_type/{id}', 'SelfAccount\SelfAccountController@cashMoveType');
Route::get('self_account/cash_move_type_add/{id}', 'SelfAccount\SelfAccountController@cashMoveTypeAdd');
Route::get('self_account/cash_move_type_delete/{id}', 'SelfAccount\SelfAccountController@cashMoveTypeDelete');

Route::get('self_account/cash_source/{id}', 'SelfAccount\SelfAccountController@cashSource');
Route::get('self_account/cash_source_add/{id}', 'SelfAccount\SelfAccountController@cashSourceAdd');
Route::get('self_account/cash_source_delete/{id}', 'SelfAccount\SelfAccountController@cashSourceDelete');

Route::get('self_account/cash_receive/{id}', 'SelfAccount\SelfAccountController@cashReceive');
Route::get('self_account/cash_receive_add/{id}', 'SelfAccount\SelfAccountController@cashReceiveAdd');
Route::get('self_account/cash_receive_delete/{id}', 'SelfAccount\SelfAccountController@cashReceiveDelete');

Route::get('self_account/cash_out/{id}', 'SelfAccount\SelfAccountController@cashOut');
Route::get('self_account/cash_out_add/{id}', 'SelfAccount\SelfAccountController@cashOutAdd');
Route::get('self_account/cash_out_delete/{id}', 'SelfAccount\SelfAccountController@cashOutDelete');

Route::get('self_account/cash_move/{id}', 'SelfAccount\SelfAccountController@cashMove');
Route::get('self_account/cash_move_add/{id}', 'SelfAccount\SelfAccountController@cashMoveAdd');
Route::get('self_account/cash_move_delete/{id}', 'SelfAccount\SelfAccountController@cashMoveDelete');

Route::get('self_account/cash_history/{id}', 'SelfAccount\SelfAccountController@cashHistory');

Route::resource('self_account', 'SelfAccount\SelfAccountController');

Route::resource('setting_email', 'Setting\ManualEmailController');
Route::post('setting_employee_attendance/process', 'Setting\AttendanceProcessController@attendanceProcess');
Route::resource('setting_employee_attendance', 'Setting\AttendanceProcessController');

Route::get('appMenuGroup/uploadFormat/{id}', 'Setting\AppMenuGroupController@uploadFormat');
Route::post('appMenuGroup/assignToUser/{id}', 'Setting\AppMenuGroupController@assignToUser');
Route::post('appMenuGroup/assignMenu/{id}', 'Setting\AppMenuGroupController@assignMenu');
Route::post('appMenuGroup/menuDelete/{id}', 'Setting\AppMenuGroupController@menuDelete');
Route::resource('appMenuGroup', 'Setting\AppMenuGroupController');
Route::get('location/maintainLocation', 'Location\LocationController@maintainLocation');

Route::post('location/filterMaintainLocation', 'Location\LocationController@filterMaintainLocation');

//================Location Master Controller start=============
Route::resource('location_master', 'Location\LocationMasterController');
Route::post('location_master/file/insert', 'Location\LocationMasterController@locationMasterFileUpload');
Route::get('location_master/format/download', 'Location\LocationMasterController@locationMasterFormatGen');
//================Location Master Controller end=============

//================Location Company Controller start=============
Route::resource('location_company', 'Location\LocationCompanyController');
Route::post('location_company/file/insert', 'Location\LocationCompanyController@locationCompanyFileUpload');
Route::get('location_company/format/download', 'Location\LocationCompanyController@locationCompanyFormatGen');
//================Location Company Controller end=============


//================Location Department Controller start=============
Route::resource('location_department', 'Location\LocationDepartmentController');
Route::post('location_department/file/insert', 'Location\LocationDepartmentController@locationDepartmentFileUpload');
Route::get('location_department/format/download', 'Location\LocationDepartmentController@locationDepartmentFormatGen');
//================Location Department Controller end=============

//================Location Section Controller start=============

Route::get('location_section/format', 'Location\LocationSectionController@format');
Route::post('location_section/upload', 'Location\LocationSectionController@upload');
Route::resource('location_section', 'Location\LocationSectionController');

//================Location Section Controller end=============


Route::resource('location_details', 'Location\LocationDetailsController');
Route::resource('location_note', 'Location\NoteDetailsController');
Route::POST('/get/location/note/filter','Location\NoteDetailsController@filterNoteDetails');
Route::post('/location/filterCompany','Location\NoteDetailsController@filterCompany');

//=====================Market Open Controller=================

Route::resource('market_open','MarketOpen\MarketOpenController');
Route::get('markert_list','MarketOpen\MarketOpenController@loadMarketList');

//=====================End==========================================

//=====================Market Report Controller=================

Route::get('market_report','MarketOpen\MarketReportController@index');
Route::get('/json/get/allemployeelist','MarketOpen\MarketReportController@jsonGetAllEmployeeList');
Route::get('jsonGetEmployeeList','MarketOpen\MarketReportController@jsonGetEmployeeList');
Route::get('/get/market/report','MarketOpen\MarketReportController@getMarketReport');
Route::get('/ward/market/details/{id}','MarketOpen\MarketReportController@wardMarketDetails');

//=====================End=================================

//=====================Market Open Controller=================

Route::get('/json/get/market_open/thana_list','MarketOpen\MarketOpenController@jsonGetThanaList');
Route::get('/json/get/market_open/word_list','MarketOpen\MarketOpenController@jsonGetMarketWordList');
Route::get('/json/get/ward_wise/market_list','MarketOpen\MarketOpenController@jsonGetWardWiseMarketList');
Route::get('/json/save/market_details','MarketOpen\MarketOpenController@jsonSaveMarketDetails');
Route::get('/json/edit_market','MarketOpen\MarketOpenController@jsonEditMarket');
Route::get('/json/edit/market_details','MarketOpen\MarketOpenController@jsonEditMarketDetails');
Route::get('/map_market_report','MarketOpen\MarketOpenController@mapMarkeReportShow');
Route::get('/json/load/market','MarketOpen\MarketOpenController@jsonLoadMarketGoogleMap');
Route::get('/json/get/sr_wise/thana/list/{id}','MarketOpen\MarketOpenController@srWiseThanaList');
Route::get('/json/delete/sr_wise/thana/list/{id}','MarketOpen\MarketOpenController@deleteSrWiseThanaList');

//=========================End Controller=============


//=====================Market Open Controller=================
Route::get('/vc_cooler_outlet_list','Report\VCCoolerReportController@mapMarkeReportShow');
Route::get('/json/load/vcCoolerOutletList','Report\VCCoolerReportController@jsonLoadVoutletGoogleMap');
Route::post('/json/load/vcCoolerOutletList','Report\VCCoolerReportController@jsonLoadVoutletGoogleMap');
//=========================End Controller=============



//=====================Json get Controller=================
Route::post('promotion/getItemCategory', 'Promotion\PromotionController@getItemCategory');
Route::post('promotion/getCategoryItem', 'Promotion\PromotionController@getCategoryItem');
Route::post('load/report/getGroup', 'Report\ActivityReportController@getGroup');
Route::get('load/report/getGroup', 'Report\ActivityReportController@getGroup');
Route::post('load/report/getZone', 'Report\ActivityReportController@getZone');
Route::post('load/report/getSR', 'Report\ActivityReportController@getSR');
Route::post('load/report/getSR', 'Report\ActivityReportController@getSR');
Route::post('load/report/getOutlet', 'Report\ActivityReportController@getOutlet');
Route::post('load/report/getItemClass', 'Report\ActivityReportController@getItemClass');
Route::post('load/report/getItem', 'Report\ActivityReportController@getItem');
//Route::post('load/report/getItemDetails', 'Report\ActivityReportController@getItem');
Route::get('load/report/getItemDetails', 'Report\ActivityReportController@getItem');
//Route::get('/json/load/company/group_name','Report\ActivityReportController@getGroup');

Route::get('/spmp','Promotion\PromotionController@getSpmiView');
Route::get('/spmp/create','Promotion\PromotionController@getSpmiCreate');
Route::post('/spmp/store/details','Promotion\PromotionController@spmpStoreDetails');

//=====================Json get Controller end=================


//=====================Campaign Controller start =================
Route::resource('campaign', 'Campaign\CampaignController');
//=====================Campaign Open Controller end =================


//===================== Distributor panel start =================
Route::resource('dis_login', 'Distributor\DistributorController');
Route::get('distributor/auth/check', 'Distributor\DistributorController@authenticationCheck');
Route::post('distributor/auth/check', 'Distributor\DistributorController@authenticationCheck');

Route::get('dis_order_list', 'Distributor\DistributorController@distributorOrderList');

Route::Post('load/filter/dis_order_list', 'Distributor\DistributorController@distributorOrderListFilter');

Route::get('distributor_item_summary', 'Distributor\DistributorController@distributorOrderItemSummary');
Route::Post('load/filter/distributorOrderItemSummary', 'Distributor\DistributorController@distributorOrderItemSummaryFilter');
Route::get('load/filter/distributorOrderItemSummary', 'Distributor\DistributorController@distributorOrderItemSummaryFilter');
//===================== Distributor panel end =================
//===================== Delivery start =================
Route::get('sr_wise_delivery', 'Delivery\DeliveryController@srWiseDelivery');
Route::Post('load/report/filter/srWiseDelivery', 'Delivery\DeliveryController@srWiseDeliveryFilterReport');
Route::get('load/report/filter/srWiseDelivery', 'Delivery\DeliveryController@srWiseDeliveryFilterReport');

//===================== Delivery end =================
//**************************Leader board start**************************
Route::get('national_leaders','LeaderBoard\LeaderBoardController@index');
Route::get('/getSecondarySalesNationalLeaders/{id}','LeaderBoard\LeaderBoardController@index');
Route::get('zonal_leaders','LeaderBoard\LeaderBoardController@filterLeaderBoard');
Route::get('progressive_leader_board','LeaderBoard\LeaderBoardController@progressiveLeaderBoard');
Route::get('progressive_leader_board/secondary','LeaderBoard\LeaderBoardController@progressiveLeaderBoardSecondary');
Route::post('load/leader_board/getGroup','LeaderBoard\LeaderBoardController@getGroup');
Route::post('filter/getLeaders','LeaderBoard\LeaderBoardController@getLeaders');
Route::get('find_yourself','LeaderBoard\LeaderBoardController@filterOwnUnitLeaderBoard');
Route::post('filter/ownUnit/getLeaders','LeaderBoard\LeaderBoardController@getOwnUnitLeaders');
//Route::get('/new/leader','LeaderBoard\LeaderBoardController@newDesign');
//**************************Leader board End**************************
//**************************Target vs Achievement Report Start**************************
Route::get('tgt_vs_achv_summary','Report\TargetVsAchvSummaryController@index');
Route::post('get/tgt_vs_achv/report','Report\TargetVsAchvSummaryController@getTgtVsAchvReport')->name('tgt.achv');

//************************Employee New************************************************
//Route::resource('employee_new', 'MasterData\NewEmployeeController');
Route::POST('/load/employeeUsnm','MasterData\EmployeeController@getEmployeeUsnm');
Route::get('/load/employeeData/{id}','MasterData\EmployeeController@editEmployeeOnChangeDropdown');
Route::get('employee_new', 'MasterData\EmployeeController@getNewEditIndexPage');
Route::post('employee_new', 'MasterData\EmployeeController@getNewEditIndexPage');


Route::post('/add/empCompany', 'MasterData\EmployeeController@addEmpCompany');
Route::post('delete/empCompany', 'MasterData\EmployeeController@deleteEmpCompany');
Route::post('/add/empSlgp', 'MasterData\EmployeeController@addEmpSlgp');
Route::post('delete/empSlgp', 'MasterData\EmployeeController@deleteEmpSlgp');
Route::get('get/empSlgpPriceList/{id}', 'MasterData\EmployeeController@getEmpSlgpPriceList');
Route::post('updateEmpInfo/{id}', 'MasterData\EmployeeController@updateEmpInfo');
Route::post('add/empDlrm/{id}', 'MasterData\EmployeeController@addEmpDlrm');
Route::get('delete/empDlr/{id}', 'MasterData\EmployeeController@deleteEmpDlrm');
Route::post('/add/empRoutePlan/{id}', 'MasterData\EmployeeController@addEmpRoutePlan');
Route::get('delete/empRoutePlan/{id}/{eid}', 'MasterData\EmployeeController@deleteEmpRoutePlan');
Route::post('/add/empZoneGroupMapping/{id}', 'MasterData\EmployeeController@addEmpZoneGroupMapping');
Route::get('/delete/zoneGroupMapping/{id}/{eid}', 'MasterData\EmployeeController@deleteEmpZoneGroupMapping');
Route::get('empActvInactv/{id}', 'MasterData\EmployeeController@empActvInactv');
Route::get('empPassReset/{id}', 'MasterData\EmployeeController@empPassReset');
//************************Employee New END************************************************



//************************Push Notification start ************************************************
Route::match(['get', 'post'],'/send-notification', 'Report\NotificationController@notificationList');
Route::match(['get', 'post'],'/create-notification', 'Report\NotificationController@notificationListCreate');
Route::get('/json/get/market_open/thana_list','MarketOpen\MarketOpenController@jsonGetThanaList');
Route::Post('/json/get/market_open/thana_list','MarketOpen\MarketOpenController@jsonGetThanaList');
Route::match(['get', 'post'],'/notification-send', 'Report\NotificationController@notificationSend');
//************************Push Notification end ************************************************

Route::get('getUserAPIVersion','Report\CommonReportController@getUserAPIVersion');
//send Email
//Route::get('sendMail','Report\CommonReportRequestController@sendMailWithReportLink');

Route::get('/logout', function () {
    Auth::logout();
    return Redirect::to('login');
});
Route::get('/clear', function () {

    \Artisan::call('clear-compiled');
    \Artisan::call('optimize:clear');
    // \Artisan::call('optimize');
    \Artisan::call('cache:clear');
    \Artisan::call('view:clear');
    \Artisan::call('route:clear');
    \Artisan::call('config:cache');
    $hostname = php_uname('n');
    dd("Cache is cleared", $hostname);
});

Route::get('/schema', function () {
    $data = new \App\MasterData\MasterDataTable();
    $data->up('6');
    $hostname = php_uname('n');
    dd("Database Schema Created Successfully", $hostname);
});

Route::get('/setup', function () {
    $data = new \DatabaseSeeder();
    $data->run('5');
    $hostname = php_uname('n');
    dd("Database Data Setup Successfully", $hostname);
});
Route::get('/down', function () {
    \Artisan::call('down --allow=103.206.184.113');
    $hostname = php_uname('n');
    dd("Server Down Now", $hostname);
});

Route::get('/up', function () {
    \Artisan::call('up');
    $hostname = php_uname('n');
    dd("Server Up Now", $hostname);
});


Route::get('faq', function () {
    return view('faq.index');
});

Route::get('faq/page', function () {
    return view('faq.page');
});

Route::get('/what-is-my-ip', function () {
    return request()->ip();
});
