<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', app_name())</title>

        <!-- Meta -->
        <meta name="description" content="@yield('meta_description', 'Laravel 5 Boilerplate')">
        <meta name="author" content="@yield('meta_author', 'Anthony Rappa')">
        @yield('meta')

        <!-- Styles -->
        @yield('before-styles')

        <!-- Check if the language is set to RTL, so apply the RTL layouts -->
        <!-- Otherwise apply the normal LTR layouts -->
        @langRTL
            {{ Html::style(getRtlCss(mix('css/frontend.css'))) }}
        @else
            {{ Html::style(mix('css/frontend.css')) }}
        @endif

        @yield('after-styles')

        <!-- Scripts -->
        <script>
            window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
            ]); ?>
        </script>
    </head>
    <body id="app-layout">
        <div id="app">
            @include('includes.partials.logged-in-as')
            @include('frontend.includes.nav')

            <div class="container">
                @include('includes.partials.messages')
                @yield('content')
            </div><!-- container -->
            <!-- Modal -->
            <div id="requestModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Request</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="title">Title:</label>
                                <input type="text" class="form-control" name="request_title" id="request_title">
                            </div>
                            <div class="form-group">
                                <label for="request_ingredient">Message:</label>
                                <textarea class="form-control" name="request_msg" id="request_msg" rows='6'></textarea>
                            </div>
                            <div class="form-group">
                            <div class="row">
                                <div class="col-lg-4">
                                    <label for="quantity">Quantity</label>
                                    <input type="number" class="form-control" id="quantity" value="1">
                                </div>
                                <div class="col-lg-4">
                                    <label for="unit_type">Unit Type</label>
                                    <select class="form-control" id="unit_type">
                                        <option>Unit</option>
                                        <option>Slice</option>
                                        <option>Piece</option>
                                        <option>Case</option>
                                        <option>Gallons</option>
                                        <option>Liters</option>
                                        <option>Ounces</option>
                                        <option>Grams</option>
                                        <option>Pack</option>
                                        <option>Pound</option>
                                        <option>Other</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label for="other">Other (Specify)</label>
                                    <input type="text" class="form-control" id="other">
                                </div>
                            </div>
                                
                            </div>        
                        </div>
                        <div class="modal-footer">
                            <span class="pull-left" style="color:red" id="request_error"></span>
                            <button type="button" class="btn btn-success" id="btn_request">Submit</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="viewModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">View Request</h4>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordred table-hover table-responsive" id="tbl_request">
                                <thead>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>&nbsp;</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="4" style="text-align: center;">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end modal -->
        </div><!--#app-->


        <div id="responseModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Response</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="title">Status:</label>
                                <input type="text" class="form-control" name="status" id="status" readonly>
                            </div>
                            <div class="form-group">
                                <label for="request_ingredient">Message:</label>
                                <textarea class="form-control" name="response_msg" id="response_msg" rows='6' readonly></textarea>
                            </div>       
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>


        <!-- Scripts -->
        @yield('before-scripts')
        {!! Html::script(mix('js/frontend.js')) !!}
        @yield('after-scripts')

        @include('includes.partials.ga')

        <script type="text/javascript">
            $('#btn_request').on('click', function(){
                var title = $('#request_title').val();
                var msg   = $('#request_msg').val();
                var qty   = $('#quantity').val();
                var type  = $('#unit_type').val();
                var other = $('#other').val();

                if(msg == '' || qty <= 0)
                {
                    $('#request_error').text('Please out all fields!');
                }
                else
                {
                    if(type == 'Other')
                    {   
                        if(other == ''){
                            $('#request_error').text('Fill out other!');
                            return;
                        }

                        type = other;
                    }

                    $.ajax({
                        url: '{{ url("dashboard/request") }}',
                        type: 'POST',
                        data: {
                            title: title,
                            message: msg,
                            quantity: qty,
                            unit_type: type
                        },
                        success: function(data){
                            if(data == 'success')
                            {
                                $('#requestModal').modal('hide');
                                swal('Request Sent!','', 'success');
                            }
                            else
                            {
                                swal('Request Failed!','', 'error');
                            }

                            $('#request_title').val('');
                            $('#request_msg').val('');
                            $('#quantity').val(1);
                            $('#unit_type').val('Piece');
                            $('#other').val('');

                        },
                        error: function(data){
                            swal('Request Failed!','', 'error');
                        }
                    }); //end ajax

                    
                    $('#request_error').text('');
                } // end of statement
            });

            function getAllRequest(){
                $.ajax({
                    url: '{{ URL::to("dashboard/request_all") }}',
                    type: 'GET',
                    success: function(data){
                        var count = data.length;

                        $('#tbl_request tbody tr').remove();

                        if(count)
                        {
                            var body = '';
                            for(var i = 0; i < count; i++)
                            {
                                var response = data[i]['response'];

                                body += '<tr>';
                                body += '<td>' + data[i]['title'] + '</td>';
                                body += '<td>' + data[i]['date'] + '</td>';

                                if(response == null)
                                {
                                    body += '<td><span class="label label-default">No Response</span></td>'
                                } 
                                else
                                {
                                    body += '<td><button class="btn btn-xs btn-primary" data-id="' + response['id'] +'" onclick="viewResponse(this)">View Response</button></td>';
                                }

                                
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

            function viewResponse(e)
            {
                var id = $(e).attr('data-id');

                $.ajax({
                    url: '{{ URL::to("dashboard/get_response") }}/' + id,
                    type: 'GET',
                    success: function(data){
                        $('.modal').modal('hide');

                        $('#status').val(data['status']);
                        $('#response_msg').val(data['message']);

                        $('#responseModal').modal();
                    }
                });
            }
        </script>
    </body>
</html>