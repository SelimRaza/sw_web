 <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12  ">
        <div class="x_panel">
          <div class="x_title">
            <h2>Select Employee To View Progress<small></small></h2>
           
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
              @foreach($un_emp as $emp)
                <p><a href="{{route('get_user_basis_data',$emp->id)}}">{{$emp->role_name."--".$emp->aemp_name}}</a></p>
              @endforeach
          </div>
        </div>
    </div>
</div>