<?php $__env->startSection('content'); ?>
    <style type="text/css">
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="<?php echo e(URL::to('/')); ?>"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li class="">
                            <a href="<?php echo e(URL::to('/market_open')); ?>">All Market</a>
                        </li>
                        <li class="">Create Market</li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
                <?php if(Session::has('success')): ?>
                    <div class="alert alert-success">
                        <strong>Success!</strong><?php echo e(Session::get('success')); ?>

                    </div>
                <?php endif; ?>
                <?php if(Session::has('danger')): ?>
                    <div class="alert alert-danger">
                        <strong>Danger! </strong><?php echo e(Session::get('danger')); ?>

                    </div>
                <?php endif; ?>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <center><strong> ::: Outlet List (VC Cooler) :::
                                </strong></center>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left" action="<?php echo e(route('market_open.store')); ?>"
                                  method="post">
                                <?php echo e(csrf_field()); ?>

                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">District
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="district_id" id="district_id" required
                                                onchange="getThanaList()">
                                            <option value="">Select</option>
                                            <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($district->id); ?>"><?php echo e($district->dsct_code); ?>

                                                    -<?php echo e($district->dsct_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Thana
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="thana_id" id="thana_id" required>
                                            <option value="">Select</option>

                                        </select>
                                    </div>

                                </div>

                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Type
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="r_type" id="r_type" required>
                                            <option value="map">Map View</option>
                                            <option value="report">Report View</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2 col-sm-2 col-xs-12 col-md-offset-2">
                                        <input type="button" class="btn btn-info" value="View" id="viewInMap"
                                               onclick="loadMapData()">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_content">
                            <div class="item form-group">
                                <div id="map" style="height: 900px;"></div>

                            </div>

                        </div>
                    </div>
                </div>
                <div id="tableDiv">
                    <div class="x_panel">

                        <div class="x_content">
                            <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                <div align="right">

                                    <button onclick="exportTableToCSV('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv')"
                                            class="btn btn-warning">Export CSV File
                                    </button>
                                </div>
                                <table id="datatablesa" class="table table-bordered table-responsive"
                                       data-page-length='100'>
                                    <thead>

                                    <tr class="tbl_header">
                                        <th>SI</th>
                                        <th>District</th>
                                        <th>Thana</th>
                                        <th>Market</th>
                                        <th>Site Code</th>
                                        <th>Site Name</th>
                                        <th>Site Bangla Name</th>
                                        <th>Site Address</th>
                                        <th>Site Bangla Address</th>
                                        <th>Owner Name</th>
                                        <th>Owner Bangla Name</th>
                                        <th>Mobile1</th>
                                        <th>Category</th>
                                        <th>VC Cooler</th>
                                        <th>Created Date</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cont">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        $(document).ready(function () {

            $("select").select2({width: 'resolve'});

        });
        $("#map").hide();
        $("#tableDiv").hide();

        function getThanaList() {
            $('#ajax_load').css("display", "block");
            var district_id = $("#district_id").val();
            var _token = $("#_token").val();
            $.ajax({
                type: "GET",
                url: "<?php echo e(URL::to('/')); ?>/json/get/market_open/thana_list",
                data: {
                    district_id: district_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $('#ajax_load').css("display", "none");
                    var $el = $('#thana_id');
                    if (!data) {

                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("---"));
                        $el.selectpicker('destroy');

                    } else {

                        $el.html(' ');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function (key, value) {
                            $el.append($("<option></option>").attr("value", value['id']).text(value['than_code'] + '-' + value['than_name']));
                        });

                    }

                }
            });

        }

        /*function initMap() {

            const uluru = {lat: -25.344, lng: 131.036};
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 4,
                center: uluru,
            });

            const marker = new google.maps.Marker({
                position: uluru,
                map: map,
            });

        }*/
        var latitude = '23.8103', logitude = '90.4125';

        function loadMapData() {
            $('#ajax_load').css("display", "block");
            //$("#map").show();
            var district_id = $("#district_id").val();
            var thana_id = $("#thana_id").val();
            var r_type = $("#r_type").val();

            var _token = $("#_token").val();
            $.ajax({
                type: "GET",
                url: "<?php echo e(URL::to('/')); ?>/json/load/vcCoolerOutletList",
                data: {
                    district_id: district_id,
                    thana_id: thana_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $('#ajax_load').css("display", "none");
                    if (r_type=='map'){
                        $('#tableDiv').hide();
                        $("#map").show();
                        latitude = data[0]['lat'];
                        logitude = data[0]['lng'];
                        initMap(data);
                    }

                    if (r_type=='report'){
                        $("#map").hide();
                        for (var i = 0; i < data.length; i++) {

                            html += '<tr>' +
                                '<td>' + count + '</td>' +
                                '<td>' + data[i]['dsct_name'] + '</td>' +
                                '<td>' + data[i]['than_name'] + '</td>' +
                                '<td>' + data[i]['mktm_name'] + '</td>' +
                                '<td>' + data[i]['site_code'] + '</td>' +
                                '<td>' + data[i]['site_name'] + '</td>' +
                                '<td>' + data[i]['site_olnm'] + '</td>' +
                                '<td>' + data[i]['site_adrs'] + '</td>' +
                                '<td>' + data[i]['site_olad'] + '</td>' +
                                '<td>' + data[i]['site_ownm'] + '</td>' +
                                '<td>' + data[i]['site_olon'] + '</td>' +
                                '<td>' + data[i]['site_mob1'] + '</td>' +
                                '<td>' + data[i]['otcg_name'] + '</td>' +
                                '<td>' + "Yes" + '</td>' +
                                '<td>' + data[i]['created_at'] + '</td>' +
                                '</tr>';
                            count++;
                        }
                        $("#cont").append(html);

                        $('#tableDiv').show();
                    }

                }
            });

        }

        let map;

        function initMap(data) {

            var dhaka = {lat: latitude, lng: logitude};
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: dhaka
            });

            /*var marker = new google.maps.Marker({
                position: dhaka,
                map: map
            });*/
            plotMap(data);

        }

        function plotMap(data) {

            var infoWind = new google.maps.InfoWindow;
            $.each(data, function (key, value) {

                var content = document.createElement('div');
                var strong = document.createElement('strong');
                strong.textContent = "District Name: " + value.dsct_name + ", \ " +
                    "Thana Name: " + value.than_name + ", \ " +
                    "Market Name: " + value.mktm_name + ", \ " +
                    "Outlet ID: " + value.site_code + ", \ " +
                    "Outlet Name: " + value.site_name + ", \ " +
                    "Address: " + value.site_adrs + ", \ " +
                    "Mobile: " + value.site_mob1 + ", \ " +
                    "Category: " + value.otcg_name + ", \ ";

                content.appendChild(strong);
                var marker = new google.maps.Marker({

                    position: new google.maps.LatLng(value.lat, value.lng),
                    map: map

                });

                marker.addListener('mouseover', function () {
                    infoWind.setContent(content);
                    infoWind.open(map, marker);
                })

            });

        };

        function exportTableToCSV(filename) {
            var csv = [];
            var rows = document.querySelectorAll("table tr");
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                for (var j = 0; j < cols.length; j++)
                    row.push(cols[j].innerText);
                csv.push(row.join(","));
            }
            downloadCSV(csv.join("\n"), filename);
        }

        function downloadCSV(csv, filename) {
            var csvFile;
            var downloadLink;
            csvFile = new Blob([csv], {type: "text/csv"});
            downloadLink = document.createElement("a");
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
        }


    </script>
<?php $__env->stopSection(); ?> 
    
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bsolutio/public_html/saleswheel/resources/views/report/vcCooler/vc_cooler_outlet_list.blade.php ENDPATH**/ ?>