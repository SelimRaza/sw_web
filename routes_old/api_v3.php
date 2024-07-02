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
//Route::group(['middleware' => 'throttle:2,1'], function () {



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
    Route::post('note/comment', 'API\v1\LocationData@comment');
    Route::post('note/commentList', 'API\v1\LocationData@commentList');
    Route::post('note/locationData', 'API\v1\LocationData@locationData');
    Route::post('note/saveLocation', 'API\v1\LocationData@saveLocation');
    Route::post('note/updateLocation', 'API\v1\LocationData@updateLocation');
    Route::post('note/siteCodeDetails', 'API\v1\AttNoteData@siteCodeDetails');
    Route::post('note/barCodeDetails', 'API\v1\AttNoteData@barCodeDetails');

    Route::post('login', 'API\v3\MobileAPI@login');
    Route::post('login_hris', 'API\v3\MobileAPI@login_hris');
    Route::post('loginMasumSelimIkramHussainSujanAsad', 'API\v3\MobileAPI@loginMasumSelimIkramHussainSujanAsad');
    Route::post('login_factory', 'API\v3\MobileAPI@login_factory');
    Route::post('app_status', 'API\v3\MobileAPI@app_status');
    Route::post('user_info', 'API\v3\MobileAPI@login_data_in_sync');
    Route::post('common_data', 'API\v3\OrderModuleData@masterData_Common');
    Route::post('site/siteRoutePlan', 'API\v3\MobileAPI@siteRoutePlan');
    Route::post('site/last_five_history', 'API\v3\MobileAPI@last_five_history');
    Route::post('site/last_five_history_details', 'API\v3\MobileAPI@last_five_history_details');
    Route::post('site/last_five_npro_history', 'API\v3\MobileAPI@last_five_npro_history');
    Route::post('time', 'API\v3\MobileAPI@time');
    Route::post('Test_Live', 'API\v3\MobileAPI@Test_Live');
    Route::post('Check_Invoice_Ok_OR_Not', 'API\v2\OrderModuleDataUAE@Check_Invoice_Ok_OR_Not');
    //
    Route::post('foc_data', 'API\v3\OrderModuleData@MasterDataNew_FOC');
	Route::post('submit_leave_HRIS', 'API\v2\OrderModuleDataUAE@submit_leave_HRIS');
    Route::post('outlet_data', 'API\v3\OrderModuleData@MasterDataNew_RouteWise_Outlet');
    Route::post('product_info_data', 'API\v3\OrderModuleData@MasterDataNew_Product_Info_With_Image_Icon_Large');
    Route::post('note/noteList', 'API\v3\MobileAPI@noteList');
    Route::post('note/noteSummaryQty', 'API\v3\MobileAPI@noteSummaryQty');
    Route::post('note/noteListReport', 'API\v3\MobileAPI@noteListReport');
    Route::post('note/noteListReportFromTo', 'API\v3\MobileAPI@noteListReportFromTo');
    Route::post('note/noteSiteWiseUserListReport', 'API\v3\MobileAPI@noteSiteWiseUserListReport');

    Route::post('OrderModuleDataUAE/IsCashPartyCreditRequestExist', 'API\v2\OrderModuleDataUAE@getIsCashPartyCreditRequestExist');
    Route::post('OrderModuleDataUAE/SubmitCashPartyCreditRequest', 'API\v2\OrderModuleDataUAE@SubmitCashPartyCreditRequest');
    Route::post('OrderModuleDataUAE/GetCashPartyCreditRequestList', 'API\v2\OrderModuleDataUAE@GetCashPartyCreditRequestList');
    Route::post('OrderModuleDataUAE/GetCashPartyCreditApprovedList', 'API\v2\OrderModuleDataUAE@GetCashPartyCreditApprovedList');
    Route::post('OrderModuleDataUAE/SubmitCashPartyCreditApproved', 'API\v2\OrderModuleDataUAE@SubmitCashPartyCreditApproved');
    Route::post('OrderModuleDataUAE/GetCashPartyCreditCollectionPendingList', 'API\v2\OrderModuleDataUAE@GetCashPartyCreditCollectionPendingList');
    Route::post('SpecialBudgetDetailsUserWise', 'API\v2\OrderModuleDataUAE@SpecialBudgetDetailsUserWise');
    //
    Route::post('login2', 'API\v3\MobileAPI@login2');
    Route::post('country', 'API\v1\MobileAPI@country');
    Route::post('countryChange', 'API\v1\MobileAPI@countryChange');

    Route::post('empMemu', 'API\v1\MobileAPI@employeeMemu');
    Route::post('empMemuNew', 'API\v3\MobileAPI@employeeMemuNew');
    Route::post('update', 'API\v1\MobileAPI@update');
    Route::post('current_Version_Check', 'API\v1\MobileAPI@current_Version_Check');
    Route::post('validToken', 'API\v1\MobileAPI@validToken');
    Route::post('spd_per', 'API\v1\MobileAPI@spd_per');
    Route::post('userRewordPoint', 'API\v1\MobileAPI@userRewordPoint');
    Route::post('appUrl', 'API\v1\MobileAPI@appUrl');
    Route::post('HRISLoginData', 'API\v3\MobileAPI@HRISLoginData');
    Route::post('time', 'API\v1\MobileAPI@time');

    Route::post('trackError', 'API\v3\MobileAPI@trackError');
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

    Route::post('preOrderCancel', 'API\v1\PreSalesData@preOrderCancel');
    Route::post('preSalesData', 'API\v1\PreSalesData@preSalesData');
    Route::post('preSalesRouteData', 'API\v1\PreSalesData@preSalesRouteData');
    Route::post('saveOrder', 'API\v1\PreSalesData@saveOrder');
    Route::post('siteVisitStatus', 'API\v1\\PreSalesData@siteVisitStatus');
    Route::post('saveReturnOrder', 'API\v1\PreSalesData@saveReturnOrder');
    Route::post('siteVisited', 'API\v1\PreSalesData@siteVisited');
    Route::post('srGrvOrderReport', 'API\v3\MobileAPI@srGrvOrderReport');
    Route::post('srGrvOrderReportDetails', 'API\v3\MobileAPI@srGrvOrderReportDetails');

    Route::post('OrderModuleDataUAE/saveReturnOrderWithReturnType', 'API\v2\OrderModuleDataUAE@saveReturnOrderWithReturnType');

    Route::post('vanStockMoveSave', 'API\v1\VanSalesModuleData@vanStockMoveSave');
    Route::post('vanSalesDataDown', 'API\v1\VanSalesModuleData@vanSalesDataDown');
    Route::post('vanLoadSave', 'API\v1\VanSalesModuleData@vanLoadSave');
  //  Route::post('vanOrderSave', 'API\v1\VanSalesModuleData@vanOrderSave');
   // Route::post('vanGRVSave', 'API\v1\VanSalesModuleData@vanGRVSave');
    Route::post('vanSalesData', 'API\v1\VanSalesModuleData@vanSalesData');
    Route::post('vanRouteSiteData', 'API\v1\VanSalesModuleData@vanRouteSiteData');
    Route::post('vanDeliveryData', 'API\v1\VanSalesModuleData@vanDeliveryData');
    Route::post('vanOrderDeliverySave', 'API\v1\VanSalesModuleData@vanOrderDeliverySave');


    Route::post('getVanTripDetails', 'API\v2\OrderModuleDataUAE@getVanTripDetails');
    Route::post('vanSales/getItemList', 'API\v2\OrderModuleDataUAE@getVanItemList');
    Route::post('CollectionDetailsData', 'API\v2\OrderModuleData@CollectionDetailsData');
    Route::post('vanLoadDataSave', 'API\v2\OrderModuleDataUAE@vanLoadDataSave');
    Route::post('Re_Generated_Invoice_TripWise', 'API\v2\OrderModuleDataUAE@Re_Generated_Invoice_TripWise');
    Route::post('Re_Generated_Invoice_OrderWise', 'API\v2\OrderModuleDataUAE@Re_Generated_Invoice_OrderWise');
    Route::post('vanOrderSave', 'API\v2\OrderModuleDataUAE@vanOrderSaveNew');
	Route::post('SaveVanOrderNew', 'API\v2\OrderModuleDataUAE@SaveVanOrderNew');
    Route::post('SavePoCopyMapping', 'API\v2\OrderModuleDataUAE@SavePoCopyMapping');
    Route::post('vanGRVSave', 'API\v2\OrderModuleDataUAE@vanGRVSave');
    Route::post('OrderModuleDataUAE/CheckINSyncAllDataMergeVanSales', 'API\v2\OrderModuleDataUAE@CheckINSyncAllDataMergeVanSales');
    Route::post('GetDMTripItemStockDetails', 'API\v2\OrderModuleDataUAE@GetDMTripItemStockDetails');
    Route::post('GetVanTripItemStockDetails', 'API\v2\OrderModuleDataUAE@GetVanTripItemStockDetails');
    Route::post('GetVanTripItemLoadDetails', 'API\v2\OrderModuleDataUAE@GetVanTripItemLoadDetails');
    Route::post('GetVanTripLoadRequestDetails', 'API\v2\OrderModuleDataUAE@GetVanTripLoadRequestDetails');

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

    Route::post('block/specialBlockOrder', 'API\v1\BlockOrderData@specialBlockOrder');
    Route::post('block/specialBlockBalance', 'API\v1\BlockOrderData@specialBlockBalance');
    Route::post('block/blockReleaseSave', 'API\v1\BlockOrderData@blockReleaseSave');


    Route::post('promotion/preData', 'API\v1\PromotionData@preData');
    Route::post('promotion/dmData', 'API\v1\PromotionData@dmData');
    Route::post('promotion/vanData', 'API\v1\PromotionData@vanData');
    Route::post('promotion/mspData', 'API\v1\MSPData@mspData');
    Route::post('outStockData', 'API\v1\PreSalesData@outStockData');
    Route::post('defaultDiscountData', 'API\v1\PreSalesData@defaultDiscountData');

    Route::post('printer/salesInvoicePrint', 'API\v1\PrinterData@salesInvoicePrint');

    Route::post('site/sitePermission', 'API\v1\OutletData@sitePermission');
    Route::post('site/sitePermissionRequest', 'API\v1\OutletData@sitePermissionRequest');
    Route::post('site/siteAddInRoutePermissionRequest', 'API\v1\OutletData@siteAddInRoutePermissionRequest');
    Route::post('site/siteAddInRoutePermissionRequestList', 'API\v1\OutletData@siteAddInRoutePermissionRequestList');
    Route::post('site/sitePermissionRequestList', 'API\v1\OutletData@sitePermissionRequestList');
    Route::post('site/sitePermissionRequestAproved', 'API\v1\OutletData@sitePermissionRequestAproved');
    Route::post('site/without_Today_All_Route_SiteList', 'API\v1\OutletData@without_Today_All_Route_SiteList');
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
    Route::post('report/employeeOrderPrintMemo', 'API\v3\MobileAPI@employeeOrderPrintMemo');
    Route::post('UaeOrderPrintMemo', 'API\v3\MobileAPI@UaeOrderPrintMemo');
    Route::post('UaeOrderInvoicePrint', 'API\v3\MobileAPI@UaeOrderInvoicePrint');
    Route::post('UaeInvoicePrint', 'API\v3\MobileAPI@UaeInvoicePrint');//latest only delivered DM
    Route::post('UaeGrvInvoicePrint', 'API\v3\MobileAPI@UaeGrvInvoicePrint');//latest only delivered DM
    Route::post('UaeTaxInvoicePrint', 'API\v3\MobileAPI@UaeTaxInvoicePrint');
    Route::post('report/employeeOrderPrintMemo_new', 'API\v3\MobileAPI@employeeOrderPrintMemo_new');
    Route::post('getOtpForgetPassword', 'API\v3\MobileAPI@getOtpForgetPassword');
    Route::post('getMobileForOtp', 'API\v3\MobileAPI@getMobileForOtp');
    Route::post('CheckOtpValidity', 'API\v3\MobileAPI@CheckOtpValidity');
    Route::post('changePasswordWithOtp', 'API\v3\MobileAPI@changePasswordWithOtp');
    Route::post('verifyOtpCode', 'API\v3\MobileAPI@verifyOtpCode');
    Route::post('getCancelDeliveryReason', 'API\v3\MobileAPI@getCancelDeliveryReason');
    Route::post('PromoReCalculationDetailsData', 'API\v3\MobileAPI@PromoReCalculationDetailsData');
    Route::post('personal_credit_data', 'API\v3\MobileAPI@personal_credit_data');
    Route::post('DpoWiseStockDetails', 'API\v3\MobileAPI@DpoWiseStockDetails');


    Route::post('fireBase/saveToken', 'API\v1\FireBaseData@saveToken');

    Route::post('report/fetchSuperHeroData', 'API\v3\MobileAPI@fetchSuperHeroData');
    Route::post('report/fetchSuperManagerData', 'API\v3\MobileAPI@fetchSuperManagerData');
    Route::post('report/getUserGroupList','API\v3\MobileAPI@getUserGroupList');
    Route::post('orderModuleData/dmTripWiseSROutletInfo', 'API\v3\MobileAPI@dmTripWiseSROutletInfo');
    Route::post('orderModuleData/Pending_collection_siteList', 'API\v3\MobileAPI@Pending_collection_siteList');
    Route::post('OrderModuleDataUAE/SubmitTripReceived', 'API\v3\MobileAPI@SubmitTripReceived');

    Route::post('note/saveNote', 'API\v3\MobileAPI@saveNote');
    Route::post('note/saveNote_sp', 'API\v3\MobileAPI@saveNote_sp');
    Route::post('note/savePendingNote', 'API\v3\MobileAPI@savePendingNote');
    Route::post('catalog_data', 'API\v3\MobileAPI@catalog_data');
    Route::post('getOrderList_ForPOCopyUpload', 'API\v3\MobileAPI@OrderList_ForPOCopyUpload');

    Route::post('report/collectionReportUae', 'API\v3\MobileAPI@collectionReportUae');
    Route::post('MobileAPI/syncAllDataPromotionalDataForDeliveryModule', 'API\v3\MobileAPI@syncAllDataPromotionalDataForDeliveryModule');
    Route::post('nationalityUAE', 'API\v3\MobileAPI@getUaeNationality');

    Route::post('asset/getAssetItemList', 'API\v3\MobileAPI@getAssetItemList');

    // Space Management
    Route::post('getAllSpaceProgram', 'API\v3\SpaceManagement@getAllSpaceProgram');
    Route::post('assignOutletToSpace', 'API\v3\SpaceManagement@assignOutletToSpace');
    Route::post('getRequestedOutletList', 'API\v3\SpaceManagement@getRequestedOutletList');
    Route::post('getRequestedOutletList1', 'API\v3\SpaceManagement@getRequestedOutletList1');
    Route::post('outletAcceptRejectCancel', 'API\v3\SpaceManagement@outletAcceptRejectCancel');
    Route::post('sendVerificationCode', 'API\v3\SpaceManagement@sendVerificationCode');
    Route::post('updateOutlet', 'API\v3\SpaceManagement@updateOutlet');
    Route::post('verifyRequestedOutlet', 'API\v3\SpaceManagement@verifyRequestedOutlet');
    Route::post('sendMessage', 'API\v3\SpaceManagement@sendMessage');
    Route::post('getOutletListToLoadGoods', 'API\v3\SpaceManagement@getOutletListToLoadGoods');
    Route::post('startProgramByUploadingImage', 'API\v3\SpaceManagement@startProgramByUploadingImage');
    // Survey
    Route::post('marketItemSurvey', 'API\v3\ItemSurvey@marketItemSurveyStore');
    Route::post('getSurveyItem', 'API\v3\ItemSurvey@getSurveyItem');


    // Fake GPS Apps
    Route::post('getFakeGpsApps', 'API\v3\FakeGpsApks@getAllInfo');
    // SR Setup All API'S
    Route::post('getPendingSRList', 'API\v3\SRSetup@getPendingSRList');
    Route::post('getDealarList', 'API\v3\SRSetup@getDealarList');
    Route::post('getRouteList', 'API\v3\SRSetup@getRouteList');
    Route::post('getThanaList', 'API\v3\SRSetup@getThanaList');
    Route::post('getSRData', 'API\v3\SRSetup@getSRData');
    Route::post('storeSRData', 'API\v3\SRSetup@storeSRData');

    // OutletLogDataSave
    Route::post('OutletLogDataSave', 'API\v3\OutletLog@storeOutletLogData');
    Route::post('resendOTP', 'API\v3\OutletLog@resendOTP');
    Route::post('verifyOutlet', 'API\v3\OutletLog@verifyOutlet');
    Route::post('empVerifiedOutletList', 'API\v3\OutletLog@empVerifiedOutletList');
    Route::post('getAroundOutletList', 'API\v3\OutletLog@getAroundOutletList');
    // Kachabazar Asset Listing
    Route::post('getAssetList', 'API\v3\OutletLog@getAssetList');
    Route::post('assignAssetToOutlet', 'API\v3\OutletLog@assignAssetToOutlet');
    Route::post('getOwnListedAssetOutlet', 'API\v3\OutletLog@getOwnListedAssetOutlet');
    Route::post('getSelectedOutletAsset', 'API\v3\OutletLog@getSelectedOutletAsset');

});
Route::group(['middleware' => ['VerifyAPIKey', 'throttle:5,1']], function () {
    Route::post('checkLimit', 'API\v3\OutletLog@checkLimit');
});
