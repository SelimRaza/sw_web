@extends('theme.app_menu')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="clearfix"></div>
            <div class="row">
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <strong></strong>{{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert-danger">
                        <strong></strong>{{ Session::get('danger') }}
                    </div>
                @endif

                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_content">
                         @auth
                            @foreach($menus as $item)
                                    <div class="col-md-2">
                                        <div class="menu_div">
                                        <i class="{{$item->wmnu_icon}}"></i>
                                            <a  href="#" style="font-size:18px!important;" onclick="openModal('{{ $item->get_user_submenu() }}','{{ $item->wmnu_name }}');"> {{ $item->wmnu_name}} <span
                                                    class=""></span></a>
                                        </div>
                                        
                                    </div>
                            @endforeach
                        @endauth
                            
                        </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_content">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Modal Title</h2>
            <div id="modal-data">
                <!-- Data will be displayed here -->
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalSubmen" role="dialog">
        <div class="modal-dialog" style="width:50%;">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center" id="wmnu_name"></h4>
                </div>
                <div class="modal-body">
                    <div id="modalSubmen_body"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

</div>
<style>
/* Style the modal */


/* Rest of your CSS styles */
/* ... */


</style>
<script>
 function openModal1(data) {
    const modal = document.getElementById("myModal");
    const modalData = document.getElementById("modal-data");
    let html = '<ul class="nav child_menu">';
    var data =JSON.parse(data);
    for (let i = 0; i < data.length; i++) {
        var url = `{{ URL::to('/') }}/${data[i].wsmn_wurl}`;
        html += `<li><a href="${url}">${data[i].wsmn_name}</a></li>`;
    }
    html += '</ul>';
    $('#modal-data').html(html);
    modal.style.display = "block";
}
function openModal(data,menu_name) {
    $("#modalSubmen").modal({backdrop: false});
    $('#modalSubmen').modal('show');
    $('#wmnu_name').html(menu_name);
    let html = '';
    var data =JSON.parse(data);
    for (let i = 0; i < data.length; i++) {
        var url = `{{ URL::to('/') }}/${data[i].wsmn_wurl}`;
        html += `<ol><img src="{{asset('/theme/arrow.png')}}"/> <a href="${url}" target="_blank"> ${data[i].wsmn_name}</a></ol>`;
    }
    html += '';
    console.log(html)
    $('#modalSubmen_body').html(html);
}




</script>
@endsection
