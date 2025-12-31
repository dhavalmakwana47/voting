@extends('app.member.layout')
@section('header-script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('customdownload/css/jquery.dataTables2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('customdownload/css/jquery.dataTables.min.css') }}">
@endsection
@section('body-content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title" style="font-size: 21px;">Voter Voting Screen</h3>
                        </div>

                        {{-- Datatable --}}
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="company_list" class="table table-bordered yajra-datatable">
                                    <thead>
                                        <tr>
                                            <th>Voting No</th>
                                            <th>Voter Name</th>
                                            <th>Company Name</th>
                                            <th>Voting Share</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            {{-- <th>Holding Date</th> --}}
                                            <th>Voting/Status</th>
                                            <th>Voting Receipt</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
@endsection
@section('footer-script')
    <script src=" {{ asset('customdownload/js/jquery.dataTables.min.js') }}"></script>
    <script>
        var table = $('#company_list').DataTable({
            processing: true,
            serverSide: true,
            "pageLength": 10,
            ajax: "{{ route('member.voting_list') }}",
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'voter_name',
                    name: 'voter_name'
                },
                {
                    data: 'company_name',
                    name: 'company_name'
                },
                {
                    data: 'voting_amount',
                    name: 'voting_amount'
                },
                {
                    data: 'start_date',
                    name: 'start_date'
                },
                {
                    data: 'end_date',
                    name: 'end_date'
                },
                {
                    data: 'voting_status',
                    name: 'voting_status'
                },
                {
                    data: 'voting_recipt',
                    name: 'voting_recipt'
                },

            ],
            "fnDrawCallback": function() {
                // $("input[data-bootstrap-switch]").each(function() {
                //     $(this).bootstrapSwitch();
                // })
            }
        });
    </script>
@endsection
