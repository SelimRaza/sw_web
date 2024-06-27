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
    , 'throttle:30,1'
});*/


Route::group(['middleware' => ['VerifyAPIKey']], function () {
    Route::post('noteAtt/employeeAttendance', 'API\v1\AttNoteData@employeeAttendance');
    Route::post('noteAtt/attendanceSave', 'API\v1\AttNoteData@attendanceSave');
    Route::post('noteAtt/saveIOM', 'API\v1\AttNoteData@saveIOM');
    Route::post('noteAtt/saveFL', 'API\v1\AttNoteData@saveFL');
    Route::post('noteAtt/saveLeave', 'API\v1\AttNoteData@saveLeave');
    Route::post('noteAtt/noteDash', 'API\v1\AttNoteData@noteDash');
    Route::post('noteAtt/notePhoneBook', 'API\v1\AttNoteData@notePhoneBook');
    Route::post('note/phoneBookRemove', 'API\v1\AttNoteData@phoneBookRemove');
    Route::post('note/phoneBookSearch', 'API\v1\AttNoteData@phoneBookSearch');
    Route::post('note/noteType', 'API\v1\AttNoteData@noteType');
    Route::post('note/saveNote', 'API\v1\AttNoteData@saveNote');
    Route::post('note/saveNote1', 'API\v1\AttNoteData@saveNote1');
    

    Route::post('note/noteList', 'API\v1\LocationData@noteList');
    Route::post('note/comment', 'API\v1\LocationData@comment');
    Route::post('note/commentList', 'API\v1\LocationData@commentList');
    Route::post('note/locationData', 'API\v1\LocationData@locationData');
    Route::post('note/saveLocation', 'API\v1\LocationData@saveLocation');
    Route::post('note/updateLocation', 'API\v1\LocationData@updateLocation');
    Route::post('note/siteCodeDetails', 'API\v1\AttNoteData@siteCodeDetails');
    Route::post('note/barCodeDetails', 'API\v1\AttNoteData@barCodeDetails');

    Route::post('login', 'API\v1\MobileAPI@login');
    Route::post('login2', 'API\v1\MobileAPI@login2');
    Route::post('country', 'API\v1\MobileAPI@country');
    Route::post('countryList', 'API\v1\MobileAPI@countryList');
    Route::post('countryChange', 'API\v1\MobileAPI@countryChange');

    Route::post('empMemu', 'API\v1\MobileAPI@employeeMemu');
    Route::post('empMemuNew', 'API\v1\MobileAPI@employeeMemuNew');
    Route::post('update', 'API\v1\MobileAPI@update');
    Route::post('validToken', 'API\v1\MobileAPI@validToken');
    Route::post('spd_per', 'API\v1\MobileAPI@spd_per');
    Route::post('userRewordPoint', 'API\v1\MobileAPI@userRewordPoint');
    Route::post('appUrl', 'API\v1\MobileAPI@appUrl');
    Route::post('time', 'API\v1\MobileAPI@time');

    Route::post('trackError', 'API\v1\MobileAPI@trackError');
    Route::post('trackGPS', 'API\v1\MobileAPI@trackGPS');
    Route::post('trackGPSSave', 'API\v1\MobileAPI@trackGPSSave');


    Route::post('g_dashboard/GlobalDashBoardData5', 'API\v1\GlobalDashboardData@GlobalDashBoardData5');
    Route::post('g_dashboard/GlobalDashBoardData', 'API\v1\GlobalDashboardData@GlobalDashBoardData');
    Route::post('g_dashboard/dashBoardSrDate', 'API\v1\GlobalDashboardData@dashBoardSrDate');
   // Route::post('g_dashboard/dashBoard1SrDate', 'API\v1\GlobalDashboardData@dashBoard1SrDate');
    Route::post('g_dashboard/srDashboard', 'API\v1\GlobalDashboardData@srDashboard');
    Route::post('g_dashboard/srOrder', 'API\v1\GlobalDashboardData@srOrder');
    Route::post('g_dashboard/srProduct', 'API\v1\GlobalDashboardData@srProduct');
    Route::post('g_dashboard/dashBoard1NonSrDate', 'API\v1\GlobalDashboardData@dashBoard1NonSrDate');
    Route::post('g_dashboard/dashBoard1NonSrDate5', 'API\v1\GlobalDashboardData@dashBoard1NonSrDate5');
    Route::post('g_dashboard/dashBoard1ProdSrDate', 'API\v1\GlobalDashboardData@dashBoard1ProdSrDate');
    Route::post('g_dashboard/dashBoard1ProdSrDate5', 'API\v1\GlobalDashboardData@dashBoard1ProdSrDate5');
    Route::post('g_dashboard/dashBoard1SiteSrDate', 'API\v1\GlobalDashboardData@dashBoard1SiteSrDate');
    Route::post('g_dashboard/dashBoard1SiteSrAvgDate', 'API\v1\GlobalDashboardData@dashBoard1SiteSrAvgDate');

    Route::post('search/searchDataList', 'API\v1\SearchData@searchDataList');
    Route::post('search/searchDataList_New', 'API\v1\SearchData@searchDataList_New');

    Route::post('preGrvOrderCancel', 'API\v1\PreSalesData@preGrvOrderCancel');
    Route::post('preOrderCancel', 'API\v1\PreSalesData@preOrderCancel');
    Route::post('VanOrderCancel', 'API\v1\PreSalesData@VanOrderCancel');
    Route::post('VanGRVCancel', 'API\v1\PreSalesData@VanGRVCancel');
    Route::post('preSalesData', 'API\v1\PreSalesData@preSalesData');
    Route::post('preSalesRouteData', 'API\v1\PreSalesData@preSalesRouteData');
    Route::post('saveOrder', 'API\v1\PreSalesData@saveOrder');
    Route::post('siteVisitStatus', 'API\v1\\PreSalesData@siteVisitStatus');
    Route::post('saveReturnOrder', 'API\v1\PreSalesData@saveReturnOrder');
    Route::post('siteVisited', 'API\v1\PreSalesData@siteVisited');



    Route::post('vanStockMoveSave', 'API\v1\VanSalesModuleData@vanStockMoveSave');
    Route::post('vanSalesDataDown', 'API\v1\VanSalesModuleData@vanSalesDataDown');
    Route::post('vanLoadSave', 'API\v1\VanSalesModuleData@vanLoadSave');
    Route::post('vanOrderSave', 'API\v1\VanSalesModuleData@vanOrderSave');
    Route::post('vanGRVSave', 'API\v1\VanSalesModuleData@vanGRVSave');
    Route::post('vanSalesData', 'API\v1\VanSalesModuleData@vanSalesData');
    Route::post('vanRouteSiteData', 'API\v1\VanSalesModuleData@vanRouteSiteData');
    Route::post('vanDeliveryData', 'API\v1\VanSalesModuleData@vanDeliveryData');
    Route::post('vanOrderDeliverySave', 'API\v1\VanSalesModuleData@vanOrderDeliverySave');


    Route::post('deliveredData', 'API\v1\DeliveryModuleData@deliveredData');
    Route::post('deliveryData', 'API\v1\DeliveryModuleData@deliveryData');
    Route::post('dmTripStatusChange', 'API\v1\DeliveryModuleData@dmTripStatusChange');
    Route::post('dmOrderDeliverySave', 'API\v1\DeliveryModuleData@dmOrderDeliverySave');
    Route::post('dmReturnDeliverySave', 'API\v1\DeliveryModuleData@dmReturnDeliverySave');
    Route::post('invoiceSave', 'API\v1\CollectionData@invoiceSave');
    Route::post('dmCollectionSave', 'API\v1\CollectionData@dmCollectionSave');
    Route::post('srCashCustomerCredit', 'API\v1\CollectionData@srCashCustomerCredit');


    Route::post('collectionSave', 'API\v1\CollectionData@collectionSave');
    Route::post('collectionModuleData', 'API\v1\CollectionData@collectionModuleData');
    Route::post('siteInvoiceListData', 'API\v1\CollectionData@siteInvoiceListData');
    Route::post('personalCreditData', 'API\v1\CollectionData@personalCreditData');
    Route::post('masterData1', 'API\v1\MasterData@masterData1');
    Route::post('masterData2', 'API\v1\MasterData@masterData2');
    Route::post('orderCancelReason', 'API\v1\MasterData@orderCancelReason');

    Route::post('report/employeeReport', 'API\v1\ReportData@employeeReportByDate');
    Route::post('report/onReferenceItem', 'API\v1\ReportData@onReferenceItem');
    Route::post('report/outOfStockData', 'API\v1\ReportData@outOfStockData');
    Route::post('report/priceListData', 'API\v1\ReportData@priceListData');
    Route::post('report/priceListLineData', 'API\v1\ReportData@priceListLineData');
    Route::post('report/priceListSearchData', 'API\v1\ReportData@priceListSearchData');
    Route::post('report/myTeamData', 'API\v1\ReportData@myTeamData');
    Route::post('report/employeeRoutePlan', 'API\v1\ReportData@employeeRoutePlan');
    Route::post('report/employeeRoutePlanSite', 'API\v1\ReportData@employeeRoutePlanSite');

    Route::post('block/pendingSpecialBlockOrder', 'API\v1\BlockOrderData@pendingSpecialBlockOrder');
    Route::post('block/approvedSpecialBlockOrder', 'API\v1\BlockOrderData@approvedSpecialBlockOrder');

    Route::post('block/specialBlockBalance', 'API\v1\BlockOrderData@specialBlockBalance');
    Route::post('block/specialBlockOrderReleaseSave', 'API\v1\BlockOrderData@blockReleaseSave');

    Route::post('promotion/preData', 'API\v1\PromotionData@preData');
    Route::post('promotion/dmData', 'API\v1\PromotionData@dmData');
    Route::post('promotion/vanData', 'API\v1\PromotionData@vanData');
    Route::post('promotion/mspData', 'API\v1\MSPData@mspData');
    Route::post('outStockData', 'API\v1\PreSalesData@outStockData');
    Route::post('defaultDiscountData', 'API\v1\PreSalesData@defaultDiscountData');    
    
    Route::post('printer/salesInvoicePrint', 'API\v1\PrinterData@salesInvoicePrint');

    Route::post('site/sitePermission', 'API\v1\OutletData@sitePermission');
    Route::post('site/siteUnverified', 'API\v1\OutletData@siteUnverified');
    Route::post('site/outletCategory', 'API\v1\OutletData@outletCategory');
    Route::post('site/outletGrade', 'API\v1\OutletData@outletGrade');
    Route::post('site/district', 'API\v1\OutletData@district');
    Route::post('site/thana', 'API\v1\OutletData@thana');
    Route::post('site/ward', 'API\v1\OutletData@ward');
    Route::post('site/market', 'API\v1\OutletData@market');
    Route::post('site/createNewSite', 'API\v1\OutletData@createNewSite');
    Route::post('site/routeSite', 'API\v1\OutletData@routeSite');
    Route::post('site/siteVerify', 'API\v1\OutletData@siteVerify');
    Route::post('site/siteRoutePlan', 'API\v1\OutletData@siteRoutePlan');


    Route::post('collection/tracking', 'API\v1\CollectionData@tracking');
    Route::post('collection/trackingOther', 'API\v1\CollectionData@trackingOther');
    Route::post('collection/trackingNoteList', 'API\v1\CollectionData@trackingNoteList');
    Route::post('collection/status', 'API\v1\CollectionData@status');
    Route::post('collection/chequeReceive', 'API\v1\CollectionData@chequeReceive');



    Route::post('map/empList', 'API\v1\MapReportData@empList');
    Route::post('map/searchList', 'API\v1\MapReportData@searchList');
    Route::post('map/aroundList', 'API\v1\MapReportData@aroundList');
    Route::post('map/historyList', 'API\v1\MapReportData@historyList');
    Route::post('map/aroundMe', 'API\v1\MapReportData@aroundMe');
    Route::post('map/filterRoleData', 'API\v1\MapReportData@filterRoleData');
    Route::post('map/filterRoleData', 'API\v1\MapReportData@filterRoleData');
    Route::post('map/filterAcmpData', 'API\v1\MapReportData@filterAcmpData');
    Route::post('map/filterSlgpData', 'API\v1\MapReportData@filterSlgpData');

    Route::post('outOfStock/searchData', 'API\v1\ReportData@outOfStockSearchData');
    Route::post('outOfStock/depotItemData', 'API\v1\ReportData@outOfStockDepotItemData');
    Route::post('outOfStock/depotData', 'API\v1\ReportData@outOfStockDepotData');

    Route::post('report/employeeOrderSummaryPrint', 'API\v1\ReportData@employeeOrderSummaryPrint');
    Route::post('report/employeeOrderPrint', 'API\v1\ReportData@employeeOrderPrint');
    Route::post('report/employeeOrderPrintMemo', 'API\v1\ReportData@employeeOrderPrintMemo');


    Route::post('fireBase/saveToken', 'API\v1\FireBaseData@saveToken');





});