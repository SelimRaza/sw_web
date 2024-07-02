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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

// Route::group(['middleware' => 'throttle:30,1'], function () {

Route::post('noteAtt/employeeAttendanceReport', 'API\AttNoteData@employeeAttendanceReport');
Route::post('noteAtt/employeeAttendance', 'API\AttNoteData@employeeAttendance');
Route::post('noteAtt/attendanceSave', 'API\AttNoteData@attendanceSave');
Route::post('noteAtt/saveLeave', 'API\AttNoteData@saveLeave');
Route::post('report/employeeReport', 'API\ReportData@employeeReportByDate');
Route::post('report/employeeOrderList', 'API\ReportData@employeeOrderList');
Route::post('report/employeeSummaryList', 'API\ReportData@employeeSummaryList');
Route::post('report/employeeProductList', 'API\ReportData@employeeProductList');

Route::post('report/orderLineList', 'API\ReportData@orderLineList');
Route::post('report/employeeRoutePlan', 'API\ReportData@employeeRoutePlan');
Route::post('report/employeeRoutePlanSite', 'API\ReportData@employeeRoutePlanSite');
Route::post('report/mapSiteList', 'API\ReportData@mapSiteList');
Route::post('report/mapSiteList_DateWise', 'API\ReportData@mapSiteList_DateWise');
Route::post('report/employeeOrderSummaryPrint', 'API\ReportData@employeeOrderSummaryPrint');
Route::post('report/employeeOrderSummaryPrintNew', 'API\ReportData@employeeOrderSummaryPrintNew');
Route::post('report/employeeOrderSummaryPrintChallan', 'API\ReportData@employeeOrderSummaryPrintChallan');
Route::post('report/employeeOrderPrint', 'API\ReportData@employeeOrderPrint');
Route::post('report/employeeOrderPrintNew', 'API\ReportData@employeeOrderPrintNew');


Route::post('outlet/siteOrderList', 'API\OutletData@siteOrderList');
Route::post('outlet/siteProductList', 'API\OutletData@siteProductList');
Route::post('outlet/siteOrderLineList', 'API\OutletData@siteOrderLineList');
Route::post('outlet/siteHistoryList', 'API\OutletData@siteHistoryList');
Route::post('outlet/noteList', 'API\OutletData@noteList');

Route::post('merchWorkType', 'API\MdrWork@merchWorkType');
Route::post('saveMdrWorkSave', 'API\MdrWork@saveMdrWorkSave');
Route::post('loginHris', 'API\MobileAPI@loginHris');
Route::post('login', 'API\MobileAPI@login');
Route::post('loginNew', 'API\MobileAPI@loginNew');
Route::post('change', 'API\MobileAPI@change');
Route::post('validToken', 'API\MobileAPI@validToken');
Route::post('update', 'API\MobileAPI@update');
Route::post('updatePrg', 'API\MobileAPI@updatePrg');
Route::post('updatePrgTest', 'API\MobileAPI@updatePrgTest');
Route::post('appUrl', 'API\MobileAPI@appUrl');
Route::post('time', 'API\MobileAPI@time');
Route::post('trackError', 'API\MobileAPI@trackError');
Route::post('trackGPS', 'API\MobileAPI@trackGPS');
Route::post('trackGPSSave', 'API\MobileAPI@trackGPSSave');
Route::post('empMemu', 'API\MobileAPI@employeeMemu');
Route::post('empMemuNew', 'API\MobileAPI@employeeMemuNew');

Route::post('cloudImageUp', 'API\Image@upImage');
Route::post('upImage', 'API\MasterData@upImage');
Route::post('departmentList', 'API\MasterData@departmentList');
Route::post('departmentListHigh', 'API\MasterData@departmentListHigh');
Route::post('salesGroupList', 'API\MasterData@salesGroupList');
Route::post('employeeGroupList', 'API\MasterData@employeeGroupList');

Route::post('vanStockMoveSave', 'API\VanSalesModuleData@vanStockMoveSave');
Route::post('vanSalesDataDown', 'API\VanSalesModuleData@vanSalesDataDown');
Route::post('vanLoadSave', 'API\VanSalesModuleData@vanLoadSave');
Route::post('vanOrderSave', 'API\VanSalesModuleData@vanOrderSave');
Route::post('vanGRVSave', 'API\VanSalesModuleData@vanGRVSave');
Route::post('vanSalesData', 'API\VanSalesModuleData@vanSalesData');
Route::post('deliveredData', 'API\DeliveryModuleData@deliveredData');
Route::post('deliveryData', 'API\DeliveryModuleData@deliveryData');
Route::post('dmTripStatusChange', 'API\DeliveryModuleData@dmTripStatusChange');
Route::post('dmOrderDeliverySave', 'API\DeliveryModuleData@dmOrderDeliverySave');
Route::post('dmReturnDeliverySave', 'API\DeliveryModuleData@dmReturnDeliverySave');


Route::post('ipAddress', 'API\DisplayProgram@ipAddress');

//Route::post('report/mapSiteList', 'API\MapReportData@mapSiteList');
Route::post('report/mapEmpList', 'API\MapReportData@mapEmpList');
Route::post('report/mapEmpHistoryList', 'API\MapReportData@mapEmpHistoryList');
Route::post('report/mapSearchList', 'API\MapReportData@mapSearchList');
Route::post('report/mapSearchUserList', 'API\MapReportData@mapSearchUserList');
Route::post('report/mapAroundList', 'API\MapReportData@mapAroundList');
Route::post('report/mapSiteDetails', 'API\MapReportData@mapSiteDetails');
Route::post('my_prg/sr_details', 'API\MyPRGData@userReportData');
Route::post('my_prg/sr_detailsNew', 'API\MyPRGData@userReportDataNew');
Route::post('my_prg/filterMapData', 'API\MyPRGData@filterMapData');
Route::post('my_prg/filterUserMapData', 'API\MyPRGData@filterUserMapData');

Route::post('my_prg/filterRoleTypeData', 'API\MyPRGData@filterRoleTypeData');
Route::post('my_prg/filterOutletTypeData', 'API\MyPRGData@filterOutletTypeData');
Route::post('my_prg/filterOutletMapData', 'API\MyPRGData@filterOutletMapData');
Route::post('my_prg/mapSearchOutletList', 'API\MyPRGData@mapSearchOutletList');
Route::post('g_dashboard/GlobalDashBoardData', 'API\GlobalDashboardData@GlobalDashBoardData');
Route::post('g_dashboard/dashBoardSrDate', 'API\GlobalDashboardData@dashBoardSrDate');
Route::post('g_dashboard/dashBoard1SrDate', 'API\GlobalDashboardData@dashBoard1SrDate');
Route::post('g_dashboard/srDashboard', 'API\GlobalDashboardData@srDashboard');
Route::post('g_dashboard/srOrder', 'API\GlobalDashboardData@srOrder');
Route::post('g_dashboard/srProduct', 'API\GlobalDashboardData@srProduct');
Route::post('g_dashboard/dashBoard1NonSrDate', 'API\GlobalDashboardData@dashBoard1NonSrDate');
Route::post('g_dashboard/dashBoard1ProdSrDate', 'API\GlobalDashboardData@dashBoard1ProdSrDate');
Route::post('g_dashboard/dashBoard1SiteSrDate', 'API\GlobalDashboardData@dashBoard1SiteSrDate');
Route::post('g_dashboard/dashBoard1SiteSrAvgDate', 'API\GlobalDashboardData@dashBoard1SiteAvgSrDate');
Route::post('g_dashboard/srOrderNew', 'API\GlobalDashboardData@srOrderNew');
Route::post('g_dashboard/srProductNew', 'API\GlobalDashboardData@srProductNew');

Route::post('report/mapAllEmpList', 'API\MapReportData@mapAllEmpList');
Route::post('report/mapTrackAroundList', 'API\MapReportData@mapTrackAroundList');
Route::post('report/mapTrackAroundListNew', 'API\MapReportData@mapTrackAroundListNew');
Route::post('report/mapTrackAroundListNew1', 'API\MyPRGData@mapTrackAroundList');
Route::post('report/mapAroundOutlet', 'API\MyPRGData@mapAroundOutlet');
Route::post('report/mapUserHistoryList', 'API\MapReportData@mapUserHistoryList');
Route::post('report/filterMapData', 'API\MapReportData@filterMapData');

Route::post('search/searchDataList', 'API\SearchData@searchDataList');
Route::post('search/searchOutletDataList', 'API\SearchData@searchOutletDataList');


Route::post('preRoute', 'API\OutletData@preRoute');
Route::post('preRouteNew', 'API\OutletData@preRouteNew');
Route::post('empRouteList', 'API\OutletData@empRouteList');
Route::post('empRouteSiteList', 'API\OutletData@empRouteSiteList');
Route::post('sitePermission', 'API\OutletData@sitePermission');
Route::post('outletCategory', 'API\OutletData@outletCategory');
Route::post('outletGrade', 'API\OutletData@outletGrade');
Route::post('siteVisitStatus', 'API\OutletData@siteVisitStatus');
Route::post('newSite', 'API\OutletData@newSite');
Route::post('createNewSite', 'API\OutletData@createNewSite');
Route::post('createNewSiteNew', 'API\OutletData@createNewSiteNew');
Route::post('empRouteSiteMapping', 'API\OutletData@empRouteSiteMapping');
Route::post('empSiteList', 'API\OutletData@empSiteList');
Route::post('empSiteListNew', 'API\OutletData@empSiteListNew');
Route::post('siteUnverified', 'API\OutletData@siteUnverified');
Route::post('siteVerify', 'API\OutletData@siteVerify');
Route::post('newSiteVerify', 'API\OutletData@newSiteVerify');
Route::post('siteVerifyNew', 'API\OutletData@siteVerifyNew');
Route::post('siteVisited', 'API\OutletData@siteVisited');
Route::post('siteStock', 'API\OutletData@siteStock');

Route::post('empSkuList', 'API\SKUData@empSkuList');

Route::post('attendance', 'API\EmployeeData@attendance');
Route::post('employeeList', 'API\EmployeeData@employeeList');
Route::post('employeeListHigh', 'API\EmployeeData@employeeListHigh');
Route::post('employee', 'API\EmployeeData@employee');
Route::post('attendanceHistory', 'API\EmployeeData@attendanceHistory');
Route::post('employeeAttendance', 'API\EmployeeData@employeeAttendance');
Route::post('attendanceData', 'API\EmployeeData@attendanceData');
Route::post('saveLeave', 'API\EmployeeData@saveLeave');
Route::post('employeeLeave', 'API\EmployeeData@employeeLeave');
Route::post('employeeIOM', 'API\EmployeeData@employeeIOM');
Route::post('saveIOM', 'API\EmployeeData@saveIOM');

Route::post('empProgram', 'API\DisplayProgram@displayProgram');

Route::post('note/noteType', 'API\NoteData@noteType');
Route::post('note/subNoteType', 'API\NoteData@getSubNote');
Route::post('note/saveNote', 'API\NoteData@saveNote');
Route::post('note', 'API\NoteData@note');
Route::post('note/noteList', 'API\NoteData@noteList');
Route::post('shareNote', 'API\NoteData@shareNote');
Route::post('noteComment', 'API\NoteData@noteComment');
Route::post('noteCommentList', 'API\NoteData@noteCommentList');

Route::post('inventoryCount', 'API\InventoryData@inventoryCount');
Route::post('inventoryCountList', 'API\InventoryData@inventoryCountList');


Route::post('dashBoardData', 'API\DashboardData@dashBoardData');
Route::post('dashBoardDataUser', 'API\DashboardData@dashBoardDataUser');


Route::post('cashFlow/cashFlowData', 'API\CashFlowData@cashFlowData');


Route::post('peopleAround/company', 'API\PeopleAroundData@company');
Route::post('peopleAround/region', 'API\PeopleAroundData@region');
Route::post('peopleAround/zone', 'API\PeopleAroundData@zone');
Route::post('peopleAround/base', 'API\PeopleAroundData@base');
Route::post('peopleAround/group', 'API\PeopleAroundData@group');
Route::post('peopleAround/details_data', 'API\PeopleAroundData@details_data');


Route::post('orderModuleData/MasterDataNew', 'API\OrderModuleData@MasterDataNew');
Route::post('orderModuleData/MasterData', 'API\OrderModuleData@MasterData');
Route::post('orderModuleData/attendanceSave', 'API\OrderModuleData@attendanceSave');
Route::post('orderModuleData/censusOutletImport', 'API\OrderModuleData@censusOutletImport');
Route::post('orderModuleData/aroundOutlet', 'API\OrderModuleData@aroundOutlet');
Route::post('orderModuleData/orderSave', 'API\OrderModuleData@orderSave');
Route::post('orderModuleData/outletCategory', 'API\OrderModuleData@outletCategory');
Route::post('orderModuleData/govDistrict', 'API\OrderModuleData@govDistrict');
Route::post('orderModuleData/govThana', 'API\OrderModuleData@govThana');
Route::post('orderModuleData/govWard', 'API\OrderModuleData@govWard');
Route::post('orderModuleData/market', 'API\OrderModuleData@market');
Route::post('orderModuleData/outletSave', 'API\OrderModuleData@outletSave');
Route::post('orderModuleData/updateOutlet', 'API\OrderModuleData@updateOutlet');
Route::post('orderModuleData/updateOutletSerial', 'API\OrderModuleData@updateOutletSerial');
Route::post('orderModuleData/updateOutletSave', 'API\OrderModuleData@updateOutletSave');

// });