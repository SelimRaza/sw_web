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

Route::group(['middleware' => ['VerifyAPIKey']], function () {

    // BD Order Module
    Route::post('orderModuleData/attendanceSave', 'API\v2\OrderModuleData@attendanceSave');
    Route::post('orderModuleData/attendanceSave_new', 'API\v2\OrderModuleData@attendanceSave_new');
    Route::post('orderModuleData/orderSave', 'API\v2\OrderModuleData@orderSave');
    Route::post('orderModuleData/dmTripWiseSROutletInfo', 'API\v2\OrderModuleData@dmTripWiseSROutletInfo');
    Route::post('orderModuleData/dmSiteWiseOrderDetails', 'API\v2\OrderModuleData@dmSiteWiseOrderDetails');
    Route::post('orderModuleData/dmTripSummeryReport', 'API\v2\OrderModuleData@dmTripSummeryReport');
    Route::post('orderModuleData/dmSiteWiseCollectionDetailsData_block', 'API\v2\OrderModuleData@dmSiteWiseCollectionDetailsData');
    Route::post('orderModuleData/dmSiteWiseCollectionDetailsData_role', 'API\v2\OrderModuleData@dmSiteWiseCollectionDetailsData_role');
    Route::post('orderModuleData/dmSiteWiseCollectionDetailsData_role_wh', 'API\v2\OrderModuleData@dmSiteWiseCollectionDetailsData_role_wh');
    Route::post('orderModuleData/groupPartyCollectionDetailsData_role', 'API\v2\OrderModuleData@groupPartyCollectionDetailsData_role');
    Route::post('orderModuleData/groupPartyCollectionDetailsData_role_wh', 'API\v2\OrderModuleData@groupPartyCollectionDetailsData_role_wh');
    Route::post('orderModuleData/SRSiteWiseCollectionListData', 'API\v2\OrderModuleData@SRSiteWiseCollectionListData');
    Route::post('orderModuleData/dmItemWiseTripDetails', 'API\v2\OrderModuleData@dmItemWiseTripDetails');
    Route::post('OrderModuleData/GetChallanWiseOrderData', 'API\v2\OrderModuleData@GetChallanWiseOrderData');
    Route::post('OrderModuleData/SubmitChallanWiseDelivery', 'API\v2\OrderModuleData@SubmitChallanWiseDelivery');
    Route::post('OrderModuleData/SubmitInvoiceWiseDelivery', 'API\v2\OrderModuleData@SubmitInvoiceWiseDelivery');
    Route::post('OrderModuleData/SubmitInvoiceWiseDelivery', 'API\v2\OrderModuleData@SubmitInvoiceWiseDelivery');
    Route::post('OrderModuleData/GetOutletNameAndID', 'API\v2\OrderModuleData@GetOutletNameAndID');
    Route::post('OrderModuleData/SRSiteWiseCollectionDetailsData', 'API\v2\OrderModuleData@SRSiteWiseCollectionDetailsData');
    Route::post('OrderModuleData/GetOutletWiseOrderID', 'API\v2\OrderModuleData@GetOutletWiseOrderID');
    Route::post('OrderModuleData/GetOutletWiseOrderDetails', 'API\v2\OrderModuleData@GetOutletWiseOrderDetails');
    Route::post('OrderModuleData/GetOutletBankFromQROutlet', 'API\v2\OrderModuleData@GetOutletBankFromQROutlet');
    Route::post('OrderModuleData/GetOutletBankFromQROutletUsingThan', 'API\v2\OrderModuleData@GetOutletBankFromQROutletUsingThan');
    Route::post('OrderModuleData/outletsByThana', 'API\v2\OrderModuleData@outletsByThana');
    Route::post('OrderModuleData/updateOutletSave', 'API\v2\OrderModuleData@updateOutletSave');
    Route::post('OrderModuleData/MQRUpdateOutletLocationFromMap', 'API\v2\OrderModuleData@MQRUpdateOutletLocationFromMap');
    Route::post('OrderModuleData/RemoveOutletFromRoute', 'API\v2\OrderModuleData@RemoveOutletFromRoute');
    Route::post('OrderModuleData/RFLOutletSave', 'API\v2\OrderModuleData@RFLOutletSave');
    Route::post('OrderModuleData/QROutletSave', 'API\v2\OrderModuleData@QROutletSave');
    Route::post('OrderModuleData/MQROutletSave', 'API\v2\OrderModuleData@MQROutletSave');
    Route::post('OrderModuleData/QROpenOutletCount', 'API\v2\OrderModuleData@QROpenOutletCount');
    Route::post('OrderModuleData/RFLOpenOutletCount', 'API\v2\OrderModuleData@RFLOpenOutletCount');
    Route::post('OrderModuleData/MQROpenOutletCount', 'API\v2\OrderModuleData@MQROpenOutletCount');
    Route::post('OrderModuleData/RFLOpenOutletInfo', 'API\v2\OrderModuleData@RFLOpenOutletInfo');
    Route::post('OrderModuleData/MQROpenOutletInfo', 'API\v2\OrderModuleData@MQROpenOutletInfo');
    Route::post('OrderModuleData/MQRUpdateOutletInfo', 'API\v2\OrderModuleData@MQRUpdateOutletInfo');
    Route::post('OrderModuleData/QROpenOutletCheck', 'API\v2\OrderModuleData@QROpenOutletCheck');
    Route::post('OrderModuleData/MQROpenOutletCheck', 'API\v2\OrderModuleData@MQROpenOutletCheck');
    Route::post('OrderModuleData/employeeOutletOrderInfo', 'API\v2\OrderModuleData@employeeOutletOrderInfo');
    Route::post('OrderModuleData/SrThanalinklist', 'API\v2\OrderModuleData@SrThanalinklist');
    Route::post('OrderModuleData/promotionGalleryLinkList', 'API\v2\OrderModuleData@promotionGalleryLinkList');
    Route::post('OrderModuleData/tutorialDataList', 'API\v2\OrderModuleData@tutorialDataList');
    Route::post('OrderModuleData/reWardsOfferList', 'API\v2\OrderModuleData@reWardsOfferList');
    Route::post('OrderModuleData/reWardsGiftList', 'API\v2\OrderModuleData@reWardsGiftList');
    Route::post('OrderModuleData/reWardsStatementsDataList', 'API\v2\OrderModuleData@reWardsStatementsDataList');
    // UAE Order Module
    Route::post('OrderModuleDataUAE/orderSave', 'API\v2\OrderModuleDataUAE@orderSave');
    Route::post('uaeSiteWiseOrderDetails', 'API\v2\OrderModuleData@SiteWiseOrderDetails');
    Route::post('BizliModuleData/ElectricianOpenSaveData', 'API\v2\BizliModuleData@ElectricianOpenSaveData');
    Route::post('BizliModuleData/Get_AllElectricianProfileUserList', 'API\v2\BizliModuleData@Get_AllElectricianProfileUserList');
    Route::post('BizliModuleData/AllElectricianSearchUserList', 'API\v2\BizliModuleData@AllElectricianSearchUserList');
    Route::post('BizliModuleData/Get_Last_UserSerial', 'API\v2\BizliModuleData@Get_Last_UserSerial');
    Route::post('BizliModuleData/govDistrict', 'API\v2\BizliModuleData@govDistrict');
    Route::post('BizliModuleData/govThana', 'API\v2\BizliModuleData@govThana');
    Route::post('BizliModuleData/Submit_Approved', 'API\v2\BizliModuleData@Submit_Approved');
    Route::post('BizliModuleData/MistiriSearch', 'API\v2\BizliModuleData@MistiriSearch');
    Route::post('BizliModuleData/Submit_Update_Data', 'API\v2\BizliModuleData@Submit_Update_Data');
    Route::post('BizliModuleData/mistiri_type', 'API\v2\BizliModuleData@mistiri_type');


    Route::post('OrderModuleDataUAE/saveReturnOrder', 'API\v2\OrderModuleDataUAE@saveReturnOrder');
    Route::post('OrderModuleDataUAE/SubmitSiteWiseInvoiceGrvDelivery', 'API\v2\OrderModuleDataUAE@SubmitSiteWiseInvoiceGrvDelivery');
    Route::post('OrderModuleDataUAE/SubmitSiteWiseInvoiceGrvDelivery_vat_dis', 'API\v2\OrderModuleDataUAE@SubmitSiteWiseInvoiceGrvDelivery_vat_dis');
    Route::post('OrderModuleDataUAE/ReverseDelivery', 'API\v2\OrderModuleDataUAE@ReverseDelivery');
    Route::post('OrderModuleDataUAE/ReverseDelivery_postman', 'API\v2\OrderModuleDataUAE@ReverseDelivery_postman');
    Route::post('OrderModuleDataUAE/CancelDelivery', 'API\v2\OrderModuleDataUAE@CancelDelivery');
    Route::post('OrderModuleDataUAE/SubmitSiteWiseInvoiceCollection', 'API\v2\OrderModuleDataUAE@SubmitSiteWiseInvoiceCollection');
    Route::post('OrderModuleDataUAE/SubmitGroupSiteWiseInvoiceCollection', 'API\v2\OrderModuleDataUAE@SubmitGroupSiteWiseInvoiceCollection');
    Route::post('OrderModuleDataUAE/SubmitInvoiceSMS', 'API\v2\OrderModuleDataUAE@SubmitInvoiceSMS');
    Route::post('OrderModuleDataUAE/SubmitTripEOT', 'API\v2\OrderModuleDataUAE@SubmitTripEOT');
    Route::post('OrderModuleDataUAE/GetTrip_Summery_Details_Data', 'API\v2\OrderModuleDataUAE@GetTrip_Summery_Details_Data');
    Route::post('OrderModuleDataUAE/GetTrip_Report_Details_Data', 'API\v2\OrderModuleDataUAE@GetTrip_Report_Details_Data');

    Route::post('OrderModuleDataUAE/GetSRRoute', 'API\v2\OrderModuleDataUAE@GetSRRoute');
    Route::post('OrderModuleDataUAE/GetSRDPO', 'API\v2\OrderModuleDataUAE@GetSRDPO');
    Route::post('OrderModuleDataUAE/GetSRSUBDPO', 'API\v2\OrderModuleDataUAE@GetSRSUBDPO');
    Route::post('OrderModuleDataUAE/GetSrSalesGroup', 'API\v2\OrderModuleDataUAE@GetSrSalesGroup');

    Route::post('getItemPicture', 'API\v2\OrderModuleData@getItemPicture');

    //  Route::post('OrderModuleDataUAE/SubmitCashPartyCreditRequest', 'API\v2\OrderModuleDataUAE@SubmitCashPartyCreditRequest');
    // Route::post('OrderModuleDataUAE/GetCashPartyCreditRequestList', 'API\v2\OrderModuleDataUAE@GetCashPartyCreditRequestList');
    Route::post('halfDayFullDayReport', 'API\v2\OrderModuleDataUAE@halfDayFullDayReport');
    Route::post('OrderModuleDataUAE/GetCumulativeVisited_Site_Details_Data', 'API\v2\OrderModuleDataUAE@GetCumulativeVisited_Site_Details_Data');
    Route::post('OrderModuleDataUAE/GetXVisited_Site_Details_Data', 'API\v2\OrderModuleDataUAE@GetXVisited_Site_Details_Data');
    Route::post('OrderModuleDataUAE/GetSRTodayOutletList', 'API\v2\OrderModuleDataUAE@GetSRTodayOutletList');
    Route::post('OrderModuleDataUAE/GetSRTodayOutletListSearch', 'API\v2\OrderModuleDataUAE@GetSRTodayOutletListSearch');
    Route::post('OrderModuleDataUAE/GetSRTodayOutletListSearchQRCode', 'API\v2\OrderModuleDataUAE@GetSRTodayOutletListSearchQRCode');
    Route::post('OrderModuleDataUAE/censusOutletImport', 'API\v2\OrderModuleDataUAE@censusOutletImport');
    Route::post('OrderModuleDataUAE/updateOutletSerial', 'API\v2\OrderModuleDataUAE@updateOutletSerial');
    Route::post('OrderModuleDataUAE/GetOutletSerialData', 'API\v2\OrderModuleDataUAE@GetOutletSerialData');
    Route::post('OrderModuleDataUAE/CheckINSyncAllData', 'API\v2\OrderModuleDataUAE@CheckINSyncAllData');
    Route::post('OrderModuleDataUAE/CheckINSyncAllData_Image', 'API\v2\OrderModuleDataUAE@CheckINSyncAllData_Image');
    Route::post('OrderModuleDataUAE/CheckINSyncAllData_Merge', 'API\v2\OrderModuleDataUAE@CheckINSyncAllData_Merge');

    Route::post('OrderModuleDataUAE/GetTemp_Site_Details_Data', 'API\v2\OrderModuleDataUAE@GetTemp_Site_Details_Data');
    Route::post('OrderModuleDataUAE/CheckINSyncAllData_TempSiteMerge', 'API\v2\OrderModuleDataUAE@CheckINSyncAllData_TempSiteMerge');



    Route::post('OrderModuleDataUAE/fetchMarketOutProduct', 'API\v2\OrderModuleDataUAE@fetchMarketOutProduct');

    //  Route::post('OrderModuleDataUAE/CheckINSyncAllDataMergeVanSales', 'API\v2\OrderModuleDataUAE@CheckINSyncAllDataMergeVanSales');
    Route::post('OrderModuleDataUAE/GetVanSalesTripSite', 'API\v2\OrderModuleDataUAE@GetVanSalesTripSite');

    Route::post('OrderModuleDataUAE/GetPromoSlabDetails', 'API\v2\OrderModuleDataUAE@GetPromoSlabDetails');
    Route::post('OrderModuleDataUAE/GetPromoFOCSlabDetails', 'API\v2\OrderModuleDataUAE@GetPromoFOCSlabDetails');
    Route::post('OrderModuleDataUAE/GetPromoSingleFOCSlabDetails', 'API\v2\OrderModuleDataUAE@GetPromoSingleFOCSlabDetails');
    Route::post('OrderModuleDataUAE/GetItemOrderHistory', 'API\v2\OrderModuleDataUAE@GetItemOrderHistory');

    Route::post('OrderModuleData/MQRoutletCategory', 'API\v2\OrderModuleData@MQRoutletCategory');
    Route::post('OrderModuleData/RFLoutletCategory', 'API\v2\OrderModuleData@RFLoutletCategory');

    Route::post('OrderModuleData/outletCategory', 'API\v2\OrderModuleData@outletCategory');
    Route::post('OrderModuleDataUAE/updateOutlet', 'API\v2\OrderModuleDataUAE@updateOutlet');
    Route::post('OrderModuleDataUAE/updateOutletSave', 'API\v2\OrderModuleDataUAE@updateOutletSave');
    Route::post('OrderModuleDataUAE/Test', 'API\v2\OrderModuleDataUAE@Test');

    Route::post('OrderModuleDataUAE/outletSave', 'API\v2\OrderModuleDataUAE@outletSave');
    Route::post('orderModuleData/aroundOutlet', 'API\v2\OrderModuleData@aroundOutlet');
    Route::post('orderModuleData/aroundOutletSearch', 'API\v2\OrderModuleData@aroundOutletSearch');


    Route::post('orderModuleData/aroundOutlet_UsingMarket', 'API\v2\OrderModuleData@aroundOutlet_UsingMarket');
    Route::post('OrderModuleData/MQRupdateOutletSave', 'API\v2\OrderModuleData@MQRupdateOutletSave');
    Route::post('orderModuleData/govDistrict', 'API\v2\OrderModuleDataUAE@govDistrict');
    Route::post('orderModuleData/govThana', 'API\v2\OrderModuleData@govThana');
    Route::post('orderModuleData/govThana1', 'API\v2\OrderModuleData@govThana1');
    Route::post('orderModuleData/govWard', 'API\v2\OrderModuleData@govWard');
    Route::post('orderModuleData/market', 'API\v2\OrderModuleData@market');
    Route::post('orderModuleData/User_SetUpMarket', 'API\v2\OrderModuleData@User_SetUpMarket');
    Route::post('orderModuleData/UsingThana_GetMarket', 'API\v2\OrderModuleData@UsingThana_GetMarket');
    Route::post('orderModuleData/aroundOutlet_UsingSearch', 'API\v2\OrderModuleData@aroundOutlet_UsingSearch');
    // BD Order MG Module
    Route::post('ManagersOrderModuleData/GetManagersSR', 'API\v2\ManagersOrderModuleData@GetManagers_SR');
    Route::post('ManagersOrderModuleData/GeSrTodayOutlet', 'API\v2\ManagersOrderModuleData@GeSrTodayOutlet');
    Route::post('ManagersOrderModuleData/MGMasterData', 'API\v2\ManagersOrderModuleData@MGMasterData');
    Route::post('ManagersOrderModuleData/MGCensusOutletScan', 'API\v2\ManagersOrderModuleData@MGCensusOutletScan');


});
// BD Order Module
Route::group(['middleware' => 'throttle:30,1'], function () {

Route::post('orderModuleData/MasterDataNew', 'API\v2\OrderModuleData@MasterDataNew');
Route::post('orderModuleData/MasterDataNew_Three', 'API\v2\OrderModuleData@MasterDataNew_Three');
Route::post('orderModuleData/MasterDataNew_Three1', 'API\v2\OrderModuleData@MasterDataNew_Three1');
Route::post('orderModuleData/PromotionTwoDataSync', 'API\v2\OrderModuleData@PromotionTwoDataSync');
Route::post('orderModuleData/MasterDataNew_WebLink', 'API\v2\OrderModuleData@MasterDataNew_WebLink');
Route::post('orderModuleData/MasterDataNew_FOC', 'API\v2\OrderModuleData@MasterDataNew_FOC');
Route::post('orderModuleData/MasterDataNew_RouteWise_Outlet', 'API\v2\OrderModuleData@MasterDataNew_RouteWise_Outlet');
Route::post('orderModuleData/MasterDataNew_Product_Info', 'API\v2\OrderModuleData@MasterDataNew_Product_Info');
Route::post('orderModuleData/MasterDataNew_Product_Info_With_Image', 'API\v2\OrderModuleData@MasterDataNew_Product_Info_With_Image');
Route::post('orderModuleData/MasterDataNew_Product_Info_With_Image_Icon_Large', 'API\v2\OrderModuleData@MasterDataNew_Product_Info_With_Image_Icon_Large');

});