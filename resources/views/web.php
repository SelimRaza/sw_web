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

//filter

//promotion
Route::post('promotion/filterItem', 'Promotion\PromotionController@filterItem');
/*Route::post('promotion/mappingRemove/{id}', 'Promotion\PromotionController@zoneMappingDelete');
Route::get('promotion/showZone/{id}', 'Promotion\PromotionController@zoneMappingShow');*/
Route::match(['GET', 'POST'], 'promotion/addZone/{id}', 'Promotion\PromotionController@zoneMappingAdd');
Route::resource('promotion', 'Promotion\PromotionController');

Route::resource('district', 'MasterData\DistrictController');


Route::resource('district', 'GovtHeirarchy\DistrictController');
Route::resource('thana', 'GovtHeirarchy\ThanaController');
Route::resource('ward', 'GovtHeirarchy\WardController');
Route::resource('market', 'GovtHeirarchy\MarketController');
Route::resource('sub_channel', 'MasterData\SubChannelController');
Route::resource('outlet_grade', 'MasterData\OutletGradeController');
Route::resource('category', 'MasterData\CategoryController');

Route::resource('sub-category', 'MasterData\SubCategoryController');
Route::resource('return_reason', 'MasterData\ReturnReasonController');
Route::resource('vehicle', 'MasterData\VehicleController');
Route::resource('no_order_reason', 'MasterData\NoOrderReasonController');
Route::resource('no_delivery_reason', 'MasterData\NoDeliveryReasonController');
Route::resource('cancel_order_reason', 'MasterData\CancelOrderReasonController');
Route::resource('bank', 'MasterData\BankController');

Route::get('test', 'TestController@index');
Route::get('test/home', 'TestController@home');
Route::get('test/menu', 'TestController@menu');

Route::get('printer/order/{cont_id}/{order_id}', 'PrintData\PrintController@orderPrint');
Route::get('printer/salesInvoice/{cont_id}/{order_id}', 'PrintData\PrintController@salesInvoicePrint');

Route::get('printer/return/{cont_id}/{order_id}', 'PrintData\PrintController@returnPrint');
Route::get('printer/returnInvoice/{cont_id}/{order_id}', 'PrintData\PrintController@returnInvoicePrint');
Route::get('printer/collectionPrint/{cont_id}/{collection_id}', 'PrintData\PrintController@collectionPrint');

Route::resource('dashboardPermission', 'Setting\DashboardPermissionController');
Route::get('setting/process', 'Setting\ProcessRunController@index');
Route::post('setting/process', 'Setting\ProcessRunController@store');
Route::get('/home', 'TestController@home')->name('home');
Route::resource('role', 'MasterData\RoleController');

Route::post('groupEmployee/filterEmployeeByGroup', 'GroupDataSetup\GroupEmployeeController@filterEmployeeByGroup');
Route::put('groupEmployee/{id}/reset', 'GroupDataSetup\GroupEmployeeController@reset');
Route::get('groupEmployee/{id}/passChange', 'GroupDataSetup\GroupEmployeeController@passChange');
Route::put('groupEmployee/{id}/change', 'GroupDataSetup\GroupEmployeeController@change');
Route::resource('groupEmployee', 'GroupDataSetup\GroupEmployeeController');
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
Route::resource('employee', 'MasterData\EmployeeController');


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

Route::post('division/filterDivision', 'GroupDataSetup\DivisionController@filterDivision');
Route::resource('division', 'GroupDataSetup\DivisionController');
Route::post('region/filterRegion', 'GroupDataSetup\RegionController@filterRegion');
Route::resource('region', 'GroupDataSetup\RegionController');
Route::post('zone/filterZone', 'GroupDataSetup\ZoneController@filterZone');
Route::resource('zone', 'GroupDataSetup\ZoneController');

Route::post('base/filterBase', 'GroupDataSetup\BaseController@filterBase');
Route::resource('base', 'GroupDataSetup\BaseController');

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


Route::get('depot/depotFormat', 'Depot\DepotController@depotFormatGen');
Route::post('depot/depotUpload', 'Depot\DepotController@depotInsert');
Route::resource('depot', 'Depot\DepotController');


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
Route::resource('route', 'GroupDataSetup\RouteController');


Route::get('pjp/empRouteFormat', 'GroupDataSetup\PJPController@empRouteFormat');
Route::post('pjp/empRouteFormatGen', 'GroupDataSetup\PJPController@empRouteFormatGen');
Route::get('pjp/empRouteUpload', 'GroupDataSetup\PJPController@empRouteUpload');
Route::post('pjp/empRouteUpload', 'GroupDataSetup\PJPController@empRouteInsert');
Route::get('pjp/route_add/{id}', 'GroupDataSetup\PJPController@routeAdd');
Route::get('pjp/route_site/{id}/{emp_id}', 'GroupDataSetup\PJPController@showSite');
Route::get('pjp/site_add/{id}', 'GroupDataSetup\PJPController@siteAdd');
Route::get('pjp/route_site_delete/{id}', 'GroupDataSetup\PJPController@siteRouteDelete');
Route::resource('pjp', 'GroupDataSetup\PJPController');


Route::post('menu/filterMenu', 'MasterData\MenuController@filterMenu');
Route::resource('menu', 'MasterData\MenuController');
Route::resource('product-group', 'MasterData\ProductGroupController');
Route::resource('product-class', 'MasterData\ProductClassController');
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


Route::resource('note_type', 'MasterData\NoteTypeController');
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
Route::resource('channel', 'MasterData\ChannelController');
Route::resource('post', 'Blog\BlogController');
Route::resource('training', 'Blog\TrainingController');
Route::post('sku/filterSKU', 'MasterData\SKUController@filterSKU');
Route::resource('sku', 'MasterData\SKUController');
Route::post('group_sku/filterSKU', 'GroupDataSetup\GroupSKUController@filterSKU');
Route::resource('group_sku', 'GroupDataSetup\GroupSKUController');

Route::resource('display', 'GroupDataSetup\DisplayProgramController');

Route::get('data_upload/siteFormat', 'MasterDataUpload\SiteUploadController@siteFormatGen');
Route::get('data_upload/siteUpload', 'MasterDataUpload\SiteUploadController@siteUpload');
Route::post('data_upload/siteUpload', 'MasterDataUpload\SiteUploadController@siteInsert');

Route::get('data_upload/routeSiteFormat', 'GroupDataUpload\RouteSiteUploadController@routeSiteFormat');
Route::post('data_upload/routeSiteFormat', 'GroupDataUpload\RouteSiteUploadController@routeSiteFormatGen');
Route::get('data_upload/routeSiteUpload', 'GroupDataUpload\RouteSiteUploadController@routeSiteUpload');
Route::post('data_upload/routeSiteUpload', 'GroupDataUpload\RouteSiteUploadController@routeSiteInsert');


Route::get('data_upload/baseSiteFormat', 'GroupDataUpload\BaseSiteMappingUploadController@baseSiteFormatGen');
Route::get('data_upload/baseSiteUpload', 'GroupDataUpload\BaseSiteMappingUploadController@baseSiteUpload');
Route::post('data_upload/baseSiteUpload', 'GroupDataUpload\BaseSiteMappingUploadController@baseSiteInsert');

Route::get('data_upload/groupEmployeeMappingUpload', 'GroupDataUpload\GroupEmployeeMappingUploadController@groupEmployeeMappingUpload');
Route::post('data_upload/groupEmployeeMappingUpload', 'GroupDataUpload\GroupEmployeeMappingUploadController@groupEmployeeMappingUploadInsert');
Route::get('data_upload/groupEmployeeMappingUploadFormat', 'GroupDataUpload\GroupEmployeeMappingUploadController@groupEmployeeMappingUploadFormatGen');

Route::get('data_export/dataExport', 'DataExport\DataExportController@dataExport');
Route::post('data_export/dataExportGroupData', 'DataExport\DataExportController@dataExportGroupData');
Route::post('data_export/dataExportEmpData', 'DataExport\DataExportController@dataExportEmpData');
Route::post('data_export/dataExportDashData', 'DataExport\DataExportController@dataExportDashData');
Route::post('data_export/dataExportOrderData', 'DataExport\DataExportController@dataExportOrderData');


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

Route::get('data_upload/zoneMasterUpload', 'GroupDataUpload\ZoneMasterUploadController@zoneMasterUpload');
Route::post('data_upload/zoneMasterUpload', 'GroupDataUpload\ZoneMasterUploadController@zoneMasterUploadInsert');
Route::get('data_upload/zoneUploadFormat', 'GroupDataUpload\ZoneMasterUploadController@zoneUploadFormatGen');

Route::get('data_upload/baseMasterUpload', 'GroupDataUpload\BaseMasterUploadController@baseMasterUpload');
Route::post('data_upload/baseMasterUpload', 'GroupDataUpload\BaseMasterUploadController@baseMasterUploadInsert');
Route::get('data_upload/baseUploadFormat', 'GroupDataUpload\BaseMasterUploadController@baseUploadFormatGen');

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

Route::get('note/summary', 'Report\NoteReportController@summary');
Route::post('note/filterNoteSummary', 'Report\NoteReportController@filterNoteSummary');
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
//});

