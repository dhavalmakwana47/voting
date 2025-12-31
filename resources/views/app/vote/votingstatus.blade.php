@extends('app.layout.app')
@section('page_title')
    User List
@endsection
@section('header-script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('customdownload/css/jquery.dataTables2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('customdownload/css/jquery.dataTables.min.css') }}">
    <!-- jQuery -->
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
@endsection
@section('content-body')
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="resolution_list">Choose an Voting No for Voting Status
                </label>
                <select name="resolution" class="form-control select2" id="resolution_list"
                    data-placeholder="Select a EVSN">
                    <option value="0">Voting No.</option>
                    @foreach ($resolutionArr as $resolution)
                        <option value="{{ $resolution->id }}" {{ old('resolution') == $resolution->id ? 'selected' : '' }}>
                            {{ $resolution->id }} ({{ $resolution->company->name }})
                        </option>
                    @endforeach
                </select>
                @error('resolution')
                    <small style="color:red; font-weight:600;">{{ $message }}</small>
                @enderror
            </div>
        </div>
        <div class="col-md-12" id="main_div" style="display: none">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="font-size: 21px;">View Voting Status</h3>

                    {{-- Data insert button with check permission --}}
                    {{-- <a href="{{ route('users.create') }}" class="btn btn-primary float-right nav-link">Add User</a> --}}
                </div>

                {{-- Datatable --}}
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="voting_list" class="table table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Voter Name</th>
                                    <th>Voter Email</th>
                                    <th>Vote casted Or Not</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div id="membercounttable" style="">
                        <table id="totalmembertable" class="table text-center table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Total Voters(Shares/INR Amount) </th>
                                    <th>Voted Voters(Shares/INR Amount) </th>
                                    <th>Remaining Voters(Shares/INR Amount)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td id="toal-member">0</td>
                                    <td id="vote-member">0</td>
                                    <td id="remaining-member">0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer-script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src=" {{ asset('customdownload/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#resolution_list').select2({
                placeholder: "Select an option",
                allowClear: true
            });
        });
        let resolutionId = 0;


        var table = $('#voting_list').DataTable({
            processing: true,
            serverSide: true,
            "pageLength": 10,
            "ajax": {
                'type': 'POST',
                'url': "{{ route('vote.list') }}",
                'data': {
                    "_token": "{{ csrf_token() }}",
                    "resolution_id": function() {
                        return resolutionId;
                    }
                },
            },
            columns: [
                {
                    data: 'DT_RowIndex',
                    'orderable': false,
                    'searchable': false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'vote_status',
                    name: 'vote_status'
                }
            ]
        });

        $('#resolution_list').on('change', function() {
            resolutionId = $('#resolution_list').val();
            if (resolutionId > 0) {
                $('#main_div').show();
            } else {
                $('#main_div').hide();

            }

            $.ajax({
                type: 'GET',
                url: "{{ route('vote.share_count') }}",
                data: {
                    resolution_id: resolutionId
                },
                success: function(data) {
                    $('#toal-member').text(data.total_member);
                    $('#vote-member').text(data.voted_share);
                    $('#remaining-member').text(data.unvoted_share)
                }
            });
            table.ajax.reload();
        })
    </script>
@endsection
