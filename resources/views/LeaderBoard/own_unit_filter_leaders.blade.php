@extends('theme.app')
@section('content')
<div class="right_col" role="main">
	<div class="row" id="selection-process">
	    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
		<div class="col-md-3 col-sm-3 col-xs-12">
			<select class="form-control" name="acmp_id[]" id="acmp_id"
                    onchange="getGroup()" multiple="multiple">
                <option value="">Select Company</option>
				@foreach($acmp as $cmp)
                <option value="{{$cmp->id}}">{{$cmp->acmp_name}}</option>
				@endforeach
                
            </select>
		</div>
		<div class="col-md-3 col-sm-3 col-xs-12">
			<select class="form-control" name="slgpid[]" id="slgp_id"
                    onchange=""  multiple="multiple">
                <option value="">Select Group</option>
            </select>
		</div>
		<div class="col-md-3 col-sm-3 col-xs-12">
			<select class="form-control" name="zone_id[]" id="zone_id"
                    onchange="" multiple="multiple">
                <option value="">Select Zone</option>
                @foreach($zone as $z)
                <option value="{{$z->id}}">{{$z->zone_name}}</option>
				@endforeach
            </select>
		</div>
		<div class="col-md-3 col-sm-3 col-xs-12" style="height:39px;">
			<button class="btn btn-dark btn-block" onclick="getLeaders()">Show</button>
		</div>
		
	</div>
<div id="result-div"></div>
	
</div>
<script type="text/javascript">
	$('#acmp_id').select2();
	$('#slgp_id').select2();
	$('#zone_id').select2();
	$('#result-div').hide();

	function getGroup() {
        var acmp_id=$('#acmp_id').val();
        var _token = $("#_token").val();
		console.log('HI');
        //$('#ajax_load').css("display", "block");
        $.ajax({
            type: "POST",
            url: "{{ URL::to('/')}}/load/leader_board/getGroup",
            data: {
                acmp_id: acmp_id,
                _token: _token
            },
            cache: false,
            dataType: "json",
            success: function (data) {
            console.log(data);
              $("#slgp_id").empty();
              $('#ajax_load').css("display", "none");
              var html = '<option value="">Select group</option>';
              for (var i = 0; i < data.length; i++) {
                  console.log(data[i]);
                  html += '<option value="' + data[i].id + '">'+ data[i].slgp_name + '</option>';
              }
              $("#slgp_id").append(html);
            },
            error:function (error) {
              console.log(error);
            }
        });
    }
	function getLeaders() {
        var acmp_id=$('#acmp_id').val();
		var slgp_id=$('#slgp_id').val();
		var zone_id=$('#zone_id').val();
        var _token = $("#_token").val();
        var selector= jQuery('#toggleBtn').is(':checked')?1:0;
        $('#ajax_load').css("display", "block");
       
        if(acmp_id !=null || slgp_id !=null || zone_id !=null){
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/filter/ownUnit/getLeaders",
                data: {
                    acmp_id: acmp_id,
                    slgp_id:slgp_id,
                    zone_id:zone_id,
                    selector:selector,
                    _token: _token
                },
                cache: false,         
                success: function (data) {
                $('#ajax_load').css("display", "none");
                console.log(data);
                $('#result-div').empty();
                $('#result-div').show();
                $('#result-div').append(data);
                
                },
                error:function (error) {
                $('#ajax_load').css("display", "none");
                Swal.fire({
                    title:"Something went wrong!!!"
                });
                console.log(error);
                }
            });
        }
        else{
            $('#ajax_load').css("display", "none");
            Swal.fire({
                title:"Please do select..."
            });
        }
    }	
</script>
@endsection