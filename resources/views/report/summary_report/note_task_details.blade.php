@extends('theme.app')
@section('content')
<div class="right_col" role="main">
<div class="container">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            @foreach($data as $dt)
            <div class="col-md-6 col-sm-6 col-xs-12 single_note_task_div">
               <div class="col-md-8 col-sm-8 col-xs-12">
                   <h5 >Title:  {{$dt->note_titl}}</h5><br>
                   <p>Message:  {{$dt->note_body}}</p><br>
                    <p ><b style="float:left;font-style:italic;">Outlet Name:  {{$dt->site_name}}</b><span style="float:right;font-style:italic;">Date:   {{$dt->note_date}}</span></p>
               </div>
               <div class="col-md-2 col-sm-2 col-xs-12 col-md-offset-2 col-sm-offset-2">
                   <img src="https://images.sihirbox.com/{{$dt->nimg_imag}}" alt="Note Image" style="border-radius: 8px;height:120px;margin:5px;">
                   
               </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

</div>
<style>
.single_note_task_div{
width:100%;
-webkit-box-shadow: 1px 1px 5px 2px rgba(0,0,0,0.21); 
box-shadow: 1px 1px 5px 2px rgba(0,0,0,0.21);
margin-bottom:15px;
}

</style>
@endsection
