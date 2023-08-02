<?php

namespace App\Console;

use App\MasterData\Auto;
use App\MasterData\Country;
use App\MasterData\MasterRole;
use App\Process\AttendanceDataProcess;
use App\Process\DashboardDataProcess;
use App\Process\DataGen;
use App\Process\GovtTrackingDataGen;
use App\Process\SendMail;
use App\Process\ReportRequest;
use App\Process\OutletCoverage;
use App\Process\OPDATA;
use App\Process\Productivity;
use App\Process\ItemCoverage;
use App\Process\HrisUser;
use App\Process\NotifyUser;
use App\Process\TrackingDataProcess;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mail;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];


    protected function schedule(Schedule $schedule)
    {


        /*$schedule->call(function () {
            date_default_timezone_set("Asia/Dhaka");
            $date = date('Y-m-d');
            $dashboardDataProcess = new  DashboardDataProcess();
            $dashboardDataProcess->outletDataImport();
            $dashboardDataProcess->pgDashboardSRMTDOrderData($date);
        })->timezone('Asia/Dhaka')->dailyAt("03:45");*/

        $schedule->call(function () {
            date_default_timezone_set("Asia/Dhaka");
            $date = date('Y-m-d');
            $dashboardDataProcess = new  DashboardDataProcess();
           // $dashboardDataProcess->dashboardRoleManagerUpdate();
           /* $dashboardDataProcess->rgManagerUpdate();
            $dashboardDataProcess->uaeDashboardSVData($date);
           // $dashboardDataProcess->pgDashboardSVData($date);
            $dashboardDataProcess->prgDashboardSVData($date);
            $dashboardDataProcess->rgDashboardSVData($date);
           // $dashboardDataProcess->prgDashboardSVData($date);
          //  $dashboardDataProcess->pgDashboardSRData($date);
            $dashboardDataProcess->rgDashboardSRData($date);
            $dashboardDataProcess->uniqueDashboardDataSR($date);
            $dashboardDataProcess->dashboardDataUpdate($date);*/

            $hrdata = new HrisUser();
            $hrdata->createOrUpdateUser((new Country())->country(2));
            $hrdata->createOrUpdateUser((new Country())->country(5));
           // $dashboardDataProcess->outletDataImport();
        })->timezone('Asia/Dhaka')->hourly();

        $schedule->call(function () {
            date_default_timezone_set("Asia/Dhaka");
            $date = date('Y-m-d');
            $dashboardDataProcess = new  DashboardDataProcess();
           // $dashboardDataProcess->uaeDashboardSRData($date);
           // $dashboardDataProcess->pgDashboardSRData($date);
           
		   //$dashboardDataProcess->rgDashboardSRData($date);
           // $dashboardDataProcess->uniqueDashboardDataSR($date);
            //$dashboardDataProcess->dashboardDataUpdate($date);
        })->everyThirtyMinutes();

        $schedule->call(function () {
            $country = Country::all();
            $dataGen = new  DataGen();
            $country1=(new Country())->country(2);
            $country5=(new Country())->country(5);

                date_default_timezone_set($country1->cont_tzon);
                $datetime = new \DateTime(now(), new \DateTimeZone($country1->cont_tzon));
               // $dataGen->prgDashboardSVData($country1->cont_conn, $datetime->format('Y-m-d H:i:s'));// will be off
             ////////   $dataGen->prgDashboardSVData5($country1->cont_conn, $datetime->format('Y-m-d H:i:s'));

                date_default_timezone_set($country5->cont_tzon);
              // $dataGen->prgDashboardSVData($country5->cont_conn, $datetime->format('Y-m-d H:i:s'));// will be off
            ////////   $dataGen->prgDashboardSVData5($country5->cont_conn, $datetime->format('Y-m-d H:i:s'));

        })->timezone('Asia/Dhaka')->dailyAt('03:45');

        $schedule->call(function () {
            $country = Country::all();
            $dataGen = new  DataGen();
            $country1=(new Country())->country(2);
           // foreach ($country as $country1) {
                date_default_timezone_set($country1->cont_tzon);
                $datetime = new \DateTime(now(), new \DateTimeZone($country1->cont_tzon));

                //$dataGen->prgDashboardSRData($country1->cont_conn, $datetime->format('Y-m-d H:i:s'));// will be off
                //$dataGen->prgdashboardUpdate($country1->cont_conn, $datetime->format('Y-m-d H:i:s'));// will be off

               ////////  $dataGen->prgDashboardSRData5($country1->cont_conn, $datetime->format('Y-m-d H:i:s'));
                //////// $dataGen->prgdashboardUpdate5($country1->cont_conn, $datetime->format('Y-m-d H:i:s'));

            $country5=(new Country())->country(5);
            date_default_timezone_set($country5->cont_tzon);

           // $dataGen->prgDashboardSRData($country5->cont_conn, $datetime->format('Y-m-d H:i:s'));// will be off
           // $dataGen->prgdashboardUpdate($country5->cont_conn, $datetime->format('Y-m-d H:i:s'));// will be off

          ////////   $dataGen->prgDashboardSRData5_rfl($country5->cont_conn, $datetime->format('Y-m-d H:i:s'));
         //   $dataGen->prgDashboardSRData5($country5->cont_conn, $datetime->format('Y-m-d H:i:s'));
           ////////  $dataGen->prgdashboardUpdate5($country5->cont_conn, $datetime->format('Y-m-d H:i:s'));

        })->everyFifteenMinutes();

        $schedule->call(function () {
            $country = Country::all();
            $notify = new  NotifyUser();
            $country1=(new Country())->country(2);
            $country5=(new Country())->country(5);
           // foreach ($country as $country1) {
                date_default_timezone_set($country1->cont_tzon);
                date_default_timezone_set($country5->cont_tzon);
                $datetime = new \DateTime(now(), new \DateTimeZone($country1->cont_tzon));
                $time = new \DateTime(now(), new \DateTimeZone($country1->cont_tzon));
                $time->add(new \DateInterval('PT15M'));
              //  $notify->notify($country1->cont_conn, $datetime->format('Y-m-d H:i:s'),$time->format('Y-m-d H:i:s'));

                $datetime1 = new \DateTime(now(), new \DateTimeZone($country5->cont_tzon));
                $time1 = new \DateTime(now(), new \DateTimeZone($country5->cont_tzon));
                $time1->add(new \DateInterval('PT15M'));
               // $notify->notify($country5->cont_conn, $datetime1->format('Y-m-d H:i:s'),$time1->format('Y-m-d H:i:s'));
           // }
        })->everyFifteenMinutes();
        


    $schedule->call(function () {
        $gvt=new GovtTrackingDataGen();
        $gvt->insertProcessedGovtData((new Country())->country(2));  
        $gvt->insertProcessedGovtData((new Country())->country(5));  
        $gvt->insertProcessedGovtData((new Country())->country(9));  
     })->timezone('Asia/Dhaka')->dailyAt('8:35');
    $schedule->call(function () {
        $sendMail=new SendMail();
        $sendMail->sendMailWithLink((new Country())->country(2));
        $sendMail->sendMailWithLink((new Country())->country(5));
        $sendMail->sendMailWithLink((new Country())->country(9));                                   
    } )->timezone('Asia/Dhaka')->dailyAt('5:01');
    $schedule->call(function () {
        $rpt=new ReportRequest();
        $rpt->updateReportStatus((new Country())->country(2));
        $rpt->updateReportStatus((new Country())->country(5));
        $rpt->updateReportStatus((new Country())->country(9));                                    
    } )->timezone('Asia/Dhaka')->dailyAt('23:45');
    // Item Coverage
    $schedule->call(function () {
        $itm=new ItemCoverage();
        $itm->insertToItemCov((new Country())->country(2));                                    
        $itm->insertToItemCov((new Country())->country(5));
        $itm->insertToItemCov((new Country())->country(9));
        $itm->insertToItemCov((new Country())->country(14));                                      
    } )->timezone('Asia/Dhaka')->dailyAt('1:21');
    //Outlet Coverage
    $schedule->call(function () {
        $rpt=new OutletCoverage();
        $rpt->insertToOltCov((new Country())->country(2));                                    
        $rpt->insertToOltCov((new Country())->country(5)); 
        $rpt->insertToOltCov((new Country())->country(9)); 
        $rpt->insertToOltCovAdvanceModule((new Country())->country(14));                             
        $rpt->insertToOltCovAdvanceModule((new Country())->country(4));                             
    } )->timezone('Asia/Dhaka')->dailyAt('1:35');

    $schedule->call(function () {
        $rpt=new OutletCoverage();
        $rpt->insertOutStat((new Country())->country(2));                                                                
        $rpt->insertOutStat((new Country())->country(5));
        $rpt->insertOutStat((new Country())->country(4));                                                                  
    } )->timezone('Asia/Dhaka')->cron('0 1 * * 6');
    // Yester Day Outlet & Item Coverage Data Feed
    // $schedule->call(function () {
    //     $rpt=new OutletCoverage();
    //     $rpt->outletDataFeed((new Country())->country(2));                                    
    //     $rpt->outletDataFeed((new Country())->country(5)); 
    //     $rpt->outletDataFeed((new Country())->country(9)); 
    //     $itm=new ItemCoverage();
    //     $itm->insertToItemCovFeed((new Country())->country(2));                                    
    //     $itm->insertToItemCovFeed((new Country())->country(5));
    //     $itm->insertToItemCovFeed((new Country())->country(9));
    //     $itm->insertToItemCovFeed((new Country())->country(14));                                                             
    // } )->timezone('Asia/Dhaka')->dailyAt('9:48');

    // $schedule->call(function () {
    //     $rpt=new OPDATA();
    //     $rpt->insertMasterOpData((new Country())->country(14));                                                              
    // } )->timezone('Asia/Dhaka')->dailyAt('1:35');
    // $schedule->call(function () {
    //     $rpt=new OutletCoverage();                                                               
    //     $rpt->insertOutStat((new Country())->country(4));                             
    // } )->timezone('Asia/Dhaka')->dailyAt('9:02');




    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
