<?php

namespace App\Console;

use App\MasterData\Auto;
use App\MasterData\Country;
use App\MasterData\MasterRole;
use App\Process\AttendanceDataProcess;
use App\Process\DashboardDataProcess;
use App\Process\DataGen;
use App\Process\HrisUser;
use App\Process\NotifyUser;
use App\Process\TrackingDataProcess;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
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
                $notify->notify($country1->cont_conn, $datetime->format('Y-m-d H:i:s'),$time->format('Y-m-d H:i:s'));

                $datetime1 = new \DateTime(now(), new \DateTimeZone($country5->cont_tzon));
                $time1 = new \DateTime(now(), new \DateTimeZone($country5->cont_tzon));
                $time1->add(new \DateInterval('PT15M'));
                $notify->notify($country5->cont_conn, $datetime1->format('Y-m-d H:i:s'),$time1->format('Y-m-d H:i:s'));
           // }
        })->everyFifteenMinutes();


    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
