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
    
    /*
    *   API MODEL V3
    */

        /*
        +++++++++++++ MobileAPI +++++++++++
        */
        
        Route::post('login', 'API\v4\MobileAPI@login');
        Route::post('app_status', 'API\v4\MobileAPI@app_status');
        Route::post('site/siteRoutePlan', 'API\v4\MobileAPI@siteRoutePlan');
        Route::post('empMemuNew', 'API\v4\MobileAPI@employeeMemuNew');  
        Route::post('report/fetchSuperHeroData', 'API\v4\MobileAPI@fetchSuperHeroData');
        Route::post('report/fetchSuperManagerData', 'API\v4\MobileAPI@fetchSuperManagerData');
        Route::post('report/getUserGroupList','API\v4\MobileAPI@getUserGroupList');
        Route::post('country', 'API\v4\MobileAPI@country');
        Route::post('countryChange', 'API\v4\MobileAPI@countryChange');
        Route::post('appUrl', 'API\v4\MobileAPI@appUrl');
        Route::post('update', 'API\v4\MobileAPI@update');
        Route::post('change', 'API\v4\MobileAPI@change');
        Route::post('time', 'API\v4\MobileAPI@time');
        Route::post('trackGPS', 'API\v4\MobileAPI@trackGPS');

        Route::post('trackError', 'API\v4\MobileAPI@trackError');
    
        /*
        +++++++++++++ OrderModuleData +++++++++++
        */

        Route::post('common_data', 'API\v4\OrderModuleData@masterData_Common'); 
        Route::post('foc_data', 'API\v4\OrderModuleData@MasterDataNew_FOC');
        Route::post('outlet_data', 'API\v4\OrderModuleData@MasterDataNew_RouteWise_Outlet');
        Route::post('product_info_data', 'API\v4\OrderModuleData@MasterDataNew_Product_Info_With_Image_Icon_Large');
        Route::post('preOrderCancel', 'API\v4\OrderModuleData@preOrderCancel');
        Route::post('orderCancelReason', 'API\v4\OrderModuleData@orderCancelReason');        
        Route::post('outlet/siteHistoryList', 'API\v4\OrderModuleData@siteHistoryList');
        Route::post('outlet/siteOrderList', 'API\v4\OrderModuleData@siteOrderList');        
        Route::post('OrderModuleData/govThana', 'API\v4\OrderModuleData@govThana');
        Route::post('OrderModuleData/govWard', 'API\v4\OrderModuleData@govWard');        
        Route::post('OrderModuleData/aroundOutlet', 'API\v4\OrderModuleData@aroundOutlet');
        Route::post('OrderModuleData/RemoveOutletFromRoute', 'API\v4\OrderModuleData@RemoveOutletFromRoute');
        Route::post('OrderModuleData/outletsByThana', 'API\v4\OrderModuleData@outletsByThana');
        Route::post('OrderModuleData/SrThanalinklist', 'API\v4\OrderModuleData@SrThanalinklist');
        Route::post('OrderModuleData/updateOutletSave', 'API\v4\OrderModuleData@updateOutletSave');
        Route::post('OrderModuleData/market', 'API\v4\OrderModuleData@market');
        Route::post('OrderModuleData/RFLoutletCategory', 'API\v4\OrderModuleData@RFLoutletCategory');
        Route::post('OrderModuleData/RFLOutletSave', 'API\v4\OrderModuleData@RFLOutletSave');
        Route::post('OrderModuleData/RFLOpenOutletCount', 'API\v4\OrderModuleData@RFLOpenOutletCount');
        Route::post('OrderModuleData/RFLOpenOutletInfo', 'API\v4\OrderModuleData@RFLOpenOutletInfo');
        Route::post('OrderModuleData/GetChallanWiseOrderData', 'API\v4\OrderModuleData@GetChallanWiseOrderData');
        Route::post('OrderModuleData/SubmitChallanWiseDelivery', 'API\v4\OrderModuleData@SubmitChallanWiseDelivery');
        Route::post('OrderModuleData/employeeOutletOrderInfo', 'API\v4\OrderModuleData@employeeOutletOrderInfo');
        Route::post('OrderModuleData/GetOutletWiseOrderDetails', 'API\v4\OrderModuleData@GetOutletWiseOrderDetails');
        Route::post('OrderModuleData/SubmitInvoiceWiseDelivery', 'API\v4\OrderModuleData@SubmitInvoiceWiseDelivery');
        Route::post('OrderModuleData/dmTripWiseSROutletInfo', 'API\v4\OrderModuleData@dmTripWiseSROutletInfo');        
        Route::post('OrderModuleData/dmItemWiseTripDetails', 'API\v4\OrderModuleData@dmItemWiseTripDetails');
        Route::post('OrderModuleData/dmSiteWiseOrderDetails', 'API\v4\OrderModuleData@dmSiteWiseOrderDetails');
        Route::post('OrderModuleData/dmSiteWiseCollectionDetailsData', 'API\v4\OrderModuleData@dmSiteWiseCollectionDetailsData');
        Route::post('OrderModuleData/SRSiteWiseCollectionListData', 'API\v4\OrderModuleData@SRSiteWiseCollectionListData');
        Route::post('OrderModuleData/SRSiteWiseCollectionDetailsData', 'API\v4\OrderModuleData@SRSiteWiseCollectionDetailsData');
        Route::post('OrderModuleData/reWardsOfferList', 'API\v4\OrderModuleData@reWardsOfferList');
        Route::post('OrderModuleData/reWardsGiftList', 'API\v4\OrderModuleData@reWardsGiftList');
        Route::post('OrderModuleData/reWardsStatementsDataList', 'API\v4\OrderModuleData@reWardsStatementsDataList');
        Route::post('OrderModuleData/tutorialDataList', 'API\v4\OrderModuleData@tutorialDataList');
        Route::post('OrderModuleData/censusOutletImport', 'API\v4\OrderModuleData@censusOutletImport');
        Route::post('report/mapAroundOutlet', 'API\v4\OrderModuleData@mapAroundOutlet');

         /*
        ++++++++++++++++++New Added v3 OrderModuleData +++++++++++++++++
        */
        
        Route::post('OrderModuleData/MQRUpdateOutletLocationFromMap', 'API\v4\OrderModuleData@MQRUpdateOutletLocationFromMap');
        Route::post('OrderModuleData/MQRoutletCategory', 'API\v2v4\OrderModuleData@MQRoutletCategory');
        Route::post('OrderModuleData/MQROutletSave', 'API\v4\OrderModuleData@MQROutletSave');
        Route::post('OrderModuleData/MQROpenOutletCount', 'API\v4\OrderModuleData@MQROpenOutletCount');
        Route::post('OrderModuleData/QROpenOutletCheck', 'API\v4\OrderModuleData@QROpenOutletCheck');
        Route::post('OrderModuleData/MQROpenOutletInfo', 'API\v4\OrderModuleData@MQROpenOutletInfo');
        Route::post('OrderModuleData/MQRUpdateOutletInfo', 'API\v4\OrderModuleData@MQRUpdateOutletInfo');
        Route::post('OrderModuleData/MQRupdateOutletSave', 'API\v4\OrderModuleData@MQRupdateOutletSave');
        Route::post('OrderModuleData/MQROpenOutletCheck', 'API\v4\OrderModuleData@MQROpenOutletCheck');
        Route::post('OrderModuleData/UsingThana_GetMarket', 'API\v4\OrderModuleData@UsingThana_GetMarket');
        Route::post('OrderModuleData/govThana1', 'API\v4\OrderModuleData@govThana1');
        Route::post('OrderModuleData/User_SetUpMarket', 'API\v4\OrderModuleData@User_SetUpMarket');
        Route::post('OrderModuleData/aroundOutlet_UsingMarket', 'API\v4\OrderModuleData@aroundOutlet_UsingMarket');
        Route::post('OrderModuleData/aroundOutlet_UsingSearch', 'API\v4\OrderModuleData@aroundOutlet_UsingSearch');
        Route::post('OrderModuleData/outletCategory', 'API\v4\OrderModuleData@outletCategory');
        Route::post('OrderModuleData/QROutletSave', 'API\v4\OrderModuleData@QROutletSave');
        Route::post('OrderModuleData/QROpenOutletCount', 'API\v4\OrderModuleData@QROpenOutletCount');
        Route::post('OrderModuleData/SubmitUseRewardPoint', 'API\v4\OrderModuleData@SubmitUseRewardPoint');


        /*
        +++++++++++++ AttNoteData +++++++++++
        */
        Route::post('noteAtt/noteDash', 'API\v4\AttNoteData@noteDash');
        Route::post('note/phoneBookRemove', 'API\v4\AttNoteData@phoneBookRemove');
        Route::post('note/phoneBookSearch', 'API\v4\AttNoteData@phoneBookSearch');
        Route::post('note/barCodeDetails', 'API\v4\AttNoteData@barCodeDetails');
        Route::post('noteAtt/employeeAttendance', 'API\v4\AttNoteData@employeeAttendance');
        Route::post('noteAtt/employeeAttendanceReport', 'API\v4\AttNoteData@employeeAttendanceReport');

        /*
        ++++++++++++++++++New Added v3 AttNoteData +++++++++++++++++
        */
        Route::post('note/siteCodeDetails', 'API\v4\AttNoteData@siteCodeDetails');
        Route::post('noteAtt/attendanceSave', 'API\v4\AttNoteData@attendanceSave');
        Route::post('note/saveNote', 'API\v4\AttNoteData@saveNote');
        Route::post('noteAtt/saveLeave', 'API\v4\AttNoteData@saveLeave');
        Route::post('noteAtt/saveIOM', 'API\v4\AttNoteData@saveIOM');
        Route::post('noteAtt/saveFL', 'API\v4\AttNoteData@saveFL');



        /*
        +++++++++++++ LocationData +++++++++++
        */
        Route::post('note/noteList', 'API\v4\LocationData@noteList');
        Route::post('note/commentList', 'API\v4\LocationData@commentList');
        Route::post('note/updateLocation', 'API\v4\LocationData@updateLocation');
        Route::post('note/comment', 'API\v4\LocationData@comment');
        


        /*
        +++++++++++++ GlobalDashboardData +++++++++++
        */
        Route::post('g_dashboard/GlobalDashBoardData5', 'API\v4\GlobalDashboardData@GlobalDashBoardData5');
        Route::post('g_dashboard/dashBoardSrDate', 'API\v4\GlobalDashboardData@dashBoardSrDate');
        Route::post('g_dashboard/dashBoard1ProdSrDate5', 'API\v4\GlobalDashboardData@dashBoard1ProdSrDate5');
        Route::post('g_dashboard/dashBoard1SiteSrDate', 'API\v4\GlobalDashboardData@dashBoard1SiteSrDate');
        /*
        ++++++++++++++++++New Added v3 GlobalDashboardData +++++++++++++++++
        */
        Route::post('g_dashboard/dashBoard1NonSrDate5', 'API\v4\GlobalDashboardData@dashBoard1NonSrDate5');
        Route::post('g_dashboard/dashBoard1SiteSrAvgDate', 'API\v4\GlobalDashboardData@dashBoard1SiteSrAvgDate');

        /*
        +++++++++++++ SearchData +++++++++++
        */
        Route::post('search/searchDataList_New', 'API\v4\SearchData@searchDataList_New');


        /*
        +++++++++++++ ReportData +++++++++++
        */
        Route::post('report/employeeReport', 'API\v4\ReportData@employeeReportByDate');
        Route::post('report/employeeRoutePlan', 'API\v4\ReportData@employeeRoutePlan');
        Route::post('report/employeeOrderPrint', 'API\v4\ReportData@employeeOrderPrint');
        Route::post('report/employeeRoutePlanSite', 'API\v4\ReportData@employeeRoutePlanSite');
        Route::post('report/employeeOrderSummaryPrintNew', 'API\v4\ReportData@employeeOrderSummaryPrintNew');
        Route::post('report/employeeProductList', 'API\v4\ReportData@employeeProductList');
        Route::post('report/mapSiteList', 'API\v4\ReportData@mapSiteList');
        Route::post('report/mapSiteList_DateWise', 'API\v4\ReportData@mapSiteList_DateWise');    
        Route::post('report/employeeSummaryList', 'API\v4\ReportData@employeeSummaryList');
        Route::post('report/employeeOrderList', 'API\v4\ReportData@employeeOrderList');
        Route::post('report/orderLineList', 'API\v4\ReportData@orderLineList');

        /*
        ++++++++++++++++++New Added v3 ReportData +++++++++++++++++
        */

        Route::post('report/myTeamData', 'API\v4\ReportData@myTeamData');
        Route::post('outOfStock/searchData', 'API\v4\ReportData@outOfStockSearchData');
        Route::post('outOfStock/depotData', 'API\v4\ReportData@outOfStockDepotData');
        Route::post('outOfStock/depotItemData', 'API\v4\ReportData@outOfStockDepotItemData'); 
        Route::post('report/priceListSearchData', 'API\v4\ReportData@priceListSearchData');
        Route::post('report/priceListLineData', 'API\v4\ReportData@priceListLineData');
        Route::post('report/priceListData', 'API\v4\ReportData@priceListData');
        Route::post('report/employeeOrderPrintMemo', 'API\v4\ReportData@employeeOrderPrintMemo');
        Route::post('report/employeeOrderSummaryPrintChallan', 'API\v4\ReportData@employeeOrderSummaryPrintChallan');
        

        /*
        +++++++++++++ MapReportData +++++++++++
        */
        Route::post('map/historyList', 'API\v4\MapReportData@historyList');
        Route::post('map/aroundMe', 'API\v4\MapReportData@aroundMe');
        Route::post('map/filterRoleData', 'API\v4\MapReportData@filterRoleData');
        Route::post('map/filterAcmpData', 'API\v4\MapReportData@filterAcmpData');
        Route::post('map/filterSlgpData', 'API\v4\MapReportData@filterSlgpData');
        Route::post('map/empList', 'API\v4\MapReportData@empList');
        Route::post('map/searchList', 'API\v4\MapReportData@searchList');
        Route::post('map/aroundList', 'API\v4\MapReportData@aroundList');

        /*
        +++++++++++++ OrderModuleDataUAE +++++++++++
        */
        Route::post('OrderModuleData/govDistrict', 'API\v4\OrderModuleDataUAE@govDistrict');
        Route::post('OrderModuleDataUAE/SubmitTripEOT', 'API\v4\OrderModuleDataUAE@SubmitTripEOT');
        Route::post('OrderModuleDataUAE/SubmitSiteWiseInvoiceCollection', 'API\v4\OrderModuleDataUAE@SubmitSiteWiseInvoiceCollection');
        Route::post('OrderModuleDataUAE/SubmitSiteWiseInvoiceGrvDelivery', 'API\v4\OrderModuleDataUAE@SubmitSiteWiseInvoiceGrvDelivery');
        Route::post('OrderModuleDataUAE/SubmitInvoiceSMS', 'API\v4\OrderModuleDataUAE@SubmitInvoiceSMS');
        Route::post('OrderModuleDataUAE/GetVanSalesTripSite', 'API\v4\OrderModuleDataUAE@GetVanSalesTripSite');
        Route::post('OrderModuleDataUAE/CheckINSyncAllDataMergeVanSales', 'API\v4\OrderModuleDataUAE@CheckINSyncAllDataMergeVanSales');
        Route::post('OrderModuleDataUAE/GetSRTodayOutletList', 'API\v4\OrderModuleDataUAE@GetSRTodayOutletList');
        Route::post('OrderModuleDataUAE/orderSave', 'API\v4\OrderModuleDataUAE@orderSave');
        Route::post('OrderModuleDataUAE/saveReturnOrder', 'API\v4\OrderModuleDataUAE@saveReturnOrder');
        
        /*
        ++++++++++++++++++New Added v3 OrderModuleDataUAE +++++++++++++++++
        */
        Route::post('OrderModuleDataUAE/GetItemOrderHistory', 'API\v4\OrderModuleDataUAE@GetItemOrderHistory');
        Route::post('OrderModuleDataUAE/outletSave', 'API\v4\OrderModuleDataUAE@outletSave');
        Route::post('OrderModuleDataUAE/updateOutletSerial', 'API\v4\OrderModuleDataUAE@updateOutletSerial');
        Route::post('OrderModuleDataUAE/updateOutlet', 'API\v4\OrderModuleDataUAE@updateOutlet');
        Route::post('OrderModuleDataUAE/updateOutletSave', 'API\v4\OrderModuleDataUAE@updateOutletSave');
        Route::post('OrderModuleDataUAE/CheckINSyncAllData_Image', 'API\v4\OrderModuleDataUAE@CheckINSyncAllData_Image');
        Route::post('OrderModuleDataUAE/CheckINSyncAllData_Merge', 'API\v4\OrderModuleDataUAE@CheckINSyncAllData_Merge');
        Route::post('OrderModuleDataUAE/GetSRTodayOutletListSearchQRCode', 'API\v4\OrderModuleDataUAE@GetSRTodayOutletListSearchQRCode');
        Route::post('OrderModuleDataUAE/censusOutletImport', 'API\v4\OrderModuleDataUAE@censusOutletImport');
        Route::post('OrderModuleDataUAE/GetSRTodayOutletList', 'API\v4\OrderModuleDataUAE@GetSRTodayOutletList');
        Route::post('OrderModuleDataUAE/GetOutletSerialData', 'API\v4\OrderModuleDataUAE@GetOutletSerialData');
        Route::post('OrderModuleDataUAE/GetSRDPO', 'API\v4\OrderModuleDataUAE@GetSRDPO');
        Route::post('OrderModuleDataUAE/GetSRRoute', 'API\v4\OrderModuleDataUAE@GetSRRoute');
        Route::post('OrderModuleDataUAE/GetSRSUBDPO', 'API\v4\OrderModuleDataUAE@GetSRSUBDPO');
        Route::post('OrderModuleDataUAE/GetPromoSlabDetails', 'API\v4\OrderModuleDataUAE@GetPromoSlabDetails');
        Route::post('OrderModuleDataUAE/GetPromoSingleFOCSlabDetails', 'API\v4\OrderModuleDataUAE@GetPromoSingleFOCSlabDetails');
        Route::post('OrderModuleDataUAE/GetPromoFOCSlabDetails', 'API\v4\OrderModuleDataUAE@GetPromoFOCSlabDetails');

        /*
        +++++++++++++ BizliModuleData +++++++++++
        */
        Route::post('BizliModuleData/Get_AllElectricianProfileUserList', 'API\v4\BizliModuleData@Get_AllElectricianProfileUserList');
        Route::post('BizliModuleData/AllElectricianSearchUserList', 'API\v4\BizliModuleData@AllElectricianSearchUserList');
        Route::post('BizliModuleData/Submit_Approved', 'API\v4\BizliModuleData@Submit_Approved');
        Route::post('BizliModuleData/Get_Last_UserSerial', 'API\v4\BizliModuleData@Get_Last_UserSerial');
        Route::post('BizliModuleData/govDistrict', 'API\v4\BizliModuleData@govDistrict');
        Route::post('BizliModuleData/govThana', 'API\v4\BizliModuleData@govThana');
        Route::post('BizliModuleData/mistiri_type', 'API\v4\BizliModuleData@mistiri_type');
        Route::post('BizliModuleData/ElectricianOpenSaveData', 'API\v4\BizliModuleData@ElectricianOpenSaveData');
        Route::post('BizliModuleData/Submit_Update_Data', 'API\v4\BizliModuleData@Submit_Update_Data');
         


        /*
        ++++++++++++++++++ ManagersOrderModuleData +++++++++++++++++
        */
        Route::post('ManagersOrderModuleData/GetManagersSR', 'API\v4\ManagersOrderModuleData@GetManagers_SR');
        Route::post('ManagersOrderModuleData/MGCensusOutletScan', 'API\vv32\ManagersOrderModuleData@MGCensusOutletScan');
        Route::post('ManagersOrderModuleData/GeSrTodayOutlet', 'API\v4\ManagersOrderModuleData@GeSrTodayOutlet');
        Route::post('ManagersOrderModuleData/MGMasterData', 'API\v4\ManagersOrderModuleData@MGMasterData');



        /*
        ++++++++++++++++++ CollectionData +++++++++++++++++
        */

        Route::post('collection/trackingNoteList', 'API\v4\CollectionData@trackingNoteList');
        Route::post('collection/chequeReceive', 'API\v4\CollectionData@chequeReceive');
        Route::post('collection/trackingOther', 'API\v4\CollectionData@trackingOther');
        Route::post('collection/tracking', 'API\v4\CollectionData@tracking');
        Route::post('collectionModuleData', 'API\v4\CollectionData@collectionModuleData');
        Route::post('siteInvoiceListData', 'API\v4\CollectionData@siteInvoiceListData');
        Route::post('personalCreditData', 'API\v4\CollectionData@personalCreditData');

      


        /*
        ++++++++++++++++++ BlockOrderData +++++++++++++++++
        */

        Route::post('block/specialBlockOrder', 'API\v4\BlockOrderData@specialBlockOrder');
        Route::post('block/specialBlockBalance', 'API\v4\BlockOrderData@specialBlockBalance');

        



        /*
        ++++++++++++++++++ OutletData +++++++++++++++++
        */
        Route::post('site/district', 'API\v4\OutletData@district');
        Route::post('site/thana', 'API\v4\OutletData@thana');
        Route::post('site/ward', 'API\v4\OutletData@ward');
        Route::post('site/market', 'API\v4\OutletData@market');        
        Route::post('site/routeSite', 'API\v4\OutletData@routeSite');
        Route::post('fireBase/saveToken', 'API\v4\FireBaseData@saveToken');

        
        Route::post('printer/salesInvoicePrint', 'API\v4\PrinterData@salesInvoicePrint');


        
         
        


    /*
    * Not Use Mobile App
    */

        Route::post('BizliModuleData/MistiriSearch', 'API\v2\BizliModuleData@MistiriSearch');

    /*
    ***** Not Found
    
    public static final String API_OUTLET_INFO = "mobile_api/index.php/api/VisitMap/outletInfo";

    */

    /*
    *   API MODEL V1
   
    Route::post('noteAtt/noteDash', 'API\v1\AttNoteData@noteDash');
    Route::post('note/phoneBookRemove', 'API\v1\AttNoteData@phoneBookRemove');
    Route::post('note/phoneBookSearch', 'API\v1\AttNoteData@phoneBookSearch');
    Route::post('note/barCodeDetails', 'API\v1\AttNoteData@barCodeDetails');
    Route::post('note/noteList', 'API\v1\LocationData@noteList');
    Route::post('note/commentList', 'API\v1\LocationData@commentList');
    Route::post('note/updateLocation', 'API\v1\LocationData@updateLocation'); 
    Route::post('country', 'API\v1\MobileAPI@country');
    Route::post('countryChange', 'API\v1\MobileAPI@countryChange');
    Route::post('appUrl', 'API\v1\MobileAPI@appUrl');
    Route::post('trackGPS', 'API\v1\MobileAPI@trackGPS');

    Route::post('g_dashboard/GlobalDashBoardData5', 'API\v1\GlobalDashboardData@GlobalDashBoardData5');
    Route::post('g_dashboard/dashBoardSrDate', 'API\v1\GlobalDashboardData@dashBoardSrDate');
    Route::post('g_dashboard/dashBoard1ProdSrDate5', 'API\v1\GlobalDashboardData@dashBoard1ProdSrDate5');
    Route::post('g_dashboard/dashBoard1SiteSrDate', 'API\v1\GlobalDashboardData@dashBoard1SiteSrDate');
    Route::post('g_dashboard/dashBoard1SiteSrAvgDate', 'API\v1\GlobalDashboardData@dashBoard1SiteSrAvgDate');
    Route::post('search/searchDataList_New', 'API\v1\SearchData@searchDataList_New');

    Route::post('report/employeeReport', 'API\v1\ReportData@employeeReportByDate');
    Route::post('report/employeeRoutePlan', 'API\v1\ReportData@employeeRoutePlan');
    Route::post('report/employeeOrderPrint', 'API\v1\ReportData@employeeOrderPrint');


    Route::post('map/historyList', 'API\v1\MapReportData@historyList');
    Route::post('map/aroundMe', 'API\v1\MapReportData@aroundMe');
    Route::post('map/filterRoleData', 'API\v1\MapReportData@filterRoleData');
    Route::post('map/filterAcmpData', 'API\v1\MapReportData@filterAcmpData');
    Route::post('map/filterSlgpData', 'API\v1\MapReportData@filterSlgpData');

    Route::post('preOrderCancel', 'API\v1\PreSalesData@preOrderCancel');
    Route::post('orderCancelReason', 'API\v1\MasterData@orderCancelReason');

    Route::post('collection/trackingNoteList', 'API\v1\CollectionData@trackingNoteList');
    Route::post('collection/chequeReceive', 'API\v1\CollectionData@chequeReceive');
    Route::post('collection/trackingOther', 'API\v1\CollectionData@trackingOther');
    Route::post('collection/tracking', 'API\v1\CollectionData@tracking');
    Route::post('collectionModuleData', 'API\v1\CollectionData@collectionModuleData');
    Route::post('siteInvoiceListData', 'API\v1\CollectionData@siteInvoiceListData');
    Route::post('personalCreditData', 'API\v1\CollectionData@personalCreditData');


    Route::post('block/specialBlockOrder', 'API\v1\BlockOrderData@specialBlockOrder');
    Route::post('block/specialBlockBalance', 'API\v1\BlockOrderData@specialBlockBalance');


    Route::post('g_dashboard/dashBoard1NonSrDate5', 'API\v1\GlobalDashboardData@dashBoard1NonSrDate5');
    Route::post('g_dashboard/dashBoard1SiteSrAvgDate', 'API\v1\GlobalDashboardData@dashBoard1SiteSrAvgDate');
    
    Route::post('printer/salesInvoicePrint', 'API\v1\PrinterData@salesInvoicePrint');
    
    Route::post('note/barCodeDetails', 'API\v1\AttNoteData@barCodeDetails');
    Route::post('note/siteCodeDetails', 'API\v1\AttNoteData@siteCodeDetails');
    Route::post('note/phoneBookSearch', 'API\v1\AttNoteData@phoneBookSearch');
    Route::post('note/phoneBookRemove', 'API\v1\AttNoteData@phoneBookRemove');


    Route::post('report/employeeRoutePlan', 'API\v1\ReportData@employeeRoutePlan');
    Route::post('report/myTeamData', 'API\v1\ReportData@myTeamData');
    Route::post('outOfStock/searchData', 'API\v1\ReportData@outOfStockSearchData');
    Route::post('outOfStock/depotData', 'API\v1\ReportData@outOfStockDepotData');
    Route::post('outOfStock/depotItemData', 'API\v1\ReportData@outOfStockDepotItemData'); 
    Route::post('report/priceListSearchData', 'API\v1\ReportData@priceListSearchData');
    Route::post('report/priceListLineData', 'API\v1\ReportData@priceListLineData');
    Route::post('report/priceListData', 'API\v1\ReportData@priceListData');
    Route::post('report/employeeOrderPrintMemo', 'API\v1\ReportData@employeeOrderPrintMemo');
    Route::post('report/employeeRoutePlanSite', 'API\v1\ReportData@employeeRoutePlanSite');

    Route::post('map/empList', 'API\v1\MapReportData@empList');
    Route::post('map/searchList', 'API\v1\MapReportData@searchList');
    Route::post('map/aroundList', 'API\v1\MapReportData@aroundList');

    Route::post('site/district', 'API\v1\OutletData@district');
    Route::post('site/thana', 'API\v1\OutletData@thana');
    Route::post('site/ward', 'API\v1\OutletData@ward');
    Route::post('site/market', 'API\v1\OutletData@market');        
    Route::post('site/routeSite', 'API\v1\OutletData@routeSite');
    
    Route::post('note/comment', 'API\v1\LocationData@comment');
    Route::post('trackGPS', 'API\v1\MobileAPI@trackGPS');
    
    */    
 

    /*
    *   API MODEL V2

    Route::post('orderModuleData/govDistrict', 'API\v2\OrderModuleDataUAE@govDistrict');
    Route::post('OrderModuleDataUAE/SubmitTripEOT', 'API\v2\OrderModuleDataUAE@SubmitTripEOT');
    Route::post('OrderModuleDataUAE/SubmitSiteWiseInvoiceCollection', 'API\v2\OrderModuleDataUAE@SubmitSiteWiseInvoiceCollection');
    Route::post('OrderModuleDataUAE/SubmitSiteWiseInvoiceGrvDelivery', 'API\v2\OrderModuleDataUAE@SubmitSiteWiseInvoiceGrvDelivery');
    Route::post('OrderModuleDataUAE/SubmitInvoiceSMS', 'API\v2\OrderModuleDataUAE@SubmitInvoiceSMS');
    Route::post('orderModuleData/govThana', 'API\v2\OrderModuleData@govThana');
    Route::post('orderModuleData/govWard', 'API\v2\OrderModuleData@govWard');
    Route::post('orderModuleData/aroundOutlet', 'API\v2\OrderModuleData@aroundOutlet');
    Route::post('OrderModuleData/RemoveOutletFromRoute', 'API\v2\OrderModuleData@RemoveOutletFromRoute');
    Route::post('OrderModuleData/outletsByThana', 'API\v2\OrderModuleData@outletsByThana');
    Route::post('OrderModuleData/SrThanalinklist', 'API\v2\OrderModuleData@SrThanalinklist');
    Route::post('OrderModuleData/updateOutletSave', 'API\v2\OrderModuleData@updateOutletSave');
    Route::post('orderModuleData/market', 'API\v2\OrderModuleData@market');
    Route::post('OrderModuleData/RFLoutletCategory', 'API\v2\OrderModuleData@RFLoutletCategory');
    Route::post('OrderModuleData/RFLOutletSave', 'API\v2\OrderModuleData@RFLOutletSave');
    Route::post('OrderModuleData/RFLOpenOutletCount', 'API\v2\OrderModuleData@RFLOpenOutletCount');
    Route::post('OrderModuleData/RFLOpenOutletInfo', 'API\v2\OrderModuleData@RFLOpenOutletInfo');
    Route::post('OrderModuleData/GetChallanWiseOrderData', 'API\v2\OrderModuleData@GetChallanWiseOrderData');
    Route::post('OrderModuleData/SubmitChallanWiseDelivery', 'API\v2\OrderModuleData@SubmitChallanWiseDelivery');
    Route::post('OrderModuleData/employeeOutletOrderInfo', 'API\v2\OrderModuleData@employeeOutletOrderInfo');
    Route::post('OrderModuleData/GetOutletWiseOrderDetails', 'API\v2\OrderModuleData@GetOutletWiseOrderDetails');
    Route::post('OrderModuleData/SubmitInvoiceWiseDelivery', 'API\v2\OrderModuleData@SubmitInvoiceWiseDelivery');
    Route::post('orderModuleData/dmTripWiseSROutletInfo', 'API\v2\OrderModuleData@dmTripWiseSROutletInfo');
    
    Route::post('orderModuleData/dmItemWiseTripDetails', 'API\v2\OrderModuleData@dmItemWiseTripDetails');
    Route::post('orderModuleData/dmSiteWiseOrderDetails', 'API\v2\OrderModuleData@dmSiteWiseOrderDetails');
    Route::post('orderModuleData/dmSiteWiseCollectionDetailsData', 'API\v2\OrderModuleData@dmSiteWiseCollectionDetailsData');
    Route::post('orderModuleData/SRSiteWiseCollectionListData', 'API\v2\OrderModuleData@SRSiteWiseCollectionListData');
    Route::post('OrderModuleData/SRSiteWiseCollectionDetailsData', 'API\v2\OrderModuleData@SRSiteWiseCollectionDetailsData');
    Route::post('OrderModuleData/reWardsOfferList', 'API\v2\OrderModuleData@reWardsOfferList');
    Route::post('OrderModuleData/reWardsGiftList', 'API\v2\OrderModuleData@reWardsGiftList');
    Route::post('OrderModuleData/reWardsStatementsDataList', 'API\v2\OrderModuleData@reWardsStatementsDataList');
    Route::post('OrderModuleData/tutorialDataList', 'API\v2\OrderModuleData@tutorialDataList');

    Route::post('BizliModuleData/Get_AllElectricianProfileUserList', 'API\v2\BizliModuleData@Get_AllElectricianProfileUserList');
    Route::post('BizliModuleData/AllElectricianSearchUserList', 'API\v2\BizliModuleData@AllElectricianSearchUserList');
    Route::post('BizliModuleData/Submit_Approved', 'API\v2\BizliModuleData@Submit_Approved');

    Route::post('OrderModuleData/MQRUpdateOutletLocationFromMap', 'API\v2\OrderModuleData@MQRUpdateOutletLocationFromMap');
    Route::post('orderModuleData/market', 'API\v2\OrderModuleData@market');
    Route::post('OrderModuleData/MQRoutletCategory', 'API\v2\OrderModuleData@MQRoutletCategory');
    Route::post('OrderModuleData/MQROutletSave', 'API\v2\OrderModuleData@MQROutletSave');
    Route::post('OrderModuleData/MQROpenOutletCount', 'API\v2\OrderModuleData@MQROpenOutletCount');
    Route::post('OrderModuleData/QROpenOutletCheck', 'API\v2\OrderModuleData@QROpenOutletCheck');
    Route::post('OrderModuleData/MQROpenOutletInfo', 'API\v2\OrderModuleData@MQROpenOutletInfo');
    Route::post('OrderModuleData/MQRUpdateOutletInfo', 'API\v2\OrderModuleData@MQRUpdateOutletInfo');
    Route::post('OrderModuleData/MQRupdateOutletSave', 'API\v2\OrderModuleData@MQRupdateOutletSave');
    Route::post('OrderModuleData/MQROpenOutletCheck', 'API\v2\OrderModuleData@MQROpenOutletCheck');
    Route::post('orderModuleData/UsingThana_GetMarket', 'API\v2\OrderModuleData@UsingThana_GetMarket');
    Route::post('orderModuleData/govThana1', 'API\v2\OrderModuleData@govThana1');
    Route::post('orderModuleData/User_SetUpMarket', 'API\v2\OrderModuleData@User_SetUpMarket');
    Route::post('orderModuleData/aroundOutlet_UsingMarket', 'API\v2\OrderModuleData@aroundOutlet_UsingMarket');
    Route::post('orderModuleData/aroundOutlet_UsingSearch', 'API\v2\OrderModuleData@aroundOutlet_UsingSearch');
    Route::post('OrderModuleData/outletCategory', 'API\v2\OrderModuleData@outletCategory');
    Route::post('OrderModuleData/QROutletSave', 'API\v2\OrderModuleData@QROutletSave');.
    Route::post('OrderModuleData/QROpenOutletCount', 'API\v2\OrderModuleData@QROpenOutletCount');
    Route::post('OrderModuleData/SubmitUseRewardPoint', 'API\v2\OrderModuleData@SubmitUseRewardPoint');
     
    Route::post('ManagersOrderModuleData/GetManagersSR', 'API\v2\ManagersOrderModuleData@GetManagers_SR');
    Route::post('ManagersOrderModuleData/MGCensusOutletScan', 'API\v2\ManagersOrderModuleData@MGCensusOutletScan');
    Route::post('ManagersOrderModuleData/GeSrTodayOutlet', 'API\v2\ManagersOrderModuleData@GeSrTodayOutlet');
    Route::post('ManagersOrderModuleData/MGMasterData', 'API\v2\ManagersOrderModuleData@MGMasterData');
    Route::post('BizliModuleData/ElectricianOpenSaveData', 'API\v2\BizliModuleData@ElectricianOpenSaveData');

    */
    

    /*
    *   API MODEL 
    
    Route::post('noteAtt/employeeAttendance', 'API\AttNoteData@employeeAttendance');
    Route::post('noteAtt/employeeAttendanceReport', 'API\AttNoteData@employeeAttendanceReport');
    Route::post('update', 'API\MobileAPI@update');
    Route::post('change', 'API\MobileAPI@change');
    Route::post('report/employeeRoutePlanSite', 'API\ReportData@employeeRoutePlanSite');
    Route::post('report/employeeOrderSummaryPrintNew', 'API\ReportData@employeeOrderSummaryPrintNew');
    Route::post('report/employeeProductList', 'API\ReportData@employeeProductList');
    Route::post('report/mapSiteList', 'API\ReportData@mapSiteList');
    Route::post('report/mapSiteList_DateWise', 'API\ReportData@mapSiteList_DateWise');    
    Route::post('report/employeeSummaryList', 'API\ReportData@employeeSummaryList');
    Route::post('report/employeeOrderList', 'API\ReportData@employeeOrderList');
    Route::post('report/orderLineList', 'API\ReportData@orderLineList');

    Route::post('outlet/siteHistoryList', 'API\OutletData@siteHistoryList');
    Route::post('outlet/siteOrderList', 'API\OutletData@siteOrderList');

    Route::post('orderModuleData/censusOutletImport', 'API\OrderModuleData@censusOutletImport');
    Route::post('report/mapAroundOutlet', 'API\MyPRGData@mapAroundOutlet');


    Route::post('shareNote', 'API\NoteData@shareNote');
    Route::post('noteComment', 'API\NoteData@noteComment');
    Route::post('noteCommentList', 'API\NoteData@noteCommentList');

    */


    
    
});