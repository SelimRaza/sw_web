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


Route::group(['middleware' => 'VerifyAPIKey'], function () {
    Route::post('noteAtt/employeeAttendance', 'API\v1\AttNoteData@employeeAttendance');
    Route::post('noteAtt/attendanceSave', 'API\v1\AttNoteData@attendanceSave');
    Route::post('noteAtt/saveLeave', 'API\v1\AttNoteData@saveLeave');
    Route::post('note/noteType', 'API\v1\AttNoteData@noteType');
    Route::post('note/saveNote', 'API\v1\AttNoteData@saveNote');

    Route::post('login', 'API\v1\MobileAPI@login');

    Route::post('empMemu', 'API\v1\MobileAPI@employeeMemu');
    Route::post('update', 'API\v1\MobileAPI@update');
    Route::post('validToken', 'API\v1\MobileAPI@validToken');
    Route::post('appUrl', 'API\v1\MobileAPI@appUrl');
    Route::post('time', 'API\v1\MobileAPI@time');

    Route::post('trackError', 'API\v1\MobileAPI@trackError');
    Route::post('trackGPS', 'API\v1\MobileAPI@trackGPS');
    Route::post('trackGPSSave', 'API\v1\MobileAPI@trackGPSSave');

    Route::post('g_dashboard/GlobalDashBoardData', 'API\v1\GlobalDashboardData@GlobalDashBoardData');
    Route::post('g_dashboard/dashBoardSrDate', 'API\v1\GlobalDashboardData@dashBoardSrDate');
    Route::post('g_dashboard/dashBoard1SrDate', 'API\v1\GlobalDashboardData@dashBoard1SrDate');
    Route::post('g_dashboard/srDashboard', 'API\v1\GlobalDashboardData@srDashboard');
    Route::post('g_dashboard/srOrder', 'API\v1\GlobalDashboardData@srOrder');
    Route::post('g_dashboard/srProduct', 'API\v1\GlobalDashboardData@srProduct');
    Route::post('g_dashboard/dashBoard1NonSrDate', 'API\v1\GlobalDashboardData@dashBoard1NonSrDate');
    Route::post('search/searchDataList', 'API\v1\SearchData@searchDataList');

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

    Route::post('report/employeeReport', 'API\v1\ReportData@employeeReportByDate');

    Route::post('block/specialBlockOrder', 'API\v1\BlockOrderData@specialBlockOrder');
    Route::post('block/specialBlockBalance', 'API\v1\BlockOrderData@specialBlockBalance');
    Route::post('block/blockReleaseSave', 'API\v1\BlockOrderData@blockReleaseSave');


});

