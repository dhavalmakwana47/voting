@extends('app.layout.app')
@section('page_title')
    Voting List
@endsection
@section('header-script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('customdownload/css/jquery.dataTables2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('customdownload/css/jquery.dataTables.min.css') }}">
@endsection
@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="font-size: 21px;">Voting List</h3>

                    {{-- Data insert button with check permission --}}
                    @if (auth()->user()->type != "0")
                    <a href="{{ route('voting.create') }}" class="btn btn-primary float-right nav-link ml-2">Create Normal Voting</a> &nbsp;
                    <a href="{{ route('option-voting.create') }}" class="btn btn-primary float-right nav-link">Create Option Voting</a>
                    @endif
                </div>

                {{-- Datatable --}}
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="voting_list" class="table table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th>Voting No</th>
                                    <th>Resolution Type</th>
                                    <th>Company Name</th>
                                    {{-- <th>AR/Scrutinizer</th> --}}
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <!-- <th>Meeting Date</th> -->
                                    <th>Created Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                    <th>Voter Report</th>
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
    <script>
        const user_type = "{{ auth()->user()->type }}"
        var table = $('#voting_list').DataTable({
            processing: true,
            serverSide: true,
            "pageLength": 10,
            ajax: "{{ route('voting.index') }}",
            "order": [[0, "desc"]],
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'resolution_type',
                    name: 'resolution_type'
                },
                {
                    data: 'company_name',
                    name: 'company_name'
                },
                // {
                //     data: 'user_name',
                //     name: 'user_name'
                // },
                {
                    data: 'start_date',
                    name: 'start_date'
                },
                {
                    data: 'end_date',
                    name: 'end_date'
                },
                // {
                //     data: 'meeting_date',
                //     name: 'meeting_date'
                // },
                {
                    data: 'created_at_modify',
                    name: 'created_at_modify'
                },
                {
                    data: 'is_active',
                    name: 'is_active',
                },

                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'member_report',
                    name: 'member_report'
                },
            ],
            "fnDrawCallback": function() {
                $("input[data-bootstrap-switch]").each(function() {
                    $(this).bootstrapSwitch();
                })
            }
        });

        function changeResolutionStatus(id) {
            var token = $("meta[name='csrf-token']").attr("content");

            $.ajax({
                type: 'POST',
                url: "{{ route('voting.changestatus') }}",
                data: {
                    id: id,
                    "_token": token,
                },
                success: function(data) {
                    table.ajax.reload(null, false);
                },
                beforeSend: function() {},
            });
        }

        
        function deleteResolution(id) {
            var token = $("meta[name='csrf-token']").attr("content");

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this resolution",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = "{{  route('voting.index') }}"+"/"+ id; 
                    $.ajax({
                        type: 'DELETE',
                        url: url,
                        data: {"_token": "{{ csrf_token() }}"},
                        success: function (res) {
                            createMessage(res)
                            table.ajax.reload();
                        },
                        beforeSend: function () {
                        },
                    });
                }
            })
        }
    </script>
@endsection
