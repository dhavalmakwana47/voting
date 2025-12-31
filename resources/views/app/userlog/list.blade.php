@extends('app.layout.app')
@section('page_title')
    User Log
@endsection
@section('header-script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('customdownload/css/jquery.dataTables2.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('customdownload/css/jquery.dataTables.min.css') }}">
@endsection
@section('content-body')
    <form action="{{ route('userlog.download') }}" method="POST">
        @csrf
        <div class="row">
            @if (auth()->user()->type == '0')
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="user-type">User Type</label>
                        <select class="form-control" id="user-type" name="user_type">
                            <option value="">Select Type</option>
                            <option value="admin">Admin</option>
                            <option value="ar">AR</option>
                            <option value="scrutinizer">Scrutinizer</option>

                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="user-id">User</label>
                        <select class="form-control" id="user-id" name="user_id">
                            <option value="">Select User</option>
                        </select>
                    </div>
                </div>
            @endif
            @if (auth()->user()->type != 0)
                <input type="hidden" id="user-id" value="{{ auth()->user()->id }}">
            @endif

            <div class="col-md-3">
                <div class="form-group">
                    <label for="voting-id">Voting</label>
                    <select class="form-control" id="voting-id" name="voting_id">
                        <option value="">Select Voting</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 mt-4">
                <button type="submit" class="btn btn-success">Export</button>

            </div>
        </div>
    </form>
    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="font-size: 21px;">User Log</h3>

                    {{-- Data insert button with check permission --}}
                </div>

                {{-- Datatable --}}
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="users_list" class="table table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date & Time</th>
                                    <th>User Type</th>
                                    <th>Voter ID</th>
                                    <th>Voting ID</th>
                                    <th>Member/User Name</th>
                                    <th>Action</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer-script')
    <script src=" {{ asset('customdownload/js/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
         $(document).ready(function() {
            $('#voting-id').select2({
                placeholder: "Select an voting",
                allowClear: true
            });
            @if (auth()->user()->type == '0')

            $('#user-id').select2({
                placeholder: "Select an user",
                allowClear: true
            });
            @endif
        });

        const csrf_token = "{{ csrf_token() }}";

        $('#user-type').on('change', function() {
            var user_type = $(this).val();
            var formData = new FormData();
            formData.append("user_type", user_type);
            formData.append("_token", csrf_token);
            $.ajax({
                url: "{{ route('userlog.users') }}", // Specify the route where you handle the file upload in your Laravel controller
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#user-id').html(response)
                }
            });

        });



        var table = $('#users_list').DataTable({
            processing: true,
            serverSide: true,
            "pageLength": 10,
            "columnDefs": [{
                "targets": [3], // Index of the column you want to hide
                "visible": false
            }],
            "ajax": {
                "url": "{{ route('userlog.index') }}",
                "type": "GET",
                "data": function(d) {
                    return jQuery.extend({}, d, {
                        "user_id": $('#user-id').val(),
                        "voting_id": $('#voting-id').val()
                    });
                },
            },
            "order": [
                [0, "desc"]
            ],
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'user_type',
                    name: 'user_type'
                },
                {
                    data: 'member_id',
                    name: 'member_id'
                },
                {
                    data: 'resolution_id',
                    name: 'resolution_id'
                },
                {
                    data: 'user_name',
                    name: 'user_name'
                },
                {
                    data: 'action',
                    name: 'action'
                },
                {
                    data: 'ipaddress',
                    name: 'ipaddress'
                }
            ]
        });
        $('#user-id').on('change', function() {

            var user_id = $(this).val();
            var formData = new FormData();
            formData.append("user_id", user_id);
            formData.append("_token", csrf_token);
            $.ajax({
                url: "{{ route('userlog.get_votings') }}", // Specify the route where you handle the file upload in your Laravel controller
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#voting-id').html(response)
                }
            });
        })
        $('#user-id, #voting-id').on('change', function() {
            voting_id = $('#voting-id').val();
            voting_id == "" ? table.column(3).visible(false) : table.column(3).visible(true);
            table.draw()
        });
        @if (auth()->user()->type != 0)
            $(document).ready(function() {
                $('#user-id').change()
            })
        @endif
    </script>
@endsection
