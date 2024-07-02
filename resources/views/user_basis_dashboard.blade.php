<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12  ">
        <div class="x_panel">
          <div class="x_title">
            <h2>Select Employee To View Progress<small></small></h2>
           
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <table class="table" style="border:none!important;">
              <thead>
              
                <th>Name</th>
                <th>Outlet</th>
                <th>Visit</th>
                <th>Order</th>
                <th>Exp</th>
              
              <thead>
              <tbody>
              @foreach($un_emp as $emp)
              <tr></tr>
                <tr style="border-radius: 10px;box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);margin-top:10px!important;margin-bottom:10px!important;">
                <td><a href="{{route('get_user_basis_data',$emp->id)}}">{{$emp->role_name."--".$emp->aemp_name}}</a></td>
                <td>{{$emp->dhbd_tsit}}</td>
                <td>{{$emp->dhbd_tvit}}</td>
                <td>{{$emp->dhbd_memo}}</td>
                <td>{{round($emp->dhbd_tamt/1000,2)}}</td>
              </tr>
              <tr><td></td><td></td><td></td><td></td><td></td></tr>
              @endforeach
              <tbody>
            </table>
          </div>
        </div>
    </div>
</div>