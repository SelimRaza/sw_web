const emp_head='<th>Sl</th>'+
'<th>Emp Name</th>'+
'<th>Staff Id</th>'+
'<th>Mobile</th>'+
'<th>Zone</th>'+
'<th>Total SR</th>'+
'<th>Present SR</th>'+
'<th>Absence SR</th>'+
'<th>Leave SR</th>';
function getEmpAttendanceReport(emid){

	hideReport();
	hide_me();
	addClass();
	$('#title_head').show();
	$('#tracing').show();
	$('#desig').empty();
	$('#emid_div').empty();
	var emid_cnt='<input type="hidden" value="'+emid+'" id="emid">';
	var date='';
	var _token = $("#_token").val();
	var c_date=new Date();
	var file_date=c_date.getFullYear()+'_'+(c_date.getMonth()+1)+'_'+c_date.getDate();
	$('#emid_div').append(emid_cnt);
	$('#employee_sales_traking_report_slgp').removeAttr('onclick');
	$('#period').removeAttr('onchange');
	$('#sh_select_date').hide();
	$('#employee_sales_traking_report_slgp').attr('onclick','exportTableToCSV("employee_attendance_report_'+file_date+'.csv","tableDiv_traking")');
	$('#period').attr('onchange','getDateWiseEmpAttendanceReport(this.value)');
	$.ajax({
		type:"POST",
		url:"/getEmpAttendanceReport",
		data:{
			emid:emid,
			date:date,
			_token:_token,
		},
		cache: false,
		dataType: "json",
		success: function (data) {
			//$('#tableDiv_traking').show();
			//$('#head_tracking_dev_note_task').empty();
			
			//$('#head_tracking_dev_note_task').append(head);
			console.log(data.role_id);
			var html="";
			var count=1;
			var dp_cnt='';
			var t_sr=0;
			var p_sr=0;
			var a_sr=0;
			var l_sr=0;
			for (var i = 0; i < data.un_emp.length; i++) {
				var color='';
				if(data.un_emp[i]['offSr']>0){
					color="red;"
				}
				t_sr=t_sr+data.un_emp[i]['totalSr'];
				p_sr=p_sr+data.un_emp[i]['onSr'];
				a_sr=a_sr+data.un_emp[i]['offSr'];
				l_sr=l_sr+data.un_emp[i]['lvSr'];
				html += '<tr>' +
					'<td>' + count + '</td>' +
					'<td>' + data.un_emp[i]['aemp_name']+ '</td>' +
					'<td>' + data.un_emp[i]['aemp_usnm']+ '</td>' +
					'<td>' + data.un_emp[i]['aemp_mob1'] + '</td>' +
					'<td>' + data.un_emp[i]['zone_name'] + '</td>' +
					'<td>' + data.un_emp[i]['totalSr'] + '</td>' +
					'<td>' + data.un_emp[i]['onSr'] + '</td>' +
					'<td style="color:'+color+'">' + data.un_emp[i]['offSr'] + '</td>' +
					'<td>' + data.un_emp[i]['lvSr'] + '</td>' +
					'</tr>';
				dp_cnt+='<li><a href="#" onclick="empClickAttendance(this)" role_id="'+data.un_emp[i]['role_id']+'" emid="'+data.un_emp[i]['id']+'" role_name="'+data.un_emp[i]['aemp_name']+'">'+' '+data.un_emp[i]['aemp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
				count++;
			}
			if(data.role_id<=5 && data.role_id>=2){
			html+='<tr><td colspan="8"></td></tr><tr><td>GT</td><td>Grand Total</td><td></td><td></td><td></td>'+
					'<td>'+t_sr+'<i class="fa fa-cloud-download fa-2x pull-right" aria-hidden="true" style="color:forestgreen;cursor:pointer;" onclick="expEmpAttendanceData('+emid+',1)"></i></td>'+
					'<td>'+p_sr+'<i class="fa fa-cloud-download fa-2x pull-right" aria-hidden="true" style="color:forestgreen;cursor:pointer;" onclick="expEmpAttendanceData('+emid+',2)"></i></td>'+
					'<td>'+a_sr+'<i class="fa fa-cloud-download fa-2x pull-right" aria-hidden="true" style="color:forestgreen;cursor:pointer;" onclick="expEmpAttendanceData('+emid+',3)"></i></td>'+
					'<td>'+l_sr+'<i class="fa fa-cloud-download fa-2x pull-right" aria-hidden="true" style="color:forestgreen;cursor:pointer;" onclick="expEmpAttendanceData('+emid+',4)"></i></td></tr>';
			}
			else{
				html+='<tr><td colspan="8"></td></tr><tr><td>GT</td><td>Grand Total</td><td></td><td></td><td></td>'+
					'<td>'+t_sr+'</td>'+
					'<td>'+p_sr+'</td>'+
					'<td>'+a_sr+'</td>'+
					'<td>'+l_sr+'</td>'+
					'</tr>';
			}
			emptyContentAndAppendDataTrack1(emp_head,html);
			//$('#cont_traking').empty();
			$('#all_dp_content').empty();
			//$('#cont_traking').append(html);
			$('#all_dp_content').append(dp_cnt);
			$('#rpt').height($("#tableDiv_traking").height()+150);
		},error:function(error){
			console.log(error);
			$('#ajax_load').css("display", "none");
			Swal.fire({
				icon: 'error',
				title: 'Oops...',
				text: 'Something went wrong!',
			})
		}
	});
	
}

function empClickAttendance(v){
	var emid=$(v).attr('emid');
	var role_name=$(v).attr('role_name');
	var role_id=$(v).attr('role_id');
	v.removeAttribute('onclick');
	v.setAttribute('onclick','empAddRemoveAttendance(this)');
	console.log(v);
	var html=role_name+" >  ";
	var period=$('#period').val();
	var _token = $("#_token").val();
	$('#desig').append(v);
	$('#emid_div').empty();
	var emid_cnt='<input type="hidden" value="'+emid+'" id="emid" role_id="'+role_id+'">';
	$('#emid_div').append(emid_cnt);
	$('#ajax_load').css("display", "block");
	$.ajax({
		type:"POST",
		url:"/getEmpAttendanceReport",
		data:{
			_token:_token,
			emid:emid,
			date:period
		},
		dataType: "json",
		success: function (data){
			$('#ajax_load').css("display", "none");
			//$('#tableDiv_traking').show();
			var html="";
			var count=1;
			var dp_cnt='';
			var t_sr=0;
			var p_sr=0;
			var a_sr=0;
			var l_sr=0;
			for (var i = 0; i < data.un_emp.length; i++) {
				var color='';
				if(data.un_emp[i]['offSr']>0){
					color="red;"
				}
				t_sr=t_sr+data.un_emp[i]['totalSr'];
				p_sr=p_sr+data.un_emp[i]['onSr'];
				a_sr=a_sr+data.un_emp[i]['offSr'];
				l_sr=l_sr+data.un_emp[i]['lvSr'];
				html += '<tr>' +
					'<td>' + count + '</td>' +
					'<td>' + data.un_emp[i]['aemp_name']+ '</td>' +
					'<td>' + data.un_emp[i]['aemp_usnm']+ '</td>' +
					'<td>' + data.un_emp[i]['aemp_mob1'] + '</td>' +
					'<td>' + data.un_emp[i]['zone_name'] + '</td>' +
					'<td>' + data.un_emp[i]['totalSr'] + '</td>' +
					'<td>' + data.un_emp[i]['onSr'] + '</td>' +
					'<td style="color:'+color+'">' + data.un_emp[i]['offSr'] + '</td>' +
					'<td>' + data.un_emp[i]['lvSr'] + '</td>' +
					'</tr>';
				dp_cnt+='<li><a href="#" onclick="empClickAttendance(this)" role_id="'+data.un_emp[i]['role_id']+'" emid="'+data.un_emp[i]['id']+'" role_name="'+data.un_emp[i]['aemp_name']+'">'+' '+data.un_emp[i]['aemp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
				count++;
			}
			if(role_id<=5 && role_id>=2){
			html+='<tr><td colspan="8"></td></tr><tr><td>GT</td><td>Grand Total</td><td></td><td></td><td></td>'+
					'<td>'+t_sr+'<i class="fa fa-cloud-download fa-2x pull-right" aria-hidden="true" style="color:forestgreen;cursor:pointer;" onclick="expEmpAttendanceData('+emid+',1)"></i></td>'+
					'<td>'+p_sr+'<i class="fa fa-cloud-download fa-2x pull-right" aria-hidden="true" style="color:forestgreen;cursor:pointer;" onclick="expEmpAttendanceData('+emid+',2)"></i></td>'+
					'<td>'+a_sr+'<i class="fa fa-cloud-download fa-2x pull-right" aria-hidden="true" style="color:forestgreen;cursor:pointer;" onclick="expEmpAttendanceData('+emid+',3)"></i></td>'+
					'<td>'+l_sr+'<i class="fa fa-cloud-download fa-2x pull-right" aria-hidden="true" style="color:forestgreen;cursor:pointer;" onclick="expEmpAttendanceData('+emid+',4)"></i></td></tr>';
			}
			else{
				html+='<tr><td colspan="8"></td></tr><tr><td>GT</td><td>Grand Total</td><td></td><td></td><td></td>'+
					'<td>'+t_sr+'</td>'+
					'<td>'+p_sr+'</td>'+
					'<td>'+a_sr+'</td>'+
					'<td>'+l_sr+'</td>'+
					'</tr>';
			}
			emptyContentAndAppendDataTrack1(emp_head,html);
			//$('#cont_traking').empty();
			$('#all_dp_content').empty();
			//$('#cont_traking').append(html);
			$('#all_dp_content').append(dp_cnt);
			$('#rpt').height($("#tableDiv_traking").height()+150);
		},error:function(error){
			$('#ajax_load').css("display", "none");
			Swal.fire({
				icon: 'error',
				title: 'Oops...',
				text: 'Something went wrong!',
			})
			console.log(error);
		}
	});
}

function empAddRemoveAttendance(elem){
	$(elem).nextAll().remove();
	var emid=$(elem).attr('emid');
	$('#emid_div').empty();
	var emid_cnt='<input type="hidden" value="'+emid+'" id="emid">';
	$('#emid_div').append(emid_cnt);
	var emid=$(elem).attr('emid');
	var role_id=$(elem).attr('role_id');
	var _token = $("#_token").val();
	var period=$('#period').val();
	$.ajax({
		type:"POST",
		url:"/getEmpAttendanceReport",
		data:{
			_token:_token,
			emid:emid,
			date:period
		},
		dataType: "json",
		success: function (data){
			$('#ajax_load').css("display", "none");
			//$('#tableDiv_traking').show();
			var html="";
			var count=1;
			var dp_cnt='';
			var t_sr=0;
			var p_sr=0;
			var a_sr=0;
			var l_sr=0;
			for (var i = 0; i < data.un_emp.length; i++) {
				var color='';
				if(data.un_emp[i]['offSr']>0){
					color="red;"
				}
				t_sr=t_sr+data.un_emp[i]['totalSr'];
				p_sr=p_sr+data.un_emp[i]['onSr'];
				a_sr=a_sr+data.un_emp[i]['offSr'];
				l_sr=l_sr+data.un_emp[i]['lvSr'];
				html += '<tr>' +
					'<td>' + count + '</td>' +
					'<td>' + data.un_emp[i]['aemp_name']+ '</td>' +
					'<td>' + data.un_emp[i]['aemp_usnm']+ '</td>' +
					'<td>' + data.un_emp[i]['aemp_mob1'] + '</td>' +
					'<td>' + data.un_emp[i]['zone_name'] + '</td>' +
					'<td>' + data.un_emp[i]['totalSr'] + '</td>' +
					'<td>' + data.un_emp[i]['onSr'] + '</td>' +
					'<td style="color:'+color+'">' + data.un_emp[i]['offSr'] + '</td>' +
					'<td>' + data.un_emp[i]['lvSr'] + '</td>' +
					'</tr>';
				dp_cnt+='<li><a href="#" onclick="empClickAttendance(this)" role_id="'+data.un_emp[i]['role_id']+'" emid="'+data.un_emp[i]['id']+'" role_name="'+data.un_emp[i]['aemp_name']+'">'+' '+data.un_emp[i]['aemp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
				count++;
			}
			if(role_id<=5 && role_id>=2){
			html+='<tr><td colspan="8"></td></tr><tr><td>GT</td><td>Grand Total</td><td></td><td></td><td></td>'+
					'<td>'+t_sr+'<i class="fa fa-cloud-download fa-2x pull-right" aria-hidden="true" style="color:forestgreen;cursor:pointer;" onclick="expEmpAttendanceData('+emid+',1)"></i></td>'+
					'<td>'+p_sr+'<i class="fa fa-cloud-download fa-2x pull-right" aria-hidden="true" style="color:forestgreen;cursor:pointer;" onclick="expEmpAttendanceData('+emid+',2)"></i></td>'+
					'<td>'+a_sr+'<i class="fa fa-cloud-download fa-2x pull-right" aria-hidden="true" style="color:forestgreen;cursor:pointer;" onclick="expEmpAttendanceData('+emid+',3)"></i></td>'+
					'<td>'+l_sr+'<i class="fa fa-cloud-download fa-2x pull-right" aria-hidden="true" style="color:forestgreen;cursor:pointer;" onclick="expEmpAttendanceData('+emid+',4)"></i></td></tr>';
			}
			else{
				html+='<tr><td colspan="8"></td></tr><tr><td>GT</td><td>Grand Total</td><td></td><td></td><td></td>'+
					'<td>'+t_sr+'</td>'+
					'<td>'+p_sr+'</td>'+
					'<td>'+a_sr+'</td>'+
					'<td>'+l_sr+'</td>'+
					'</tr>';
			}
			emptyContentAndAppendDataTrack1(emp_head,html);
			//$('#cont_traking').empty();
			$('#all_dp_content').empty();
			//$('#cont_traking').append(html);
			$('#all_dp_content').append(dp_cnt);
			$('#rpt').height($("#tableDiv_traking").height()+150);
		},error:function(error){
			$('#ajax_load').css("display", "none");
			Swal.fire({
				icon: 'error',
				title: 'Oops...',
				text: 'Something went wrong!',
			})
			console.log(error);
		}
	});
}

function getDateWiseEmpAttendanceReport(date){
	var emid=$('#emid').val();
	var role_id=$('#emid').attr('role_id');
	var _token = $("#_token").val();
	$('#ajax_load').css("display", "block");
	$.ajax({
		type:"POST",
		url:"/getEmpAttendanceReport",
		data:{
			_token:_token,
			emid:emid,
			date:date
		},
		dataType: "json",
		success: function (data){
			$('#ajax_load').css("display", "none");
			//$('#tableDiv_traking').show();
			var html="";
			var count=1;
			var dp_cnt='';
			var t_sr=0;
			var p_sr=0;
			var a_sr=0;
			var l_sr=0;
			for (var i = 0; i < data.un_emp.length; i++) {
				var color='';
				if(data.un_emp[i]['offSr']>0){
					color="red;"
				}
				t_sr=t_sr+data.un_emp[i]['totalSr'];
				p_sr=p_sr+data.un_emp[i]['onSr'];
				a_sr=a_sr+data.un_emp[i]['offSr'];
				l_sr=l_sr+data.un_emp[i]['lvSr'];
				html += '<tr>' +
					'<td>' + count + '</td>' +
					'<td>' + data.un_emp[i]['aemp_name']+ '</td>' +
					'<td>' + data.un_emp[i]['aemp_usnm']+ '</td>' +
					'<td>' + data.un_emp[i]['aemp_mob1'] + '</td>' +
					'<td>' + data.un_emp[i]['zone_name'] + '</td>' +
					'<td>' + data.un_emp[i]['totalSr'] + '</td>' +
					'<td>' + data.un_emp[i]['onSr'] + '</td>' +
					'<td style="color:'+color+'">' + data.un_emp[i]['offSr'] + '</td>' +
					'<td>' + data.un_emp[i]['lvSr'] + '</td>' +
					'</tr>';
				dp_cnt+='<li><a href="#" onclick="empClickAttendance(this)" role_id="'+data.un_emp[i]['role_id']+'" emid="'+data.un_emp[i]['id']+'" role_name="'+data.un_emp[i]['aemp_name']+'">'+' '+data.un_emp[i]['aemp_name']+' ⥤&nbsp;&nbsp; '+'</a></li>';
				count++;
			}
			if(role_id<=5 && role_id>=2){
			html+='<tr><td colspan="8"></td></tr><tr><td>GT</td><td>Grand Total</td><td></td><td></td><td></td>'+
					'<td>'+t_sr+'<i class="fa fa-cloud-download fa-2x pull-right" aria-hidden="true" style="color:forestgreen;cursor:pointer;" onclick="expEmpAttendanceData('+emid+',1)"></i></td>'+
					'<td>'+p_sr+'<i class="fa fa-cloud-download fa-2x pull-right" aria-hidden="true" style="color:forestgreen;cursor:pointer;" onclick="expEmpAttendanceData('+emid+',2)"></i></td>'+
					'<td>'+a_sr+'<i class="fa fa-cloud-download fa-2x pull-right" aria-hidden="true" style="color:forestgreen;cursor:pointer;" onclick="expEmpAttendanceData('+emid+',3)"></i></td>'+
					'<td>'+l_sr+'<i class="fa fa-cloud-download fa-2x pull-right" aria-hidden="true" style="color:forestgreen;cursor:pointer;" onclick="expEmpAttendanceData('+emid+',4)"></i></td></tr>';
			}
			else{
				html+='<tr><td colspan="8"></td></tr><tr><td>GT</td><td>Grand Total</td><td></td><td></td><td></td>'+
					'<td>'+t_sr+'</td>'+
					'<td>'+p_sr+'</td>'+
					'<td>'+a_sr+'</td>'+
					'<td>'+l_sr+'</td>'+
					'</tr>';
			}
			emptyContentAndAppendDataTrack1(emp_head,html);
			//$('#cont_traking').empty();
			$('#all_dp_content').empty();
			//$('#cont_traking').append(html);
			$('#all_dp_content').append(dp_cnt);
			$('#rpt').height($("#tableDiv_traking").height()+150);
		},error:function(error){
			$('#ajax_load').css("display", "none");
			Swal.fire({
				icon: 'error',
				title: 'Oops...',
				text: 'Something went wrong!',
			})
			console.log(error);
		}
	});
}
//Type Definition
//1 for total_Sr
//2 for present_Sr
//3 for off_Sr
//4 for leave Sr

function expEmpAttendanceData(emid,type){
	var _token = $("#_token").val();
	var date = $("#period").val();
	$.ajax({
		type:"POST",
		url:"/export_emp_attendance_data",
		data:{
			_token:_token,
			emid:emid,
			date:date,
			type:type
		},
		//responseType: "json",
		xhrFields:{
            responseType: 'blob'
        },
		success: function (data){
			console.log(data);
			var link = document.createElement('a');
            link.href = window.URL.createObjectURL(data);
			if(type==1){
				link.download = `total_sr_list.xlsx`;
			}
			else if(type==2){
				link.download = `present_sr_report.xlsx`;
			}
			else if(type==3){
				link.download = `off_sr_report.xlsx`;
			}
			else{
				link.download = `leave_sr_report.xlsx`;
			}
           
            link.click();
			Swal.fire({
				text: 'Thanks!',
			});
		},error:function(error){
			console.log(error);
			
		}
	});
}

function getRequestedReportList(){
	var _token = $("#_token").val();
	hide_me();
//	hideReport();
	$.ajax({
		type:"POST",
		url:"/getGeneratedReportList",
		data:{
			_token:_token,
		},
		dataType:"json",
		cache: false,
		success:function(data){
			console.log(data);
			var html='';
			var count=1;
			var stat='';
			$('#tblDiv_requested_report_body').empty();
			$('#tblDiv_requested_report').show();
			for (var i = 0; i < data.length; i++) {
				
				var color='';
				if(data[i]['report_status']==1 || data[i]['report_status']==10){
					color="red;"
					stat='Pending'
				}
				else if(data[i]['report_status']==4){
					color="yellow;"
					stat='Running'
				}
				else if(data[i]['report_status']==2){
					color="forestgreen";
					stat='Ready'
				}
				else if(data[i]['report_status']==3){
					color="olive";
					stat='Delivered'
				}
				var file="http://coreapi.sihirfms.com/reports/"+data[i]['report_link'];
				html += '<tr>' +
					'<td>' + count + '</td>' +
					'<td>' + data[i]['report_name']+ '</td>' +
					'<td style=""><a href="'+file+'" style="text-decoration:underline;color:blue;">'+file+ '</a></td>' +
					'<td>' + data[i]['created_at'] + '</td>'+
					'<td>' + data[i]['aemp_email'] + '</td>'+
					'<td style="color:'+color+'!important;">' + stat + '</td>';
					
				count++;
			}
			$('#tblDiv_requested_report_body').append(html);
		},error:function(error){
			 console.log(error);
		}

	})
}