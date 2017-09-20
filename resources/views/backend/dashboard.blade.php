@extends('backend.layouts.app')

@section('after-styles')
{{ Html::style('css/highcharts.css') }}
{{ Html::style('css/dashboard.css') }}
@endsection

@section('page-header')
    <h1>
        {{ app_name() }}
        <small>{{ trans('strings.backend.dashboard.title') }}</small>
    </h1>
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">POS MONTHLY SALES</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div><!-- /.box tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            <div id="posChart"></div>
        </div><!-- /.box-body -->
    </div><!--box box-success-->

    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">COMMISSARY MONTHLY SALES</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div><!-- /.box tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            <div id="commissaryChart"></div>
        </div><!-- /.box-body -->
    </div><!--box box-success-->

    <div class="col-lg-6">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Top Products for {{ date('F') }}</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div><!-- /.box tools -->
            </div><!-- /.box-header -->
            <div class="box-body">
                <ul class="list-group">
                    @if(count($tops))
                        @foreach($tops as $product)
                            <li class="list-group-item">
                                {{ $product->name }}   
                                <span class="label label-primary pull-right">
                                    {{ $product->count }}
                                </span>                             
                            </li>
                        @endforeach
                    @else
                        <li class="list-group-item">
                            No product to be list.                              
                        </li>
                    @endif
                </ul>
            </div><!-- /.box-body -->
        </div><!--box box-success-->
    </div>

    <div class="col-lg-6">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Request</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                @if(count($requests))
                    @foreach($requests as $request)
                        <li class="list-group-item">
                            {{ $request->title }}&nbsp;
                            <button class="btn btn-xs btn-primary pull-right" data-id="{{ $request->id }}" onclick="getRequest(this)">View</button>
                        </li>
                    @endforeach
                    <li class="list-group-item" style="text-align: center">
                        <a href="#" onclick="viewAll()">View All</a>
                    </li>
                @else
                <li class="list-group-item">No Request</li>
                @endif
            </div>
        </div>
    </div>

    <div id="requestModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="request_from">Request</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="request_title">Title:</label>
                        <input type="text" class="form-control" name="request_title" id="request_title" style="background:white" readonly>
                    </div> 
                    <div class="form-group">
                        <label for="request_ingredient">Message:</label>
                        <textarea class="form-control" name="request_msg" id="request_msg" rows='6' style="background:white" readonly></textarea>
                    </div>
                    <div class="form-group">
                        <label for="request_qty">Request Quantity:</label>
                        <input type="text" class="form-control" id="request_qty" style="background:white" readonly>
                    </div>    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btn_make_response" onclick="createResponse()">Response</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="responseModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create Response</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="request_title">Request From:</label>
                        <input type="text" class="form-control" id="response_request_from" style="background:white" readonly>
                    </div> 
                    <div class="form-group">
                        <label for="response_msg">Message:</label>
                        <textarea class="form-control" name="response_msg" id="response_msg" rows='6'></textarea>
                    </div>   
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="submitResponse(this)">Accept</button>
                    <button type="button" class="btn btn-danger" onclick="submitResponse(this)">Decline</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="requestAllModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="request_from">Request List</h4>
                </div>
                <div class="modal-body">
                    <div id="table-wrapper">
                        <div id="table-scroll">
                            <table class="table table-responsive table-bordered" id="tbl_request">
                                <thead>
                                    <th>TITLE</th>
                                    <th>DATE</th>
                                    <th>FROM</th>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('after-scripts')
{{ Html::script('js/highcharts.js') }}
<script type="text/javascript">
    var request_id = 0;
    var request_from = '';

    Highcharts.chart('posChart', {

        title: {
            text: ' '
        },

        subtitle: {
            text: ''
        },
        xAxis: {
            categories: [
                @foreach($monthNames as $name)
                    '{{ $name }}',
                @endforeach
            ]
        },
        yAxis: {
            title: {
                text: 'Value'
            }
        },
        tooltip: {
            valueSuffix: ' PHP'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },

        plotOptions: {
            lineWidth: 1,
                shadow: true,
        },

        series: [{
            name: 'SALES',
            data: [
                @foreach($months as $month)
                {{ $month }},
                @endforeach
            ]
        }]

    });


    Highcharts.chart('commissaryChart', {

        title: {
            text: ' '
        },

        subtitle: {
            text: ''
        },
        xAxis: {
            categories: [
                @foreach($monthNames as $name)
                    '{{ $name }}',
                @endforeach
            ]
        },
        yAxis: {
            title: {
                text: 'Value'
            }
        },
        tooltip: {
            valueSuffix: ' PHP'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },

        plotOptions: {
            lineWidth: 1,
                shadow: true,
        },

        series: [{
            name: 'SALES',
            data: [
                @foreach($commissaries as $commissary)
                {{ $commissary }},
                @endforeach
            ]
        }]

    });

    $('.highcharts-credits').hide();

    function getRequest(e)
    {
        var id  = $(e).attr('data-id');
        var url = "{{ URL::to('admin/dashboard/request') }}/" + id;

        $.ajax({
            url: url,
            type: 'get',
            success: function(data){
                $('.modal').modal('hide');

                if(typeof data == 'object')
                {
                    request_id    = data[0]['id'];
                    request_from  = data[1]['name'];

                    $('#request_from').text('Request From: ' + data[1]['name']);
                    $('#request_title').val(data[0]['title']);
                    $('#request_msg').val(data[0]['message']);
                    $('#request_qty').val(data[0]['quantity'] + ' ' + data[0]['unit_type']);

                    //
                    // show button response if data[2] is null
                    // for user to create response from request
                    //
                    if(data[2] == null)
                    {
                        $('#btn_make_response').show();
                    }
                    else
                    {
                        $('#btn_make_response').hide();
                    }

                    $('#requestModal').modal();
                }
                else
                {
                    swal("Failed!", "Try again later!", "info");
                }
            }
        });
    }

    function viewAll(){
        $.ajax({
            url: "{{ URL::to('admin/dashboard/request_all') }}",
            type: 'get',
            success: function(data){
                var count = data.length;

                $('#tbl_request tbody tr').remove();

                if(count)
                {
                    var body = '';
                    for(var i = 0; i < count; i++)
                    {
                        body += '<tr>';
                        body += '<td>' + data[i]['title'] + '</td>';
                        body += '<td>' + data[i]['date'] + '</td>';
                        body += '<td>' + data[i]['user']['name'] + '</td>';
                        body += '<td><button class="btn btn-xs btn-primary pull-right" data-id="' + data[i]['id'] +'" onclick="getRequest(this)">View</button></td>';
                        body += '</tr>';
                    }

                    $('#tbl_request tbody').append(body);
                }
                else
                {
                    var body = '<tr><td>No Request!</td></tr>';
                    $('#tbl_request tbody').append(body);
                }

                $('#requestAllModal').modal();
            }
        });
    }

    function createResponse()
    {
        $('#requestModal').modal('hide');

        $('#response_request_from').val(request_from);

        $('#responseModal').modal();
    }

    function submitResponse(e)
    {
        $.ajax({
            url: '{{ URL::to("admin/dashboard/response") }}',
            type: 'POST',
            data: {
                request_id: request_id,
                message: $('#response_msg').val(),
                status: $(e).text()
            },
            success: function(data)
            {
                if(data == 'success')
                {
                    $('#responseModal').modal('hide');
                    swal("Response Sent!", "", "success");
                } 
                else
                {
                    swal("Response Fail!", "", "error");
                }
            }
        });
    }
</script>
@endsection