
<?php $__env->startSection('content'); ?>
<div class="right_col" role="main" id="app_all_db">
  <?php if($pt_show==1): ?>
  <div
    class="col-md-12 col-sm-12 col-xs-12"
    style="background-color: #fff; margin-top: -10px"
  >
    <div class="">
      <div class="">
        <?php if($parent_emp): ?>
        <h4 class="text-center">
          <?php echo e($pt_show==1?$parent_emp[0]->role_name."-".$parent_emp[0]->aemp_name."-".$parent_emp[0]->aemp_usnm:''); ?><small></small>
        </h4>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>
  <!-- top tiles -->
  <div class="row tile_count ">
    <div class="col-md-6 col-sm-6 col-xs-6 tile_stats_count">
          <div class="menu_div">
                <input
              type="hidden"
              name="_token"
              id="_token"
              value="<?php echo csrf_token(); ?>"
            />
            <input type="hidden" id="emid" value="<?php echo e($emid); ?>" />
            <span class="count_top"><i class="fa fa-user"></i> Total SR</span>
            <?php if($employee): ?>
            <div class="count" onclick="loadUnderEmployee()" style="cursor: pointer">
              <?php echo e($employee[0]->totalSr); ?>

            </div>
            <span class="count_bottom">
              <i class="fa fa-sort-asc">
                <i
                  class="red"
                  data-toggle="modal"
                  data-target="#myModalOffSr"
                  style="cursor: pointer"
                  onclick="getOffSRList()"
                  >Off:<?php echo e($employee[0]->offSr?$employee[0]->offSr:0); ?></i
                >
                <i class="green">|On:<?php echo e($employee[0]->onSr?$employee[0]->onSr:0); ?></i>
                <i class="gray">|Act:<?php echo e($employee[0]->actSr?$employee[0]->actSr:0); ?></i>
              </i>
            </span>
            <?php else: ?>
            <div class="count" onclick="loadUnderEmployee()" style="cursor: pointer">
              0
            </div>
            <span class="count_bottom">
              <i class="fa fa-sort-asc">
                <i class="red">Off:0</i>
                <i class="green">|On:0</i>
                <i class="gray">|Act:0</i>
              </i>
            </span>
            <?php endif; ?>
          </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-6 tile_stats_count "  id="outletDiv">
        <div class="menu_div">
              <span class="count_top"
              ><i class="glyphicon glyphicon-list-alt"></i> Route Outlet</span
            >
            <?php if($employee): ?> <?php if($emp_role==2): ?>
            <div style="text-size: 15px; font-style: bold">
              <span
                class="count"
                ><?php echo e($employee?$employee[0]->totalScheduleCall:0); ?></span
              >out of:
              <span style="font-size: 16px; font-weight: bold"
                ><?php echo e($today_open_out?round($today_open_out[0]->opened_outlet_today/6):0); ?>

                <sub
                  ><?php echo e($today_open_out?$today_open_out[0]->opened_outlet_today:0); ?></sub
                ></span
              >
            </div>
            <span class="count_bottom">
              <i class="fa fa-sort-asc">
                <i class=""
                  >W:<?php echo e($out_type_count[0]->Whole_Sale_site?$out_type_count[0]->Whole_Sale_site:0); ?></i
                >
                <i class=""
                  >|T:<?php echo e($out_type_count[0]->Tong_site?$out_type_count[0]->Tong_site:0); ?></i
                >
                <i class=""
                  >|GT:<?php echo e($out_type_count[0]->Trade_M_site?$out_type_count[0]->Trade_M_site:0); ?></i
                >
              </i>
            </span>
      
            <?php else: ?>
            <div class="count"><?php echo e($employee?$employee[0]->totalScheduleCall:0); ?></div>
            <span class="count_bottom">
              <i class="fa fa-sort-asc">
                <i class="red"
                  >V:<?php echo e($employee[0]->total_visited?$employee[0]->total_visited:0); ?></i
                >
                <i class="green"
                  >|S:<?php echo e($employee[0]->productiveMemo?$employee[0]->productiveMemo:0); ?></i
                >
                <i class="gray"
                  >|PE/Olt:<?php echo e($employee[0]->APEOIT?$employee[0]->APEOIT:0); ?></i
                >
              </i>
            </span>
            <?php endif; ?> <?php else: ?>
            <div class="count">0</div>
            <span class="count_bottom">
              <i class="fa fa-sort-asc">
                <i class="red">V:0</i>
                <i class="green">|S:0</i>
                <i class="gray">|A./Olt:0</i>
              </i>
            </span>
            <?php endif; ?>
        </div>
    </div>
    <!-- visited card for TSM -->
    <?php if($emp_role==2): ?>
    <div class="col-md-6 col-sm-6 col-xs-6 tile_stats_count rp_type_div" id="">
      <span class="count_top"
        ><i class="glyphicon glyphicon-list-alt"></i> Visited</span
      >
      <?php if($employee): ?>
      <div class="count">
        <a
          href="#"
          data-toggle="modal"
          data-target="#myModalVisit"
          style="cursor: pointer"
          onclick="getCatWiseOutVisit()"
          ><?php echo e($employee[0]->total_visited?$employee[0]->total_visited:0); ?></a
        >
      </div>
      <span class="count_bottom">
        <i class="fa fa-sort-asc">
          <i class=""
            >S:<?php echo e($employee[0]->productiveMemo?$employee[0]->productiveMemo:0); ?></i
          >
          <i class="">
            |PE/OLT:<?php echo e($employee[0]->APEOIT?$employee[0]->APEOIT:0); ?></i
          >
        </i>
      </span>
      <?php else: ?>
      <div class="count">0</div>
      <span class="count_bottom">
        <i class="fa fa-sort-asc">
          <i class="">S:0</i>
          <i class="">PE/OLT:0</i>
        </i>
      </span>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    <!-- visited card for TSM END-->
    <div class="col-md-6 col-sm-6 col-xs-6 tile_stats_count rp_type_div">
      <span class="count_top"
        ><i class="glyphicon glyphicon-tag"></i> Strike Rate</span
      >
      <?php if($employee): ?> <?php if($employee[0]->total_visited !=0){
      $stk_rate=($employee[0]->productiveMemo/$employee[0]->total_visited)*100;
      } else{ $stk_rate=0.00; } ?>
      <div class="count"><?php echo e(number_format($stk_rate, 2)); ?>%</div>
      <span class="count_bottom">
        <i class="fa fa-sort-asc">
          <i class="green"
            >-Ve.:<?php echo e($employee[0]->nonProductiveSr?$employee[0]->nonProductiveSr:0); ?></i
          >
        </i>
      </span>
      <?php else: ?>
      <div class="count">0%</div>
      <span class="count_bottom">
        <i class="fa fa-sort-asc">
          <i class="green">-Ve:0</i>
        </i>
      </span>
      <?php endif; ?>
    </div>
    <div class="col-md-1 col-sm-1"></div>
    <div class="col-md-5 col-sm-5 col-xs-6 tile_stats_count rp_type_div">
      <?php if($emp_role==2): ?>
      <span class="count_top"
        ><i class="glyphicon glyphicon-list-alt"></i> Non visited</span
      >
      <?php else: ?>
      <span class="count_top"
        ><i class="glyphicon glyphicon-list-alt"></i> LPC</span
      >
      <?php endif; ?> <?php if($employee): ?> <?php if($emp_role==2): ?> <?php
      $out=$today_open_out?$today_open_out[0]->opened_outlet_today:0;
      if($visited){ $month1=$out-($visited[0]->onemonth);
      $month2=$out-($visited[0]->twomonth);
      $month3=$out-($visited[0]->threemonth); }else{ $month1=$out; $month2=$out;
      $month3=$out; } ?>
      <div class="nonvisited">
        <p style="margin: 0 0 -6px">
          L1M:<span style="font-size: 20px; font-weight: bold"><?php echo e($month1); ?></span>
        </p>
        <p style="margin: 0 0 -6px">
          L2M:<span style="font-size: 20px; font-weight: bold"><?php echo e($month2); ?></span>
        </p>
        <p style="margin: 0 0 -6px">
          L3M:<span style="font-size: 20px; font-weight: bold"><?php echo e($month3); ?></span>
        </p>
      </div>

      <span class="count_bottom">
        <i class="fa fa-sort-asc">
          <i class="green"></i>
        </i>
      </span>
      <?php else: ?>
      <div class="count">
        <?php echo e($employee?(number_format($employee[0]->lineParCall,2)):0); ?>

      </div>
      <span class="count_bottom">
        <i class="fa fa-sort-asc">
          <i class="">A.M/SR:<?php echo e($employee[0]->AMSR?$employee[0]->AMSR:0); ?></i>
        </i>
      </span>
      <?php endif; ?> <?php else: ?>
      <div class="count">0</div>
      <span class="count_bottom">
        <i class="fa fa-sort-asc">
          <i class="">A.M/SR:0</i>
        </i>
      </span>
      <?php endif; ?>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-6 tile_stats_count rp_type_div">
      <span class="count_top"
        ><i class="glyphicon glyphicon-list-alt"></i> Todays Order</span
      >
      <?php if($employee): ?>
      <div class="count">
        <?php echo e($employee?(number_format($employee[0]->totalOrderAmount,2)):0); ?>

      </div>
      <span class="count_bottom">
        <i class="fa fa-sort-asc">
          <i class=""
            >Today Tgt:<?php echo e($employee[0]->totalTargetAmount?(number_format($employee[0]->totalTargetAmount/26,2)):0); ?></i
          >
        </i>
      </span>
      <?php else: ?>
      <div class="count">0</div>
      <span class="count_bottom">
        <i class="fa fa-sort-asc">
          <i class="">Today Tgt:0</i>
        </i>
      </span>
      <?php endif; ?>
    </div>
    <div class="col-md-1 col-sm-1"></div>
    <div class="col-md-5 col-sm-5 col-xs-6 tile_stats_count rp_type_div"
      id="contributionDiv"
    >
      <span class="count_top"
        ><i class="glyphicon glyphicon-list-alt"></i> Contribution</span
      >
      <?php if($employee): ?>
      <div class="count">
        <?php echo e($employee?(number_format($employee[0]->totalOrderAmount/($employee[0]->actSr?$employee[0]->actSr:1),2)):0); ?>

      </div>
      <span class="count_bottom">
        <i class="fa fa-sort-asc">
          <i class=""
            >A.Olt/SR:<?php echo e($employee[0]->totalScheduleCall?(number_format($employee[0]->totalScheduleCall/($employee[0]->onSr?$employee[0]->onSr:1),2)):0); ?></i
          >
          <i class=""
            >A.V/SR:<?php echo e($employee[0]->total_visited?(number_format($employee[0]->total_visited/($employee[0]->onSr?$employee[0]->onSr:1),2)):0); ?></i
          >
        </i>
      </span>
      <?php else: ?>
      <div class="count">0</div>
      <span class="count_bottom">
        <i class="fa fa-sort-asc">
          <i class="">A.Olt/SR:0</i>
          <i class="">A.V/SR:0</i>
        </i>
      </span>
      <?php endif; ?>
    </div>
  </div>
  <!-- /top tiles -->

  <!-- chart -section -1 start -->
  <?php if($employee): ?>
  <div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Out.Coverage<small></small></h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#">Settings 1</a>
                <a class="dropdown-item" href="#">Settings 2</a>
              </div>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
            <!-- <button class="btn btn-dark btn-sm">Daily</button>
                <button class="btn btn-warning btn-sm">Weekly</button> -->
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <canvas id="lineChartOutCov" height="300"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Strike Rate<small></small></h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#">Settings 1</a>
                <a class="dropdown-item" href="#">Settings 2</a>
              </div>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
            <!-- <button class="btn btn-dark btn-sm">Daily</button>
                <button class="btn btn-warning btn-sm">Weekly</button> -->
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <canvas id="lineChartStrikeRate" height="300"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>LPC<small></small></h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#">Settings 1</a>
                <a class="dropdown-item" href="#">Settings 2</a>
              </div>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
            <!-- <button class="btn btn-dark btn-sm">Daily</button>
                <button class="btn btn-warning btn-sm">Weekly</button> -->
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <canvas id="lineChartLpc" height="300"></canvas>
        </div>
      </div>
    </div>
    <!-- <div class="col-md-3 col-sm-3 col-xs-12  ">
          <div class="x_panel">
            <div class="x_title">
              <h2>+VE<small></small></h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                      <a class="dropdown-item" href="#">Settings 1</a>
                      <a class="dropdown-item" href="#">Settings 2</a>
                    </div>
                </li>
                <li><a class="close-link"><i class="fa fa-close"></i></a>
                </li>
                
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <canvas id="lineChartPositive" height="300"></canvas>
            </div>
          </div>
      </div>    -->
  </div>
  <!-- chart -section -1 End -->
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Outlet Statistics (Avg)<small></small></h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#">Settings 1</a>
                <a class="dropdown-item" href="#">Settings 2</a>
              </div>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <div id="echart_pie_out_stat" style="height: 350px"></div>
        </div>
      </div>
    </div>
    <div class="col-md-7 col-sm-7 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Visit vs Order<small></small></h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#">Settings 1</a>
                <a class="dropdown-item" href="#">Settings 2</a>
              </div>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <div id="visit_vs_memo_bar" style="height: 350px"></div>
        </div>
      </div>
    </div>
  </div>
  <!-- chart section 2 start -->
  <div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Target vs Order<small></small></h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#">Settings 1</a>
                <a class="dropdown-item" href="#">Settings 2</a>
              </div>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
            <!-- <button class="btn btn-dark btn-sm">Daily</button>
                <button class="btn btn-warning btn-sm">Weekly</button> -->
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <div id="bar_graph_tgOrd" style="width: 100%; height: 300px"></div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Order vs Delivery<small></small></h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#">Settings 1</a>
                <a class="dropdown-item" href="#">Settings 2</a>
              </div>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
            <!-- <button class="btn btn-dark btn-sm">Daily</button>
                <button class="btn btn-warning btn-sm">Weekly</button> -->
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <div id="bar_graph_OrdDelv" style="width: 100%; height: 300px"></div>
        </div>
      </div>
    </div>
  </div>
  <!-- chart section 2 end -->
  <!-- table section start  -->
  <div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Select Top(Based on Yesterday)<small></small></h2>
          <ul class="nav navbar-right panel_toolbox">
            <input
              type="hidden"
              name="_token"
              id="_token"
              value="<?php echo csrf_token(); ?>"
            />
            <?php echo e(csrf_field()); ?>

            <select
              class="form-control"
              name="type"
              id="type"
              onchange="getTop(this.value)"
            >
              <option value="">Select</option>
              <option value="SR">Top 10 SR</option>
              <option value="TSM">Top 10 TSM</option>
              <option value="DSM">Top 10 DSM</option>
              <option value="CLASS">Top 10 Class</option>
              <option value="ITEM">Top 10 Item</option>
              <option value="OUTLET">Top 10 Outlet</option>
            </select>
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <div class="table-responsive">
            <table class="table table-striped bulk_action">
              <thead>
                <div id="top_load" style="display: none">
                  <img src="<?php echo e(asset("theme/production/images/gif-load.gif")); ?>"
                  class="ajax-loader"/>
                </div>
                <tr id="SR">
                  <th class="column-title">Name</th>
                  <th class="column-title">Zone</th>
                  <th class="column-title">Mobile</th>
                  <th class="column-title">Amount</th>
                </tr>
                <tr id="DSM">
                  <th class="column-title">Name</th>
                  <th class="column-title">Mobile</th>
                  <th class="column-title">Amount</th>
                </tr>
                <tr id="ITEM">
                  <th class="column-title">Sl</th>
                  <th class="column-title">Name</th>
                  <th class="column-title">Amount</th>
                </tr>
                <tr id="OUTLET">
                  <th class="column-title">Name</th>
                  <th class="column-title">Mobile</th>
                  <th class="column-title">Zone</th>
                  <th class="column-title">Amount</th>
                </tr>
              </thead>

              <tbody id="topData"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Select Bottom(Based on Yesterday)<small></small></h2>
          <ul class="nav navbar-right panel_toolbox">
            <input
              type="hidden"
              name="_token"
              id="_token"
              value="<?php echo csrf_token(); ?>"
            />
            <?php echo e(csrf_field()); ?>

            <select
              class="form-control"
              name="type"
              id="type"
              onchange="getBottom(this.value)"
            >
              <option value="">Select</option>
              <option value="SR">Bottom 10 SR</option>
              <option value="TSM">Bottom 10 TSM</option>
              <option value="DSM">Bottom 10 DSM</option>
              <option value="CLASS">Bottom 10 Class</option>
              <option value="ITEM">Bottom 10 Item</option>
              <option value="OUTLET">Bottom 10 Outlet</option>
            </select>
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <div class="table-responsive">
            <table class="table table-striped bulk_action">
              <thead>
                <div id="btm_load" style="display: none">
                  <img src="<?php echo e(asset("theme/production/images/gif-load.gif")); ?>"
                  class="ajax-loader"/>
                </div>
                <tr id="bSR">
                  <th class="column-title">Name</th>
                  <th class="column-title">Zone</th>
                  <th class="column-title">Mobile</th>
                  <th class="column-title">Amount</th>
                </tr>
                <tr id="bDSM">
                  <th class="column-title">Name</th>
                  <th class="column-title">Mobile</th>
                  <th class="column-title">Amount</th>
                </tr>
                <tr id="bITEM">
                  <th class="column-title">Sl</th>
                  <th class="column-title">Name</th>
                  <th class="column-title">Amount</th>
                </tr>
                <tr id="bOUTLET">
                  <th class="column-title">Name</th>
                  <th class="column-title">Mobile</th>
                  <th class="column-title">Zone</th>
                  <th class="column-title">Amount</th>
                </tr>
              </thead>

              <tbody id="bottomData"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- table section end  -->
  <!-- line chart for target vs order | order vs delivery -->
  <div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Target vs Order<small></small></h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#">Settings 1</a>
                <a class="dropdown-item" href="#">Settings 2</a>
              </div>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
            <!-- <button class="btn btn-dark btn-sm">Daily</button>
                <button class="btn btn-warning btn-sm">Weekly</button> -->
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <canvas id="tgOrder"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Order vs Delivery<small></small></h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#">Settings 1</a>
                <a class="dropdown-item" href="#">Settings 2</a>
              </div>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
            <!-- <button class="btn btn-dark btn-sm">Daily</button>
                <button class="btn btn-warning btn-sm">Weekly</button> -->
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <canvas id="ordDelivery"></canvas>
        </div>
      </div>
    </div>
  </div>
  <!-- end tg vs ord| ord vs deliver -->
  <!-- chart section 4 start -->
  <div class="row" style="display: none">
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Line Per Call<small></small></h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#">Settings 1</a>
                <a class="dropdown-item" href="#">Settings 2</a>
              </div>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
            <!-- <button class="btn btn-dark btn-sm">Daily</button>
                <button class="btn btn-warning btn-sm">Weekly</button> -->
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <div id="bar_graph_lpc" style="width: 100%; height: 300px"></div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>+VE<small></small></h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#">Settings 1</a>
                <a class="dropdown-item" href="#">Settings 2</a>
              </div>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
            <!-- <button class="btn btn-dark btn-sm">Daily</button>
                <button class="btn btn-warning btn-sm">Weekly</button> -->
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <div id="bar_graph_positive" style="width: 100%; height: 300px"></div>
        </div>
      </div>
    </div>
  </div>
  <!-- chart section 4 end -->

  <div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12">
      <div class="x_panel tile fixed_height_320">
        <div class="x_title">
          <h2>Monthly Data</h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#">Settings 1</a></li>
                <li><a href="#">Settings 2</a></li>
              </ul>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <h4>Sales,Delivery, Target</h4>
          <br />
          <br />
          <div class="widget_summary">
            <div class="w_left w_25">
              <span> Target</span>
            </div>
            <div class="w_center pg_bar_percen">
              <div class="progress">
                <div
                  class="progress-bar bg-forestgreen"
                  role="progressbar"
                  aria-valuenow="60"
                  aria-valuemin="0"
                  aria-valuemax="100"
                  style="width: 100%"
                >
                  <span class="sr-only"></span>
                </div>
                <!-- <div class="progress-bar bg-green" role="progressbar" aria-valuenow="60"
                       aria-valuemin="0" aria-valuemax="100" style="width:100%;">
                    <span class="sr-only"></span>
                  </div> -->
              </div>
            </div>
            <div class="">
              <span></span>
            </div>
            <div class="clearfix"></div>
          </div>

          <div class="widget_summary">
            <div class="w_left w_25">
              <span> Order</span>
            </div>
            <div class="w_center pg_bar_percen">
              <div class="progress">
                <!-- <?php
                    $width=0;
                    if($employee){
                      if($employee[0]->totalTargetAmount==0){
                        $width=100;
                      }
                      else if($employee[0]->totalTargetAmount<=$employee[0]->mtd_total_sales){
                          $width=100;
                      }else{
                        $width=($employee[0]->mtd_total_sales*100/$employee[0]->totalTargetAmount);
                        if($width>100){
                          $width=100;
                        }
                      }
                    }
                  ?> -->
                <div
                  class="progress-bar bg-green"
                  role="progressbar"
                  aria-valuenow="60"
                  aria-valuemin="0"
                  aria-valuemax="100"
                  style="width: 99%"
                >
                  <span class="sr-only"></span>
                </div>
              </div>
            </div>
            <div class="">
              <span></span>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="widget_summary">
            <div class="w_left w_25">
              <span> Delivery</span>
            </div>
            <div class="w_center pg_bar_percen">
              <div class="progress">
                <!-- <?php
                    $width=0;
                    if($employee){
                      if($employee[0]->mtd_total_delivery==0){
                        $width=0;
                      }
                      else if($employee[0]->mtd_total_delivery==$employee[0]->mtd_total_sales){
                          $width=100;
                      }else{
                        $mts=$employee[0]->mtd_total_sales==0?1:$employee[0]->mtd_total_sales;
                        $width=($employee[0]->mtd_total_delivery*100/$mts);
                        if($width>100){
                          $width=100;
                        }
                      }
                    }
                  ?> -->
                <div
                  class="progress-bar bg-orange"
                  role="progressbar"
                  aria-valuenow="60"
                  aria-valuemin="0"
                  aria-valuemax="100"
                  style="width: 80%"
                >
                  <span class="sr-only"></span>
                </div>
              </div>
            </div>
            <div class="">
              <!-- <span></span> -->
            </div>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4 col-sm-4 col-xs-12">
      <div class="x_panel tile fixed_height_320 overflow_hidden">
        <div class="x_title">
          <h2>SR Summary</h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#">Settings 1</a></li>
                <li><a href="#">Settings 2</a></li>
              </ul>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <table class="" style="width: 100%">
            <tr>
              <th style="width: 37%">
                <p></p>
              </th>
              <th>
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                  <p class="">Device</p>
                </div>
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                  <p class="">Progress</p>
                </div>
              </th>
            </tr>
            <tr>
              <td>
                <canvas
                  class="canvasDoughnut1"
                  height="140"
                  width="140"
                  style="margin: 15px 10px 10px 0"
                ></canvas>
              </td>
              <td>
                <table class="tile_info">
                  <tr>
                    <td>
                      <p>
                        <i class="fa fa-square" style="color: #1abb9c"></i
                        >Positive
                      </p>
                    </td>
                    <td>
                      <?php echo e($employee?(number_format(($employee[0]->pvSr*100/($employee[0]->onSr==0?1:$employee[0]->totalSr)),2)):0); ?>%
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <p>
                        <i class="fa fa-square" style="color: #f5f75a"></i
                        >Negative
                      </p>
                    </td>
                    <td>
                      <?php echo e($employee?(number_format(($employee[0]->nveSr*100/($employee[0]->onSr==0?1:$employee[0]->totalSr)),2)):0); ?>%
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <p>
                        <i class="fa fa-square" style="color: lightgreen"></i
                        >Inactive
                      </p>
                    </td>
                    <td>
                      <?php echo e($employee?(number_format(($employee[0]->inactSr*100/($employee[0]->onSr==0?1:$employee[0]->totalSr)),2)):0); ?>%
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <p>
                        <i class="fa fa-square" style="color: #f9522d"></i>Off
                      </p>
                    </td>
                    <td>
                      <?php echo e($employee?(number_format(($employee[0]->offSr*100/($employee[0]->onSr==0?1:$employee[0]->totalSr)),2)):0); ?>%
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <p>
                        <i class="fa fa-square" style="color: #a9a9a9"></i>Leave
                      </p>
                    </td>
                    <td>
                      <?php echo e($employee?(number_format(($employee[0]->levSr*100/($employee[0]->onSr==0?1:$employee[0]->totalSr)),2)):0); ?>%
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
      <div class="x_panel tile fixed_height_320 overflow_hidden">
        <div class="x_title">
          <h2>SR Productivity</h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#">Settings 1</a></li>
                <li><a href="#">Settings 2</a></li>
              </ul>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <table class="" style="width: 100%">
            <tr>
              <th style="width: 37%">
                <p></p>
              </th>
              <th>
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                  <p class="">SR Type</p>
                </div>
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                  <p class="">Progress</p>
                </div>
              </th>
            </tr>
            <tr>
              <td>
                <canvas
                  class="canvasDoughnut2"
                  height="140"
                  width="140"
                  style="margin: 15px 10px 10px 0"
                ></canvas>
              </td>
              <td>
                <table class="tile_info">
                  <tr>
                    <td>
                      <p>
                        <i class="fa fa-square" style="color: #35a236"></i
                        >Productive
                      </p>
                    </td>
                    <td>
                      <?php echo e($employee?(number_format(($employee[0]->pvSr*100/($employee[0]->onSr==0?1:$employee[0]->onSr)),2)):0); ?>%
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <p>
                        <i class="fa fa-square" style="color: #f9522d"></i
                        >Non-P.
                      </p>
                    </td>
                    <td>
                      <?php echo e($employee?(number_format(($employee[0]->nonProductiveSr*100/($employee[0]->onSr==0?1:$employee[0]->onSr)),2)):0); ?>%
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="row" style="display: none">
    <div class="col-md-4 col-sm-4 col-xs-12">
      <div class="x_panel tile fixed_height_320">
        <div class="x_title">
          <h2>App Versions</h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#">Settings 1</a></li>
                <li><a href="#">Settings 2</a></li>
              </ul>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <h4>App Usage across versions</h4>
          <div class="widget_summary">
            <div class="w_left w_25">
              <span>0.1.5.2</span>
            </div>
            <div class="w_center w_55">
              <div class="progress">
                <div
                  class="progress-bar bg-green"
                  role="progressbar"
                  aria-valuenow="60"
                  aria-valuemin="0"
                  aria-valuemax="100"
                  style="width: 66%"
                >
                  <span class="sr-only">60% Complete</span>
                </div>
              </div>
            </div>
            <div class="w_right w_20">
              <span>123k</span>
            </div>
            <div class="clearfix"></div>
          </div>

          <div class="widget_summary">
            <div class="w_left w_25">
              <span>0.1.5.3</span>
            </div>
            <div class="w_center w_55">
              <div class="progress">
                <div
                  class="progress-bar bg-green"
                  role="progressbar"
                  aria-valuenow="60"
                  aria-valuemin="0"
                  aria-valuemax="100"
                  style="width: 45%"
                >
                  <span class="sr-only">60% Complete</span>
                </div>
              </div>
            </div>
            <div class="w_right w_20">
              <span>53k</span>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="widget_summary">
            <div class="w_left w_25">
              <span>0.1.5.4</span>
            </div>
            <div class="w_center w_55">
              <div class="progress">
                <div
                  class="progress-bar bg-green"
                  role="progressbar"
                  aria-valuenow="60"
                  aria-valuemin="0"
                  aria-valuemax="100"
                  style="width: 25%"
                >
                  <span class="sr-only">60% Complete</span>
                </div>
              </div>
            </div>
            <div class="w_right w_20">
              <span>23k</span>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="widget_summary">
            <div class="w_left w_25">
              <span>0.1.5.5</span>
            </div>
            <div class="w_center w_55">
              <div class="progress">
                <div
                  class="progress-bar bg-green"
                  role="progressbar"
                  aria-valuenow="60"
                  aria-valuemin="0"
                  aria-valuemax="100"
                  style="width: 5%"
                >
                  <span class="sr-only">60% Complete</span>
                </div>
              </div>
            </div>
            <div class="w_right w_20">
              <span>3k</span>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="widget_summary">
            <div class="w_left w_25">
              <span>0.1.5.6</span>
            </div>
            <div class="w_center w_55">
              <div class="progress">
                <div
                  class="progress-bar bg-green"
                  role="progressbar"
                  aria-valuenow="60"
                  aria-valuemin="0"
                  aria-valuemax="100"
                  style="width: 2%"
                >
                  <span class="sr-only">60% Complete</span>
                </div>
              </div>
            </div>
            <div class="w_right w_20">
              <span>1k</span>
            </div>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4 col-sm-4 col-xs-12">
      <div class="x_panel tile fixed_height_320 overflow_hidden">
        <div class="x_title">
          <h2>SR Summary</h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#">Settings 1</a></li>
                <li><a href="#">Settings 2</a></li>
              </ul>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <table class="" style="width: 100%">
            <tr>
              <th style="width: 37%">
                <p></p>
              </th>
              <th>
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                  <p class="">Device</p>
                </div>
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                  <p class="">Progress</p>
                </div>
              </th>
            </tr>
            <tr>
              <td>
                <canvas
                  class="canvasDoughnut1"
                  height="140"
                  width="140"
                  style="margin: 15px 10px 10px 0"
                ></canvas>
              </td>
              <td>
                <table class="tile_info">
                  <tr>
                    <td>
                      <p>
                        <i class="fa fa-square" style="color: #006a4e"></i
                        >Positive
                      </p>
                    </td>
                    <td>30%</td>
                  </tr>
                  <tr>
                    <td>
                      <p>
                        <i class="fa fa-square" style="color: green"></i
                        >Negative
                      </p>
                    </td>
                    <td>30%</td>
                  </tr>
                  <tr>
                    <td>
                      <p>
                        <i class="fa fa-square" style="color: lightgreen"></i
                        >Inactive
                      </p>
                    </td>
                    <td>30%</td>
                  </tr>
                  <tr>
                    <td>
                      <p>
                        <i class="fa fa-square" style="color: #8b0000"></i>Off
                      </p>
                    </td>
                    <td>10%</td>
                  </tr>
                  <tr>
                    <td>
                      <p>
                        <i class="fa fa-square" style="color: #a9a9a9"></i>Leave
                      </p>
                    </td>
                    <td>20%</td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-4 col-sm-4 col-xs-12">
      <div class="x_panel tile fixed_height_320 overflow_hidden">
        <div class="x_title">
          <h2>SR Summary</h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#">Settings 1</a></li>
                <li><a href="#">Settings 2</a></li>
              </ul>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <table class="" style="width: 100%">
            <tr>
              <th style="width: 37%">
                <p></p>
              </th>
              <th>
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                  <p class="">SR Type</p>
                </div>
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                  <p class="">Progress</p>
                </div>
              </th>
            </tr>
            <tr>
              <td>
                <canvas
                  class="canvasDoughnut2"
                  height="140"
                  width="140"
                  style="margin: 15px 10px 10px 0"
                ></canvas>
              </td>
              <td>
                <table class="tile_info">
                  <tr>
                    <td>
                      <p>
                        <i class="fa fa-square" style="color: #006a4e"></i
                        >Productive
                      </p>
                    </td>
                    <td>
                      <?php echo e($employee?(number_format(($employee[0]->pvSr*100/($employee[0]->onSr==0?1:$employee[0]->onSr)),2)):0); ?>%
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <p>
                        <i class="fa fa-square" style="color: #8b0000"></i
                        >Non-P.
                      </p>
                    </td>
                    <td>
                      <?php echo e($employee?(number_format(($employee[0]->nonProductiveSr*100/($employee[0]->onSr==0?1:$employee[0]->onSr)),2)):0); ?>%
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
  <!-- experimental start -->
  <div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>SR Summary<small></small></h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#">Settings 1</a>
                <a class="dropdown-item" href="#">Settings 2</a>
              </div>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <div id="echart_pie_t" style="height: 350px"></div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>SR Productivity<small></small></h2>
          <ul class="nav navbar-right panel_toolbox">
            <li>
              <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <li class="dropdown">
              <a
                href="#"
                class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false"
                ><i class="fa fa-wrench"></i
              ></a>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#">Settings 1</a>
                <a class="dropdown-item" href="#">Settings 2</a>
              </div>
            </li>
            <li>
              <a class="close-link"><i class="fa fa-close"></i></a>
            </li>
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <div id="echart_pie_t1" style="height: 350px"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- modal area start visit -->
  <div class="modal fade" id="myModalVisit" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">
            &times;
          </button>
          <h4 class="modal-title text-center">Category wise Visit</h4>
        </div>
        <div class="modal-body">
          <div
            class="loader"
            id="cat_out_load"
            style="display: none; margin-left: 35%"
          ></div>
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Category Name</th>
                <th>Total Outlet</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="myModalVisitBody"></tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- Visited outlet details -->
  <div class="modal fade" id="myModalVisitedOutlet" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">
            &times;
          </button>
          <h4 class="modal-title text-center">Visited Outlet Details</h4>
        </div>
        <div class="modal-body">
          <div
            class="loader"
            id="cat_out_load_details"
            style="display: none; margin-left: 35%"
          ></div>
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Outlet Code</th>
                <th>Outlet Name</th>
                <th>Outlet Mobile</th>
                <th>Outlet Address</th>
              </tr>
            </thead>
            <tbody id="myModalVisitedOutletBody"></tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
  <!--off sr modal area start -->
  <div class="modal fade" id="myModalOffSr" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">
            &times;
          </button>
          <h4 class="modal-title text-center">OFF SR List</h4>
        </div>
        <div class="modal-body">
          <div class="loader" style="display: none; margin-left: 35%"></div>
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Sl.</th>
                <th>Employee Name</th>
                <th>Staff ID</th>
                <th>Mobile</th>
              </tr>
            </thead>
            <tbody id="myModalOffSrBody"></tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>
<style>
  .shadowgen {
    background: #fff;
    border-radius: 2px;
    display: inline-block;
    height: 300px;
    margin: 1rem;
    position: relative;
    width: 300px;
    box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
  }
  .loader {
    border: 16px solid forestgreen; /* Light grey */
    border-top: 16px solid #3498db; /* Blue */
    border-radius: 50%;
    width: 100px;
    height: 100px;
    animation: spin 2s linear infinite;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }
</style>
<script>
  $('#SR').hide();
  $('#DSM').hide();
  $('#ITEM').hide();
  $('#OUTLET').hide();
  $('#bDSM').hide();
  $('#bSR').hide();
  $('#bITEM').hide();
  $('#bOUTLET').hide();

  window.onload=function(){
    doughnut();
    srProductivitydoughnut();
    drawGaugeChart();
    barChart();
   // chartPlot();
    lineChart();
    echart();
  }
  var data=<?php echo json_encode($data); ?>;
  var role=<?php echo json_encode($emp_role); ?>;
  if(role==2){
    $('#contributionDiv').hide();
  }
  //console.log(data);
  function doughnut(){

    if( typeof (Chart) === 'undefined'){ return; }
    var pieData=[];
    var pvN=[];
    var dt=<?php echo json_encode($employee); ?>;
    if(dt.length){
      pieData.push(dt[0]['pvSr']);
      pieData.push(dt[0]['nveSr']);
      pieData.push(dt[0]['inactSr']);
      pieData.push(dt[0]['offSr']);
      pieData.push(dt[0]['levSr']);
    }
    else{
      pieData.push(0);
      pieData.push(0);
      pieData.push(0);
      pieData.push(0);
      pieData.push(0);
    }
  if ($('.canvasDoughnut1').length){
    var chart_doughnut_settings = {
      type: 'doughnut',
      tooltipFillColor: "rgba(51, 51, 51, 0.55)",
      data: {
        labels: [
            "Positive",
            "Negative",
            "Inactive",
            "Off",
            "Leave",

          ],
          datasets: [{
            data: pieData,
            backgroundColor: [
            "#1ABB9C",
            "#F5F75A",
            "lightgreen",
            "#F9522D",
            "#A9A9A9"

            ],

          }],
      },
      options: {
        responsive: false,
        maintainAspectRatio:true,
        labels:false,
      }
      //showScale:false
    }
    $('.canvasDoughnut1').each(function(){

  				var chart_element = $(this);
  				var chart_doughnut = new Chart( chart_element, chart_doughnut_settings);

  			});
  }}

  //sr productivity doughnut chart function
  function srProductivitydoughnut(){
    if( typeof (Chart) === 'undefined'){ return; }
    if ($('.canvasDoughnut2').length){
    var chart_doughnut_settings = {
      type: 'doughnut',
      tooltipFillColor: "rgba(51, 51, 51, 0.55)",
      data: {
        labels: [
            "Productive",
            "Non-Productive",
          ],
          datasets: [{
            data: [<?php if($employee){echo $employee[0]->pvSr;}else{echo 0;}?>,
                    <?php if($employee){echo $employee[0]->nonProductiveSr;}else{echo 0;}?>
                  ],
            backgroundColor: [
            "#35A236",
            "#F9522D"

            ],

          }],
      },
      options: {
        responsive: false,
        maintainAspectRatio:true,
        labels:false,
      }
      //showScale:false
    }
    $('.canvasDoughnut2').each(function(){

          var chart_element = $(this);
          var chart_doughnut = new Chart( chart_element, chart_doughnut_settings);

        });
    }
  }

  function drawGaugeChart(){
    var opts = {
      lines: 12,
      angle: 0,
      lineWidth: 0.4,
      pointer: {
            length: 0.75,
            strokeWidth: 0.042,
            color: '#1D212A'
          },
      limitMax: false,
      limitMin: false,
      colorStart: '#6FADCF',
      colorStop: 'forestgreen',
      strokeColor: 'darkred',
      generateGradient: true,

    };
    if ($('#gauge_chart').length){

        var chart_gauge_01_elem = document.getElementById('gauge_chart');
        var gauge_chart = new Gauge(chart_gauge_01_elem).setOptions(opts);

      }
      if ($('#gauge-text').length){
        gauge_chart.maxValue =<?php if($employee){echo $employee[0]->actSr;}else{echo 0;}?>;
        gauge_chart.animationSpeed = 32;
        gauge_chart.setMinValue(0);
        gauge_chart.set(<?php if($employee){echo $employee[0]->pvSr;}else{echo 0;}?>);
        //gauge_chart.setTextField(document.getElementById("gauge-text"));

      }


  }
  //Outlet bar chart function
  function barChart() {
  			if( typeof (Morris) === 'undefined'){ return; }
  			if ($('#graph_bar1').length){
          var out_cov=[];
          for(let i=0;i<data.length;i++){
            out_cov.push({device:data[i]['total_visited'],geekbench:data[i]['date_day']});
          }
  				Morris.Bar({
  				  element: 'graph_bar1',
            data:out_cov,
  				  xkey: 'geekbench',
  				  ykeys: ['device'],
  				  labels: ['Visit'],
  				  barRatio: 0.5,
  				  barColors: ['#006A4E', '#3498DB'],
  				  xLabelAngle:45,
  				  hideHover: 'auto',
  				  resize: true
  				});

  			}
        if ($('#bar_graph_strike').length){
          var strike=[];
          for(let i=0;i<data.length;i++){
            strike.push({device:data[i]['strikeRate'],geekbench:data[i]['date_day']});
          }
  				Morris.Bar({
  				  element: 'bar_graph_strike',
            data:strike,
  				  xkey: 'geekbench',
  				  ykeys: ['device'],
  				  labels: ['Rate'],
  				  barRatio: 0.5,
  				  barColors: ['#FFB6C1', '#3498DB'],
  				  xLabelAngle:45,
  				  hideHover: 'auto',
  				  resize: true
  				});

  			}
        if ($('#bar_graph_lpc').length){
          var lpc=[];
          for(let i=0;i<data.length;i++){
            lpc.push({device:data[i]['lineParCall'],geekbench:data[i]['date_day']});
          }
  				Morris.Bar({
  				  element: 'bar_graph_lpc',
            data:lpc,
  				  xkey: 'geekbench',
  				  ykeys: ['device'],
  				  labels: ['LPC'],
  				  barRatio: 0.5,
  				  barColors: ['#678983', '#3498DB'],
  				  xLabelAngle:45,
  				  hideHover: 'auto',
  				  resize: true
  				});

  			}
        if ($('#bar_graph_positive').length){
          var pos=[];
          for(let i=0;i<data.length;i++){
            pos.push({device:data[i]['pvSr'],geekbench:data[i]['date_day']});
          }
  				Morris.Bar({
  				  element: 'bar_graph_positive',
            data:pos,
  				  xkey: 'geekbench',
  				  ykeys: ['device'],
  				  labels: ['LPC'],
  				  barRatio: 0.5,
  				  barColors: ['#766161', '#3498DB'],
  				  xLabelAngle:45,
  				  hideHover: 'auto',
  				  resize: true
  				});

  			}
        if ($('#bar_graph_tgOrd').length ){
              var tgOrd=[];
              for(let i=0;i<data.length;i++){
                tgOrd.push({period:data[i]['date_day'],target:(data[i]['totalTargetAmount']/26).toFixed(2),order:data[i]['totalOrderAmount'].toFixed(2)});
              }
            Morris.Bar({
              element: 'bar_graph_tgOrd',
              data:tgOrd,
              xkey: 'period',
              barColors: ['#26B99A', '#34495E', '#ACADAC', '#3498DB'],
              ykeys: ['target', 'order'],
              labels: ['Target', 'Order'],
              hideHover: 'auto',
              xLabelAngle: 60,
              resize: true
            });

          }
          if ($('#bar_graph_OrdDelv').length ){
              var ordDel=[];
              for(let i=0;i<data.length;i++){
                ordDel.push({period:data[i]['date_day'],delivery:(data[i]['mtd_total_delivery']/26).toFixed(2),order:data[i]['totalOrderAmount'].toFixed(2)});
              }
            Morris.Bar({
              element: 'bar_graph_OrdDelv',
              data:tgOrd,
              xkey: 'period',
              barColors: ['#26B99A', '#34495E', '#ACADAC', '#3498DB'],
              ykeys: ['order', 'delivery'],
              labels: ['Order', 'Delivery'],
              hideHover: 'auto',
              xLabelAngle: 60,
              barRatio: 0.8,
              resize: true
            });

          }
      }

  function lineChart(){
    if ($('#lineChartOutCov').length ){
        var label=[];
        var out_cov=[];
        for(let i=0;i<data.length;i++){
          label.push(data[i]['date_day']);
          out_cov.push(data[i]['total_visited']);
        }
        var ctx = document.getElementById("lineChartOutCov");
        var lineChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels:label,
          datasets: [{
          label: "Visit",
          backgroundColor: "rgba(38, 185, 154, 0.31)",
          borderColor: "rgba(38, 185, 154, 0.7)",
          pointBorderColor: "rgba(38, 185, 154, 0.7)",
          pointBackgroundColor: "rgba(38, 185, 154, 0.7)",
          pointHoverBackgroundColor: "#fff",
          pointHoverBorderColor: "rgba(220,220,220,1)",
          pointBorderWidth: 1,
          data:out_cov
          }]
        },
        });

    }

    if ($('#lineChartStrikeRate').length ){
        var label=[];
        var strike=[];
          for(let i=0;i<data.length;i++){
            label.push(data[i]['date_day']);
            strike.push(((data[i]['productiveMemo']/data[i]['total_visited'])*100).toFixed(2));
          }
        var ctx = document.getElementById("lineChartStrikeRate");
        var lineChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels:label,
          datasets: [{
          label: "Strike Rate",
          backgroundColor: "rgba(38, 185, 154, 0.31)",
          borderColor: "rgba(38, 185, 154, 0.7)",
          pointBorderColor: "rgba(38, 185, 154, 0.7)",
          pointBackgroundColor: "rgba(38, 185, 154, 0.7)",
          pointHoverBackgroundColor: "#fff",
          pointHoverBorderColor: "rgba(220,220,220,1)",
          pointBorderWidth: 1,
          data:strike
          }]
        },
        });

    }

    if ($('#lineChartLpc').length ){
        var label=[];
        var lpc=[];
          for(let i=0;i<data.length;i++){
            label.push(data[i]['date_day']);
            lpc.push(data[i]['lineParCall']);
          }
        var ctx = document.getElementById("lineChartLpc");
        var lineChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels:label,
          datasets: [{
          label: "Lpc",
          backgroundColor: "rgba(38, 185, 154, 0.31)",
          borderColor: "rgba(38, 185, 154, 0.7)",
          pointBorderColor: "rgba(38, 185, 154, 0.7)",
          pointBackgroundColor: "rgba(38, 185, 154, 0.7)",
          pointHoverBackgroundColor: "#fff",
          pointHoverBorderColor: "rgba(220,220,220,1)",
          pointBorderWidth: 1,
          data:lpc
          }]
        },
        });

    }

    if ($('#lineChartPositive').length ){
        var label=[];
        var pos=[];
          for(let i=0;i<data.length;i++){
            label.push(data[i]['date_day']);
            pos.push(data[i]['pvSr']);
          }
        var ctx = document.getElementById("lineChartPositive");
        var lineChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels:label,
          datasets: [{
          label: "Positive",
          backgroundColor: "rgba(38, 185, 154, 0.31)",
          borderColor: "rgba(38, 185, 154, 0.7)",
          pointBorderColor: "rgba(38, 185, 154, 0.7)",
          pointBackgroundColor: "rgba(38, 185, 154, 0.7)",
          pointHoverBackgroundColor: "#fff",
          pointHoverBorderColor: "rgba(220,220,220,1)",
          pointBorderWidth: 1,
          data:pos
          }]
        },
        });

    }
    if ($('#tgOrder').length ){
        var label=[];
        var target=[];
        var order=[];
          for(let i=0;i<data.length;i++){
            label.push(data[i]['date_day']);
            target.push((data[i]['totalTargetAmount']/26).toFixed(2));
            order.push((data[i]['totalOrderAmount']).toFixed(2));
          }
        var ctx = document.getElementById("tgOrder");
        var lineChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels:label,
          datasets: [{
          label: "Target",
          backgroundColor: "rgba(38, 185, 154, 0.31)",
          borderColor: "rgba(38, 185, 154, 0.7)",
          pointBorderColor: "rgba(38, 185, 154, 0.7)",
          pointBackgroundColor: "rgba(38, 185, 154, 0.7)",
          pointHoverBackgroundColor: "#fff",
          pointHoverBorderColor: "rgba(220,220,220,1)",
          pointBorderWidth: 1,
          data:target
          },{
          label: "Order",
          backgroundColor: "rgba(38, 185, 154, 0.31)",
          borderColor: "rgba(38, 185, 154, 0.7)",
          pointBorderColor: "rgba(38, 185, 154, 0.7)",
          pointBackgroundColor: "rgba(38, 185, 154, 0.7)",
          pointHoverBackgroundColor: "#fff",
          pointHoverBorderColor: "rgba(220,220,220,1)",
          pointBorderWidth: 1,
          data:order
          }]
        },
        });

    }
    if ($('#ordDelivery').length ){
        var label=[];
        var delivery=[];
        var order=[];
          for(let i=0;i<data.length;i++){
            label.push(data[i]['date_day']);
            delivery.push((data[i]['mtd_total_delivery']/26).toFixed(2));
            order.push((data[i]['totalOrderAmount']).toFixed(2));
          }
        var ctx = document.getElementById("ordDelivery");
        var lineChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels:label,
          datasets: [{
          label: "Order",
          backgroundColor: "rgba(38, 185, 154, 0.31)",
          borderColor: "rgba(38, 185, 154, 0.7)",
          pointBorderColor: "rgba(38, 185, 154, 0.7)",
          pointBackgroundColor: "rgba(38, 185, 154, 0.7)",
          pointHoverBackgroundColor: "#fff",
          pointHoverBorderColor: "rgba(220,220,220,1)",
          pointBorderWidth: 1,
          data:order
          },{
          label: "Delivery",
          backgroundColor: "rgba(38, 185, 154, 0.31)",
          borderColor: "rgba(38, 185, 154, 0.7)",
          pointBorderColor: "rgba(38, 185, 154, 0.7)",
          pointBackgroundColor: "rgba(38, 185, 154, 0.7)",
          pointHoverBackgroundColor: "#fff",
          pointHoverBorderColor: "rgba(220,220,220,1)",
          pointBorderWidth: 1,
          data:delivery
          }]
        },
        });

    }
  }
  function getTop(type) {
      var token = $("#_token").val();
      var emid = $("#emid").val();
      if(type !=''){
      $('#top_load').css("display", "block");
      $.ajax({
          type: "POST",
          url: "<?php echo e(URL::to('/')); ?>/load/top10/data",
          data: {
              type: type,
              emid:emid,
              _token:token
          },
          cache: false,
          dataType: "json",

          success: function (data) {
            $('#top_load').css("display", "none");
            console.log(data);
            var html = '';
            var count = 1;
            $("#topData").empty();
            if(type=="SR" || type=="TSM"){
              $('#DSM').hide();
              $('#ITEM').hide();
              $('#OUTLET').hide();
              $('#SR').show();
              for (var i = 0; i < data.length; i++) {
                  html += '<tr>' +
                  '<td>' + data[i]['aemp_usnm'] +" - " + data[i]['aemp_name'] + '</td>' +
                  '<td>' + data[i]['zone_name'] + '</td>' +
                  '<td>' + data[i]['aemp_mob1'] + '</td>' +
                  '<td>' + data[i]['amnt'].toFixed(2) + '</td>' +
                  '</tr>';
              }
            }
            else if(type=="DSM"){
              $('#ITEM').hide();
              $('#OUTLET').hide();
              $('#SR').hide();
              $('#DSM').show();
              for (var i = 0; i < data.length; i++) {
                  html += '<tr>' +
                  '<td>' + data[i]['aemp_usnm'] +" - " + data[i]['aemp_name'] + '</td>' +
                  '<td>' + data[i]['aemp_mob1'] + '</td>' +
                  '<td>' + data[i]['amnt'].toFixed(2) + '</td>' +
                  '</tr>';
              }
            }
            else if(type=="CLASS"){
              $('#DSM').hide();
              $('#OUTLET').hide();
              $('#SR').hide();
              $('#ITEM').show();
              for (var i = 0; i < data.length; i++) {
                  html += '<tr>' +
                  '<td>' + count+ '</td>' +
                  '<td>' + data[i]['itcl_code'] +" - " + data[i]['itcl_name'] + '</td>' +
                  '<td>' + data[i]['amnt'].toFixed(2) + '</td>' +
                  '</tr>';
                  count++;
              }
            }
            else if(type=="ITEM"){
              $('#DSM').hide();
              $('#OUTLET').hide();
              $('#SR').hide();
              $('#ITEM').show();
              for (var i = 0; i < data.length; i++) {
                  html += '<tr>' +
                  '<td>' + count+ '</td>' +
                  '<td>' + data[i]['amim_code'] +" - " + data[i]['amim_name'] + '</td>' +
                  '<td>' + data[i]['amnt'].toFixed(2) + '</td>' +
                  '</tr>';
                  count++;
              }
            }
            else if(type=="OUTLET"){
              $('#DSM').hide();
              $('#SR').hide();
              $('#ITEM').hide();
              $('#OUTLET').show();
              for (var i = 0; i < data.length; i++) {
                  html += '<tr>' +
                  '<td>' + data[i]['site_code'] +" - " + data[i]['site_name'] + '</td>' +
                  '<td>' + data[i]['site_mob1'] + '</td>' +
                  '<td>' + data[i]['zone_name'] + '</td>' +
                  '<td>' + data[i]['amnt'].toFixed(2) + '</td>' +
                  '</tr>';
                  count++;
              }
            }
            $("#topData").append(html);

          },error: function(error) {
               console.log(error);
           }
      });
      }else{
        $('#DSM').hide();
        $('#SR').hide();
        $('#ITEM').hide();
        $('#OUTLET').hide();
        $("#topData").empty();
      }
    }
  function getBottom(type) {
      var token = $("#_token").val();
      var emid = $("#emid").val();
      if(type !=''){
      $('#btm_load').css("display", "block");
      $.ajax({
          type: "POST",
          url: "<?php echo e(URL::to('/')); ?>/load/bottom10/data",
          data: {
              type: type,
              emid:emid,
              _token:token
          },
          cache: false,
          dataType: "json",

          success: function (data) {
            $('#btm_load').css("display", "none");
            console.log(data);
            var html = '';
            var count = 1;
            $("#bottomData").empty();
            if(type=="SR" || type=="TSM"){
              $('#bDSM').hide();
              $('#bITEM').hide();
              $('#bOUTLET').hide();
              $('#bSR').show();
              for (var i = 0; i < data.length; i++) {
                  html += '<tr>' +
                  '<td>' + data[i]['aemp_usnm'] +" - " + data[i]['aemp_name'] + '</td>' +
                  '<td>' + data[i]['zone_name'] + '</td>' +
                  '<td>' + data[i]['aemp_mob1'] + '</td>' +
                  '<td>' + data[i]['amnt'].toFixed(2) + '</td>' +
                  '</tr>';
              }
            }
            else if(type=="DSM"){
              $('#bITEM').hide();
              $('#bOUTLET').hide();
              $('#bSR').hide();
              $('#bDSM').show();
              for (var i = 0; i < data.length; i++) {
                  html += '<tr>' +
                  '<td>' + data[i]['aemp_usnm'] +" - " + data[i]['aemp_name'] + '</td>' +
                  '<td>' + data[i]['aemp_mob1'] + '</td>' +
                  '<td>' + data[i]['amnt'].toFixed(2) + '</td>' +
                  '</tr>';
              }
            }
            else if(type=="CLASS"){
              $('#bDSM').hide();
              $('#bOUTLET').hide();
              $('#bSR').hide();
              $('#bITEM').show();
              for (var i = 0; i < data.length; i++) {
                  html += '<tr>' +
                  '<td>' + count+ '</td>' +
                  '<td>' + data[i]['itcl_code'] +" - " + data[i]['itcl_name'] + '</td>' +
                  '<td>' + data[i]['amnt'].toFixed(2) + '</td>' +
                  '</tr>';
                  count++;
              }
            }
            else if(type=="ITEM"){
              $('#bDSM').hide();
              $('#bOUTLET').hide();
              $('#bSR').hide();
              $('#bITEM').show();
              for (var i = 0; i < data.length; i++) {
                  html += '<tr>' +
                  '<td>' + count+ '</td>' +
                  '<td>' + data[i]['amim_code'] +" - " + data[i]['amim_name'] + '</td>' +
                  '<td>' + data[i]['amnt'].toFixed(2) + '</td>' +
                  '</tr>';
                  count++;
              }
            }
            else if(type=="OUTLET"){
              $('#bDSM').hide();
              $('#bSR').hide();
              $('#bITEM').hide();
              $('#bOUTLET').show();
              for (var i = 0; i < data.length; i++) {
                  html += '<tr>' +
                  '<td>' + data[i]['site_code'] +" - " + data[i]['site_name'] + '</td>' +
                  '<td>' + data[i]['site_mob1'] + '</td>' +
                  '<td>' + data[i]['zone_name'] + '</td>' +
                  '<td>' + data[i]['amnt'].toFixed(2) + '</td>' +
                  '</tr>';
                  count++;
              }
            }
            $("#bottomData").append(html);

          },error: function(error) {
               console.log(error);
           }
      });
      }
      else{
        $('#bDSM').hide();
        $('#bSR').hide();
        $('#bITEM').hide();
        $('#bOUTLET').hide();
        $("#bottomData").empty();
      }
    }

  function echart(){
    var pieData=[];
    var pvN=[];
    var outStat=[];
    var dt=<?php echo json_encode($employee); ?>;
    console.log(dt);
    if(dt.length){
      pieData.push(dt[0]['pvSr']);
      pieData.push(dt[0]['nveSr']);
      pieData.push(dt[0]['inactSr']);
      pieData.push(dt[0]['offSr']);
      pieData.push(dt[0]['levSr']);
      pvN.push(dt[0]['pvSr']);
      pvN.push(dt[0]['nonProductiveSr']);
      outStat.push(dt[0]['AMSR'])
      outStat.push(dt[0]['AVSR'])
      outStat.push(dt[0]['avgOutSR'])
      outStat.push(dt[0]['APEOIT'])
      outStat.push(dt[0]['APESR'])
    }
    else{
      pieData.push(0);
      pieData.push(0);
      pieData.push(0);
      pieData.push(0);
      pieData.push(0);
      pvN.push(0);
      pvN.push(0);
      outStat.push(0)
      outStat.push(0)
      outStat.push(0)
      outStat.push(0)
      outStat.push(0)
    }

    var design = {
  				  color: [
  					  '#006A4E', '#34495E', '#BDC3C7', '#3498DB',
  					  '#9B59B6', '#8abb6f', '#759c6a', '#bfd3b7'
  				  ],

  				  title: {
  					  itemGap: 8,
  					  textStyle: {
  						  fontWeight: 'normal',
  						  color: '#408829'
  					  }
  				  },

  				  dataRange: {
  					  color: ['#1f610a', '#97b58d']
  				  },

  				  toolbox: {
  					  color: ['#408829', '#408829', '#408829', '#408829']
  				  },

  				  tooltip: {
  					  backgroundColor: 'rgba(0,0,0,0.5)',
  					  axisPointer: {
  						  type: 'line',
  						  lineStyle: {
  							  color: '#408829',
  							  type: 'dashed'
  						  },
  						  crossStyle: {
  							  color: '#408829'
  						  },
  						  shadowStyle: {
  							  color: 'rgba(200,200,200,0.3)'
  						  }
  					  }
  				  },

  				  dataZoom: {
  					  dataBackgroundColor: '#eee',
  					  fillerColor: 'rgba(64,136,41,0.2)',
  					  handleColor: '#408829'
  				  },
  				  grid: {
  					  borderWidth: 0
  				  },

  				  categoryAxis: {
  					  axisLine: {
  						  lineStyle: {
  							  color: '#408829'
  						  }
  					  },
  					  splitLine: {
  						  lineStyle: {
  							  color: ['#eee']
  						  }
  					  }
  				  },

  				  valueAxis: {
  					  axisLine: {
  						  lineStyle: {
  							  color: '#408829'
  						  }
  					  },
  					  splitArea: {
  						  show: true,
  						  areaStyle: {
  							  color: ['rgba(250,250,250,0.1)', 'rgba(200,200,200,0.1)']
  						  }
  					  },
  					  splitLine: {
  						  lineStyle: {
  							  color: ['#eee']
  						  }
  					  }
  				  },
  				  timeline: {
  					  lineStyle: {
  						  color: '#408829'
  					  },
  					  controlStyle: {
  						  normal: {color: '#408829'},
  						  emphasis: {color: '#408829'}
  					  }
  				  },

  				  k: {
  					  itemStyle: {
  						  normal: {
  							  color: '#68a54a',
  							  color0: '#a9cba2',
  							  lineStyle: {
  								  width: 1,
  								  color: '#408829',
  								  color0: '#86b379'
  							  }
  						  }
  					  }
  				  },
  				  map: {
  					  itemStyle: {
  						  normal: {
  							  areaStyle: {
  								  color: '#ddd'
  							  },
  							  label: {
  								  textStyle: {
  									  color: '#c12e34'
  								  }
  							  }
  						  },
  						  emphasis: {
  							  areaStyle: {
  								  color: '#99d2dd'
  							  },
  							  label: {
  								  textStyle: {
  									  color: '#c12e34'
  								  }
  							  }
  						  }
  					  }
  				  },
  				  force: {
  					  itemStyle: {
  						  normal: {
  							  linkStyle: {
  								  strokeColor: '#408829'
  							  }
  						  }
  					  }
  				  },
  				  chord: {
  					  padding: 4,
  					  itemStyle: {
  						  normal: {
  							  lineStyle: {
  								  width: 1,
  								  color: 'rgba(128, 128, 128, 0.5)'
  							  },
  							  chordStyle: {
  								  lineStyle: {
  									  width: 1,
  									  color: 'rgba(128, 128, 128, 0.5)'
  								  }
  							  }
  						  },
  						  emphasis: {
  							  lineStyle: {
  								  width: 1,
  								  color: 'rgba(128, 128, 128, 0.5)'
  							  },
  							  chordStyle: {
  								  lineStyle: {
  									  width: 1,
  									  color: 'rgba(128, 128, 128, 0.5)'
  								  }
  							  }
  						  }
  					  }
  				  },
  				  gauge: {
  					  startAngle: 225,
  					  endAngle: -45,
  					  axisLine: {
  						  show: true,
  						  lineStyle: {
  							  color: [[0.2, '#86b379'], [0.8, '#68a54a'], [1, '#408829']],
  							  width: 8
  						  }
  					  },
  					  axisTick: {
  						  splitNumber: 10,
  						  length: 12,
  						  lineStyle: {
  							  color: 'auto'
  						  }
  					  },
  					  axisLabel: {
  						  textStyle: {
  							  color: 'auto'
  						  }
  					  },
  					  splitLine: {
  						  length: 18,
  						  lineStyle: {
  							  color: 'auto'
  						  }
  					  },
  					  pointer: {
  						  length: '90%',
  						  color: 'auto'
  					  },
  					  title: {
  						  textStyle: {
  							  color: '#333'
  						  }
  					  },
  					  detail: {
  						  textStyle: {
  							  color: 'auto'
  						  }
  					  }
  				  },
  				  textStyle: {
  					  fontFamily: 'Arial, Verdana, sans-serif'
  				  }
  	};

    if ($('#echart_pie_t').length ){
  			  var echartPieCollapse = echarts.init(document.getElementById('echart_pie_t'), design);
  			echartPieCollapse.setOption({
  				tooltip: {
  				  trigger: 'item',
  				  formatter: "{a} <br/>{b} : {c} ({d}%)"
  				},
  				legend: {
  				  x: 'center',
  				  y: 'bottom',
  				  data: ['Positive', 'Negative', 'Inactive', 'Off', 'Leave']
  				},
  				toolbox: {
  				  show: true,
  				  feature: {
  					magicType: {
  					  show: true,
  					  type: ['pie', 'funnel']
  					},
  					restore: {
  					  show: false,
  					  //title: "Restore"
  					},
  					saveAsImage: {
  					  show: false,
  					 // title: "Save Image"
  					}
  				  }
  				},
  				calculable: true,
  				series: [{
  				  name: 'Area Mode',
  				  type: 'pie',
  				  radius: [25, 90],
  				  center: ['50%', 170],
  				  roseType: 'area',
  				  x: '50%',
  				  max: 40,
  				  sort: 'ascending',
  				  data: [{
  					value: pieData[0],
  					name: 'Positive'
  				  }, {
  					value:pieData[1],
  					name: 'Negative'
  				  }, {
  					value:pieData[2],
  					name: 'Inactive'
  				  }, {
  					value:pieData[3],
  					name: 'Off'
  				  }, {
  					value:pieData[4],
  					name: 'Leave'
  				  }]
  				}]
  			  });

  	}
    if ($('#echart_pie_t1').length ){
  			  var echartPieCollapse = echarts.init(document.getElementById('echart_pie_t1'), design);
  			echartPieCollapse.setOption({
  				tooltip: {
  				  trigger: 'item',
  				  formatter: "{a} <br/>{b} : {c} ({d}%)"
  				},
  				legend: {
  				  x: 'center',
  				  y: 'bottom',
  				  data: ['Productive', 'Non Productive']
  				},
  				toolbox: {
  				  show: true,
  				  feature: {
  					magicType: {
  					  show: true,
  					  type: ['pie', 'funnel']
  					},
  					restore: {
  					  show: false,
  					  //title: "Restore"
  					},
  					saveAsImage: {
  					  show: false,
  					 // title: "Save Image"
  					}
  				  }
  				},
  				calculable: true,
  				series: [{
  				  name: 'Area Mode',
  				  type: 'pie',
  				  radius: [25, 90],
  				  center: ['50%', 170],
  				  roseType: 'area',
  				  x: '50%',
  				  max: 40,
  				  sort: 'ascending',
  				  data: [{
  					value: pvN[0],
  					name: 'Positive'
  				  }, {
  					value:pvN[1],
  					name: 'Non Productive'
  				  }]
  				}]
  			  });

  	}
    if ($('#echart_pie_out_stat').length ){
  			  var echartPieCollapse = echarts.init(document.getElementById('echart_pie_out_stat'), design);
  			echartPieCollapse.setOption({
  				tooltip: {
  				  trigger: 'item',
  				  formatter: "{a} <br/>{b} : {c} ({d}%)"
  				},
  				legend: {
  				  x: 'center',
  				  y: 'bottom',
  				  data: ['Ord/SR', 'A.V/SR','A.Olt/SR','A.PE/Olt','A.PE/SR']
  				},
  				toolbox: {
  				  show: true,
  				  feature: {
  					magicType: {
  					  show: true,
  					  type: ['pie', 'funnel']
  					},
  					restore: {
  					  show: false,
  					  //title: "Restore"
  					},
  					saveAsImage: {
  					  show: false,
  					 // title: "Save Image"
  					}
  				  }
  				},
  				calculable: true,
  				series: [{
  				  name: 'Area Mode',
  				  type: 'pie',
  				  radius: [25, 90],
  				  center: ['50%', 170],
  				  roseType: 'area',
  				  x: '50%',
  				  max: 40,
  				  sort: 'ascending',
  				  data: [{
  					value: outStat[0],
  					name: 'Ord/SR'
  				  }, {
  					value:outStat[1],
  					name: 'A.V/SR'
  				  },
            {
  					value: outStat[2],
  					name: 'A.Olt/SR'
  				  }, {
  					value:outStat[3],
  					name: 'A.PE/Olt'
  				  },
            {
  					value:outStat[4],
  					name: 'A.PE/SR'
  				  }
            ]
  				}]
  			  });

  	}
    if ($('#visit_vs_memo_bar').length ){
          var visit=[];
          var memo=[];
          var labels=[];
          console.log(data);
          for(let i=0;i<data.length;i++){
            //out_cov.push({device:data[i]['total_visited'],geekbench:data[i]['date_day']});
            labels.push(data[i]['date_day']);
            visit.push(data[i]['total_visited']);
            memo.push(data[i]['productiveMemo']);
          }
          var echartBar = echarts.init(document.getElementById('visit_vs_memo_bar'), design);
          echartBar.setOption({
          title: {
            text: '',
            subtext: ''
          },
          tooltip: {
            trigger: 'axis'
          },
          legend: {
            data: ['Visit', 'Order']
          },
          toolbox: {
            show: false
          },
          calculable: false,
          xAxis: [{
            type: 'category',
            data:labels
          }],
          yAxis: [{
            type: 'value'
          }],
          series: [{
            name: 'Visit',
            type: 'bar',
            data:visit,
            markPoint: {
            data: [{
              type: 'max',
              name: 'Maximum'
            }, {
              type: 'min',
              name: 'Lowest'
            }]
            },
            markLine: {
            data: [{
              type: 'average',
              name: 'Average'
            }]
            }
          }, {
            name: 'Order',
            type: 'bar',
            data:memo,
            markPoint: {
            data: [{
              name: 'Visit',
              value: 182.2,
              xAxis: 7,
              yAxis: 183,
            }, {
              name: 'Order',
              value: 2.3,
              xAxis: 11,
              yAxis: 3
            }]
            },
            markLine: {
            data: [{
              type: 'average',
              name: 'Average'
            }]
            }
          }]
          });

    }

    if ($('#tgt_vs_achv_bar').length ){
      var tgt=[];
      var achv=[];
      var labels=[];
      //labels=class name

      if(role==2){
        var pillar_data=<?php echo json_encode($pillar_data)?>;
        console.log(pillar_data);
        if(pillar_data){
          var itm_class=<?php echo json_encode($gp_wise_class)?>;
          console.log(itm_class);
          for(var i=0;i<itm_class.length;i++){
            if(itm_class[i]['itcl_name'] ==''){
               labels.push('N/S');
            }else{
              labels.push(itm_class[i]['itcl_name']);
            }
          }
          labels.push('Other');
          tgt.push(pillar_data[0]['plv1']);
          tgt.push(pillar_data[0]['plv2']);
          tgt.push(pillar_data[0]['plv3']);
          tgt.push(pillar_data[0]['plv4']);
          tgt.push(pillar_data[0]['plv5']);
          tgt.push(pillar_data[0]['plv6']);
          tgt.push(pillar_data[0]['plv7']);
          tgt.push(pillar_data[0]['plv8']);
          tgt.push(pillar_data[0]['plv9']);
          tgt.push(pillar_data[0]['other']);

          achv.push(pillar_data[0]['aplv1']);
          achv.push(pillar_data[0]['aplv2']);
          achv.push(pillar_data[0]['aplv3']);
          achv.push(pillar_data[0]['aplv4']);
          achv.push(pillar_data[0]['aplv5']);
          achv.push(pillar_data[0]['aplv6']);
          achv.push(pillar_data[0]['aplv7']);
          achv.push(pillar_data[0]['aplv8']);
          achv.push(pillar_data[0]['aplv9']);
          achv.push(pillar_data[0]['aother']);
          var echartBar = echarts.init(document.getElementById('tgt_vs_achv_bar'), tgt_achv);

          echartBar.setOption({
          title: {
            text: '',
            subtext: ''
          },
          tooltip: {
            trigger: 'axis'
          },
          legend: {
            data: ['Target', 'Achieve']
          },
          toolbox: {
            show: false
          },
          calculable: false,
          xAxis: [{
            type: 'category',
            data:labels
          }],
          yAxis: [{
            type: 'value'
          }],
          series: [{
            name: 'Target',
            type: 'bar',
            data:tgt,
            color:['#8b0000','#228B22'],
            markPoint: {
            data: [{
              type: 'max',
              name: 'Maximum'
            }, {
              type: 'min',
              name: 'Lowest'
            }]
            },
            markLine: {
            data: [{
              type: 'average',
              name: 'Average'
            }]
            }
          }, {
            name: 'Achieve',
            type: 'bar',
            data:achv,
            markPoint: {
            data: [{
              name: 'Achieve',
              value: 182.2,
              xAxis: 7,
              yAxis: 183,
            }, {
              name: 'Achieve',
              value: 2.3,
              xAxis: 11,
              yAxis: 3
            }]
            },
            markLine: {
            data: [{
              type: 'average',
              name: 'Average'
            }]
            }
          }]
          });
        }else{

        }

      }

  }


  }

  function loadUnderEmployee(f_id) {
      var _token = $("#_token").val();
      var emid = $("#emid").val();
      console.log(emid);
      $.ajax({
          type: "POST",
          url: "<?php echo e(URL::to('/')); ?>/load_under_employee",
          data: {
              _token: _token,
             	emid:emid,
          },
          cache: false,
          success: function (data) {
          console.log(data);
          $('#app_all_db').empty();
          $('#app_all_db').append(data);
          },
          error:function(error){
          	console.log(error);
          }
      });
  }
</script>
<script>
  var tgt_achv = {
    color: [
      "#8b0000",
      "#228B22",
      "#BDC3C7",
      "#3498DB",
      "#9B59B6",
      "#8abb6f",
      "#759c6a",
      "#bfd3b7",
    ],

    title: {
      itemGap: 8,
      textStyle: {
        fontWeight: "normal",
        color: "#408829",
      },
    },

    dataRange: {
      color: ["#1f610a", "#97b58d"],
    },

    toolbox: {
      color: ["#8b0000", "#228B22", "#408829", "#408829", "#408829"],
    },

    tooltip: {
      backgroundColor: "rgba(0,0,0,0.5)",
      axisPointer: {
        type: "line",
        lineStyle: {
          color: "#408829",
          type: "dashed",
        },
        crossStyle: {
          color: "#408829",
        },
        shadowStyle: {
          color: "rgba(200,200,200,0.3)",
        },
      },
    },

    dataZoom: {
      dataBackgroundColor: "#eee",
      fillerColor: "rgba(64,136,41,0.2)",
      handleColor: "#408829",
    },
    grid: {
      borderWidth: 0,
    },

    categoryAxis: {
      axisLine: {
        lineStyle: {
          color: "#408829",
        },
      },
      splitLine: {
        lineStyle: {
          color: ["#eee"],
        },
      },
    },

    valueAxis: {
      axisLine: {
        lineStyle: {
          color: "#408829",
        },
      },
      splitArea: {
        show: true,
        areaStyle: {
          color: ["rgba(250,250,250,0.1)", "rgba(200,200,200,0.1)"],
        },
      },
      splitLine: {
        lineStyle: {
          color: ["#eee"],
        },
      },
    },
    timeline: {
      lineStyle: {
        color: "#408829",
      },
      controlStyle: {
        normal: { color: "#408829" },
        emphasis: { color: "#408829" },
      },
    },

    k: {
      itemStyle: {
        normal: {
          color: "#68a54a",
          color0: "#a9cba2",
          lineStyle: {
            width: 1,
            color: "#408829",
            color0: "#86b379",
          },
        },
      },
    },
    map: {
      itemStyle: {
        normal: {
          areaStyle: {
            color: "#ddd",
          },
          label: {
            textStyle: {
              color: "#c12e34",
            },
          },
        },
        emphasis: {
          areaStyle: {
            color: "#99d2dd",
          },
          label: {
            textStyle: {
              color: "#c12e34",
            },
          },
        },
      },
    },
    force: {
      itemStyle: {
        normal: {
          linkStyle: {
            strokeColor: "#408829",
          },
        },
      },
    },
    chord: {
      padding: 4,
      itemStyle: {
        normal: {
          lineStyle: {
            width: 1,
            color: "rgba(128, 128, 128, 0.5)",
          },
          chordStyle: {
            lineStyle: {
              width: 1,
              color: "rgba(128, 128, 128, 0.5)",
            },
          },
        },
        emphasis: {
          lineStyle: {
            width: 1,
            color: "rgba(128, 128, 128, 0.5)",
          },
          chordStyle: {
            lineStyle: {
              width: 1,
              color: "rgba(128, 128, 128, 0.5)",
            },
          },
        },
      },
    },
    gauge: {
      startAngle: 225,
      endAngle: -45,
      axisLine: {
        show: true,
        lineStyle: {
          color: [
            [0.2, "#86b379"],
            [0.8, "#68a54a"],
            [1, "#408829"],
          ],
          width: 8,
        },
      },
      axisTick: {
        splitNumber: 10,
        length: 12,
        lineStyle: {
          color: "auto",
        },
      },
      axisLabel: {
        textStyle: {
          color: "auto",
        },
      },
      splitLine: {
        length: 18,
        lineStyle: {
          color: "auto",
        },
      },
      pointer: {
        length: "90%",
        color: "auto",
      },
      title: {
        textStyle: {
          color: "#333",
        },
      },
      detail: {
        textStyle: {
          color: "auto",
        },
      },
    },
    textStyle: {
      fontFamily: "Arial, Verdana, sans-serif",
    },
  };
</script>
<script>
  function getOffSRList() {
    var emid = $("#emid").val();
    $(".loader").show();
    $.ajax({
      type: "get",
      url: "<?php echo e(URL::to('/')); ?>/getOffSRList/" + emid,
      dataType: "json",
      success: function (data) {
        $(".loader").hide();
        console.log(data);
        var html = "";
        var count = 1;
        for (var i = 0; i < data.length; i++) {
          html +=
            "<tr><td>" +
            count +
            "</td>" +
            "<td>" +
            data[i]["aemp_name"] +
            "</td>" +
            "<td>" +
            data[i]["aemp_usnm"] +
            "</td>" +
            "<td>" +
            data[i]["aemp_mob1"] +
            "</td></tr>";
          count++;
        }
        if (data.length == 0) {
          html +=
            "<tr><td>Off SR List Will be shown Between HOS and TSM Level</td></tr>";
        }
        $("#myModalOffSrBody").empty();
        $("#myModalOffSrBody").append(html);
      },
      error: function (error) {
        console.log(error);
      },
    });
  }
  function getCatWiseOutVisit() {
    var emid = $("#emid").val();
    $("#cat_out_load").show();
    $.ajax({
      type: "get",
      url: "<?php echo e(URL::to('/')); ?>/getCatWiseOutVisit/" + emid,
      dataType: "json",
      success: function (data) {
        $("#cat_out_load").hide();
        console.log(data);
        var html = "";
        for (var i = 0; i < data.length; i++) {
          html +=
            "<tr><td>" +
            data[i]["otcg_name"] +
            "</td>" +
            "<td>" +
            data[i]["num"] +
            "</td>" +
            '<td><i id="show" class="fa fa-info-circle fa-2x "  data-toggle="modal" data-target="#myModalVisitedOutlet" style="cursor:pointer; color:forestgreen;" onclick="getVisitedOutletDetails(' +
            emid +
            "," +
            data[i]["id"] +
            ')"></i></td>' +
            "</tr>";
        }
        $("#myModalVisitBody").empty();
        $("#myModalVisitBody").append(html);
        $("#myModalVisit").modal("show");
      },
      error: function (error) {
        console.log(error);
      },
    });
  }
  function getVisitedOutletDetails(emid, cat_id) {
    $("#cat_out_load_details").show();
    $.ajax({
      type: "get",
      url:
        "<?php echo e(URL::to('/')); ?>/getVisitedOutletDetailsDashboard/" +
        emid +
        "/" +
        cat_id,
      dataType: "json",
      success: function (data) {
        $("#cat_out_load_details").hide();
        $("#myModalVisitedOutlet").modal({ backdrop: false });
        console.log(data);
        var html = "";
        for (var i = 0; i < data.length; i++) {
          html +=
            "<tr><td>" +
            data[i]["site_code"] +
            "</td>" +
            "<td>" +
            data[i]["site_name"] +
            "</td>" +
            "<td>" +
            data[i]["site_mob1"] +
            "</td>" +
            "<td>" +
            data[i]["site_adrs"] +
            "</td>" +
            "</tr>";
        }
        // $('.modal-backdrop ').removeClass('modal-backdrop');
        $("#myModalVisitedOutletBody").empty();
        $("#myModalVisitedOutletBody").append(html);
        $("#myModalVisitedOutlet").modal("show");
      },
      error: function (error) {
        console.log(error);
      },
    });
  }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/project_board.blade.php ENDPATH**/ ?>