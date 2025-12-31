@extends('app.layout.app')
@section('page_title')
    Voting List
@endsection
@section('header-script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('customdownload/css/jquery.dataTables2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('customdownload/css/jquery.dataTables.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="font-size: 21px;">Voting Report</h3>
                </div>

                {{-- Datatable --}}
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="company_list">Company</label>
                                <select name="company" class="form-control select2" id="company_list"
                                    data-placeholder="Select a Company">
                                    @foreach ($companyArr as $company)
                                        <option value="{{ $company->id }}"
                                            {{ old('company') == $company->id ? 'selected' : '' }}>{{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company')
                                    <small style="color:red; font-weight:600;">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="voting_list" class="table table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th>Sr No</th>
                                    <th>Voting No</th>
                                    <th>Created By</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Download</th>
                                    <th>New Report</th>
                                    <th>View</th>
                                    <th>PDF</th>
                                    <th>% Report</th>
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
            $('#company_list').select2({
                placeholder: "Select an option",
                allowClear: true
            });
        });
        const user_type = "{{ auth()->user()->type }}"
        var table = $('#voting_list').DataTable({
            processing: true,
            serverSide: true,
            "pageLength": 10,
            ajax: {
                url: "{{ route('votingreport.index') }}",
                data: function(d) {
                    d.company = $('#company_list').val(),
                        d.search = $('input[type="search"]').val()
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },

                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'user_name',
                    name: 'user_name'
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
                    data: 'download',
                    name: 'download',
                    orderable: false,
                    searchable: false
                },

                {
                    data: 'new_report',
                    name: 'new_report',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'new_report_view',
                    name: 'new_report_view',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'pdf',
                    name: 'pdf',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'reportpdf',
                    name: 'reportpdf',
                    orderable: false,
                    searchable: false
                }
            ],
        });
        $('#company_list').change(function() {
            table.draw();
        });
    </script>
@endsection
