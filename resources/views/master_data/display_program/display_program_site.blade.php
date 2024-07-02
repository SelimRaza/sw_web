@extends('theme.app')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li>
                            <a href="{{ URL::to('/display-program')}}">All Display Program</a>
                        </li>
                        <li class="active">
                            <strong>Display Program</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <strong>Success!</strong>{{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert-danger">
                        <strong>Danger! </strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Display Program</h1>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                       aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#">Settings 1</a>
                                        </li>
                                        <li><a href="#">Settings 2</a>
                                        </li>
                                    </ul>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Program Name
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                           data-validate-length-range="6" data-validate-words="2" name="name"
                                           value="{{$displayProgram->name}}"
                                           placeholder="Name" required="required" type="text">
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Program Code
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                           data-validate-length-range="6" data-validate-words="2" name="code"
                                           value="{{$displayProgram->code}}"
                                           placeholder="Code" required="required" type="text">
                                </div>

                            </div>

                            <form style="display:inline"
                                  action="{{ URL::to('display-program/siteAssignAdd/'.$displayProgram->id)}}"
                                  class="pull-xs-right5 card-link" method="GET">
                                {{csrf_field()}}
                                {{method_field('DELETE')}}

                                <table class="table table-striped projects">
                                    <thead>
                                    <tr>
                                        <th>
                                            Outlet
                                        </th>
                                        <th>
                                            Width
                                        </th>
                                        <th>
                                            Height
                                        </th>
                                        <th>
                                            Number Of Block
                                        </th>
                                        <th>
                                            Action
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>

                                        <td>
                                            <select class="form-control" name="site_id" id="site_id"
                                                    required>
                                                <option value="">Select</option>
                                                @foreach ($sites as $site)
                                                    <option value="{{ $site->id }}">{{ $site->name.' ('.$site->code.')' }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="any" class="form-control" name="width"
                                                   id="width"
                                                   value=""/>
                                        </td>
                                        <td>
                                            <input type="number" step="any" class="form-control"
                                                   name="height"
                                                   id="height"
                                                   value=""/>
                                        </td>
                                        <td>
                                            <input type="number" step="any" class="form-control"
                                                   name="block_count"
                                                   id="block_count"
                                                   value=""/>
                                        </td>
                                        <td>
                                            <input class="btn btn-success btn-xs" type="submit"
                                                   value="Save"
                                                   onclick="">
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                                </input>
                            </form>

                            <table class="table table-striped projects">
                                <thead>
                                <tr>
                                    <th>
                                       Outlet
                                    </th>
                                    <th>
                                        Width
                                    </th>
                                    <th>
                                        Height
                                    </th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($displayProgramSites as $displayProgramSite)
                                        <tr>
                                            <td>{{$displayProgramSite->site()->name.'('.$displayProgramSite->site()->code.')'}}</td>
                                            <td>{{$displayProgramSite->width}}</td>
                                            <td>{{$displayProgramSite->height}}</td>
                                            <td>
                                                <form style="display:inline"
                                                      action="{{ URL::to('display-program/siteInactive/'.$displayProgramSite->id)}}"
                                                      class="pull-xs-right5 card-link" method="GET">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="<?php echo $displayProgramSite->status_id == 1 ? 'Acvive' : 'Inactive'?>"
                                                           onclick="return ConfirmDelete()">
                                                    </input>
                                                </form>

                                            </td>
                                        </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $("#site_id").select2({width: 'resolve'});
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
@endsection