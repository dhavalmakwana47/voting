@php
    if (isset($resolution)) {
        $memberListRoute = route('member.list', $resolution->id);
    }
@endphp
@extends('app.layout.app')
@section('page_title')
    Admin-Assign
@endsection
@section('header-script')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('custom/resolution/css/addupdate.css') }}">
    <link href="{{ asset('customdownload/css/jquery.dataTables2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('customdownload/css/jquery.dataTables.min.css') }}">
@endsection
@section('content-body')
    <style type="text/css">
        textarea {
            width: 100%;
        }
    </style>

    <div class="row">

        <div class="modal fade" id="add-member-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="form-title">Add Voter</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('member.store') }}" id="member-form" data-type="add">
                        <input type="hidden" id="member-id" value="">
                        @isset($resolution)
                            <input type="hidden" id="resolution-id" value="{{ $resolution->id }}">
                        @endisset
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="member-name">Member Name</label>
                                <input type="text" class="form-control" name="member_name" id="member-name">
                            </div>

                            <div class="form-group">
                                <label for="member-share">No. Of Share</label>
                                <input type="number" class="form-control" name="member_share" id="member-share"
                                    min="0">
                            </div>

                            <div class="form-group">
                                <label for="member-name">Email</label>
                                <input type="email" class="form-control" name="member_email" id="member-email">
                            </div>

                            <div class="form-group">
                                <label for="member-phone">Contact No</label>
                                <input type="number" class="form-control" name="member_phone" id="member-phone">
                            </div>

                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="memberFormSubmit()">Submit</button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ isset($resolution) ? 'Edit' : 'New' }} Voting
                    </h3>
                </div>
                @error('resolution_files.*')
                    <script>
                        alert("{{ $message }}")
                    </script>
                @enderror
                {{-- Data insert and update in one file --}}
                <form action="{{ isset($resolution) ? route('voting.update', $resolution->id) : route('voting.store') }}"
                    method="POST" enctype="multipart/form-data" id="resolution-form">
                    @isset($resolution)
                        @method('PATCH')
                    @endisset
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            @if (isset($resolution) && auth()->user()->type == '0')
                                <div class="col-sm-6 mb-2">
                                    <label for="resolution_meetingdate">Active Status</label>

                                    <div>
                                        <input type="checkbox" data-bootstrap-switch="" name="is_active"
                                            {{ $resolution->is_active ? 'checked' : '' }}>
                                    </div>
                                </div>
                            @endif
                            <div class="col-sm-6 mb-2">
                                <label for="resolution_meetingdate">Is Modifiable</label>

                                <div>
                                    <input type="checkbox" data-bootstrap-switch="" name="is_modifiable"
                                        {{ isset($resolution) && $resolution->is_modifiable ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <label>Voting Type*</label>
                                <div class="input-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="0" name="evsn_type"
                                            {{ old('evsn_type') == '0' ? 'checked' : '' }}
                                            {{ (isset($resolution) && $resolution->evsn_type) == '0' ? 'checked ' : (empty(old('evsn_type')) ? 'checked' : '') }}
                                            {{ isset($resolution) ? 'disabled' : '' }}>
                                        <label class="form-check-label">Normal Voting</label>
                                    </div>&nbsp;&nbsp;
                                    <!-- <div class="form-check">
                                                                            <input class="form-check-input" type="radio" value="1" name="evsn_type"
                                                                                {{ old('evsn_type') == '1' ? 'checked' : '' }}
                                                                                {{ isset($resolution) && $resolution->evsn_type == '1' ? 'checked ' : '' }} {{ isset($resolution) ? 'disabled' : '' }}>
                                                                            <label class="form-check-label">Instruction</label>
                                                                        </div>&nbsp;&nbsp;
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" value="2" name="evsn_type"
                                                                                {{ old('evsn_type') == '2' ? 'checked' : '' }}
                                                                                {{ isset($resolution) && $resolution->evsn_type == '2' ? 'checked ' : '' }} {{ isset($resolution) ? 'disabled' : '' }}>
                                                                            <label class="form-check-label">Option Voting</label>
                                                                        </div> -->
                                    @error('evsn_type')
                                        <small style="color:red; font-weight:600;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="company_list">Company Name*</label>
                                    <select name="company" class="form-control select2" id="company_list"
                                        data-placeholder="Select a Company" data-error="#company-error"
                                        {{ $active }}>
                                        <option value=""></option>
                                        @if (isset($resolution))
                                            <option value=" {{ $resolution->company->id }}" selected>
                                                {{ $resolution->company->name }}</option>
                                        @else
                                            @foreach ($companyArr as $company)
                                                <option value="{{ $company->id }}"
                                                    {{ in_array($company->id, [old('company'), isset($resolution) ? $resolution->company_id : 0]) ? 'selected' : '' }}>
                                                    {{ $company->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('company')
                                        <small style="color:red; font-weight:600;">{{ $message }}</small>
                                    @enderror
                                    <span class="error" id="company-error"></span>

                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="meeting_details">Meeting Details</label>
                                    <input class="form-control" placeholder="Write details here" type="text"
                                        type="text" name="meeting_details" id="meeting_details"
                                        value="{{ old('meeting_details', isset($resolution) ? $resolution->meeting_details : '') }}">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="resolution_startdate">Voting Start Date*</label>
                                    <div class="input-group date" id="resolution_startdate" data-target-input="nearest">
                                        <input type="text"
                                            value="{{ old('start_date', isset($resolution) ? Carbon\Carbon::parse($resolution->start_date)->format('d/m/Y g:i A') : '') }}"
                                            name="start_date" class="form-control datetimepicker-input"
                                            data-target="#resolution_startdate" data-error="#startdate-error"
                                            data-toggle="datetimepicker" autocomplete="off" {{ $active }} />
                                        <div class="input-group-append" data-target="#resolution_startdate"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                    @error('start_date')
                                        <small style="color:red; font-weight:600;">{{ $message }}</small>
                                    @enderror
                                    <span class="error" id="startdate-error"></span>

                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="resolution_enddate">Voting End Date*</label>
                                    <div class="input-group date" id="resolution_enddate" data-target-input="nearest">
                                        <input type="text"
                                            value="{{ old('end_date', isset($resolution) ? Carbon\Carbon::parse($resolution->end_date)->format('d/m/Y g:i A') : '') }}"
                                            name="end_date" class="form-control datetimepicker-input"
                                            data-error="#enddate-error" data-target="#resolution_enddate"
                                            data-toggle="datetimepicker" autocomplete="off" />
                                        <div class="input-group-append" data-target="#resolution_enddate"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                    @error('end_date')
                                        <small style="color:red; font-weight:600;">{{ $message }}</small>
                                    @enderror
                                    <span class="error" id="enddate-error"></span>
                                </div>
                            </div>
                            <!--
                                                                <div class="col-sm-10">
                                                                    <div class="form-group">
                                                                        <label for="resolution_meetingdate">Meeting Date*</label>
                                                                        <div class="input-group date" id="resolution_meetingdate"
                                                                            data-target-input="nearest">
                                                                            <input type="text"
                                                                                value="{{ old('meeting_date', isset($resolution) ? Carbon\Carbon::parse($resolution->meeting_date)->format('d/m/Y g:i A') : '') }}"
                                                                                name="meeting_date" class="form-control datetimepicker-input"
                                                                                data-error="#meeting-date-error" data-target="#resolution_meetingdate"  data-toggle="datetimepicker" autocomplete="off" />
                                                                            <div class="input-group-append" data-target="#resolution_meetingdate"
                                                                                data-toggle="datetimepicker">
                                                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                                            </div>
                                                                        </div>
                                                                        @error('meeting_date')
        <small style="color:red; font-weight:600;">{{ $message }}</small>
    @enderror
                                                                        <span class="error" id="meeting-date-error"></span>

                                                                    </div>
                                                                </div> -->
                            <div class="col-sm-12 ">
                                <a href="{{ route('voting.sample-download') }}"
                                    class="btn btn-sm btn-success mt-3">Download Voter Template</a>
                            </div>


                            <div class="col-sm-8" id="member_file_div">
                                <div class="form-group">
                                    <label for="member_file">Voter File*</label>

                                    <div class="custom-file">
                                        <input type="file" name="member_file" class="custom-file-input"
                                            id="member_file" onchange="fileChange(this)" data-error="#member-file-error">
                                        <label class="custom-file-label" for="member_file" id="member_file_label">Choose
                                            file</label>

                                    </div>
                                    @isset($resolution)
                                        <a href="{{ asset('uploads/members_files/' . $resolution->member_file) }}">View</a>
                                    @endisset

                                    @error('member_file')
                                        <small style="color:red; font-weight:600;">{{ $message }}</small>
                                    @enderror
                                    <span class="error" id="member-file-error"></span>

                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group mt-4">
                                    <button type="button" class="btn btn-primary" onclick="uploadFile()"
                                        id="member-verify-btn">Upload &
                                        Verify</button>
                                    <button type="button" class="btn btn-primary" onclick="restExcelFile()"
                                        {{ $active }}>Reset
                                        Excel</button>
                                </div>
                            </div>
                            @if (isset($resolution) && !empty($resolution->members))
                                <div class="col-sm-10 mb-3" id="add-member-section">
                                    <button type="button" class="btn btn-primary btn-sm"
                                        onclick="openMemberFormModal('add')" {{ $active }}>Add New Voter</button>
                                </div>
                            @endif
                            <div class="col-12">
                                <table id="excel_table">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Name</th>
                                            <th>Share</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            @if (isset($resolution) && !empty($resolution->members))
                                                <th id="action-column">Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody id="excel_body">
                                    </tbody>
                                    <tfoot id="table_footer"></tfoot>
                                </table>
                            </div><br>
                        </div>
                        <div id="resolution-files">
                            <div class="row file-wrapper">
                                <div class="col-6">
                                    <textarea class="resolution_description required-section" id="resolution_description" cols="60" rows="10"
                                        disabled name="description[]" {{ $active }}>{{ isset($resolutionDetailsArr) ? $resolutionDetailsArr[0]->description : '' }}</textarea>
                                </div>
                                <div class="col-4">
                                    @isset($resolutionDetailsArr)
                                        <input type="hidden" name="resolution_details_id[]"
                                            id="resolution_details_id-{{ $resolutionDetailsArr[0]->id }}"
                                            value="{{ $resolutionDetailsArr[0]->id }}">
                                    @endisset
                                    <input type="file"
                                        value="{{ isset($resolutionDetailsArr) ? asset('uploads/resolution_details_files/' . $resolutionDetailsArr[0]->file_name) : '' }}"
                                        class="custom-file-input required-section" disabled
                                        @if (isset($resolutionDetailsArr)) name="{{ 'resolution_files[' . $resolutionDetailsArr[0]->id . ']' }}"
                                        @else
                                        name="resolution_files[]" @endif
                                        @isset($resolutionDetailsArr) data-id="{{ $resolutionDetailsArr[0]->id }}" @endisset
                                        data-error="#member-file-error-0" onchange="fileChange(this)"
                                        {{ $active }}>
                                    <label
                                        class="custom-file-label">{{ isset($resolutionDetailsArr) ? $resolutionDetailsArr[0]->file_name : 'Choose file' }}</label>
                                    <br>
                                    <span class="error" id="member-file-error-0"></span>
                                </div>
                                <div class="col-2">
                                    @isset($resolutionDetailsArr)
                                        <a href="{{ route('memberresolutiondetails.download', Crypt::encrypt($resolutionDetailsArr[0]->id)) }}"
                                            class="btn btn-success">View</a>
                                    @endisset
                                    <button type="button" class="btn btn-primary required-section" disabled
                                        id="rowAdder" onclick="AddResolutionDetalisRaw()" {{ $active }}>Add
                                        More</button>
                                </div>
                                <br>
                            </div>

                            @isset($resolutionDetailsArr)
                                <input type="hidden" name="id" value="{{ $resolution->id }}">
                                @foreach ($resolutionDetailsArr as $value)
                                    @if ($loop->index > 0)
                                        <div class="row file-wrapper">
                                            <div class="col-6">

                                                <input type="hidden" name="resolution_details_id[]"
                                                    id="resolution_details_id-{{ $value->id }}"
                                                    value="{{ $value->id }}">
                                                <textarea cols="60" rows="10" class="resolution_description required-section"
                                                    name="{{ 'description[' . $loop->index . ']' }}" {{ $active }}>{{ $value->description }}</textarea>
                                            </div>
                                            <div class="col-4">
                                                <input type="file"
                                                    value="{{ asset('uploads/resolution_details_files/' . $value->file_name) }}"
                                                    class="custom-file-input required-section" onchange="fileChange(this)"
                                                    data-error="{{ '#member-file-error-' . $loop->index }}"
                                                    name="{{ 'resolution_files[' . $value->id . ']' }}"
                                                    data-id="{{ $value->id }}" {{ $active }}>
                                                <label class="custom-file-label">{{ $value->file_name }}</label>
                                                <br>
                                                <span class="error" id="{{ 'member-file-error-' . $loop->index }}"></span>
                                            </div>
                                            <div class="col-2">
                                                <a href="{{ route('memberresolutiondetails.download', Crypt::encrypt($value->id)) }}"
                                                    class="btn btn-success">View</a>
                                                @if ($value->votes->count() < 1)
                                                    <button type="button"
                                                        class="btn btn-danger required-section reolution-delete-btn"
                                                        {{ $active }}>Delete</button>
                                                @endif
                                            </div>
                                            <br>
                                        </div>
                                    @endif
                                @endforeach
                            @endisset

                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Submit
                        </button>
                        &nbsp;
                    </div>
                </form>

            </div>
        </div>
    </div>
    </div>
@endsection
@section('footer-script')
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script src=" {{ asset('customdownload/js/jquery.dataTables.min.js') }}"></script>
    <script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>


    <script>
        const csrf_token = "{{ csrf_token() }}";
        const memberFileValidationRoute = "{{ route('voting.upload') }}";
        let randomCount = 0;
        @if (isset($resolutionDetailsArr))
            randomCount = "{{ count($resolutionDetailsArr) }}";
            memberUpdateRoute = "{{ route('member.update') }}";
            let hideColumn = {!! $active == 'disabled' ? '0' : '1' !!};

            var table = $('#excel_table').DataTable({
                processing: true,
                serverSide: true,
                "pageLength": 10,
                ajax: "{{ $memberListRoute }}",
                "columnDefs": [{
                    "targets": [5], // Index of the column you want to hide
                    "visible": hideColumn
                }],
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'share',
                        name: 'share'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                "fnDrawCallback": function() {
                    $("input[data-bootstrap-switch]").each(function() {
                        $(this).bootstrapSwitch();
                    })
                }
            });
            table.on('xhr', function() {
                var json = table.ajax.json();
                var totalShares = 0;

                json.data.forEach(function(member) {
                    totalShares += parseFloat(member.share); // Ensure 'share' is treated as a number
                });
                $('#table_footer').html(
                    '<tr><td colspan="2" class="text-center" rowspan="1">Grand Total</td><td rowspan="1" colspan="1">' +
                    totalShares.toFixed(2) + // Ensure the total is formatted to 2 decimal places
                    '</td><td rowspan="1" colspan="1"></td><td rowspan="1" colspan="1"></td></tr>'
                );
            });
        @else
        @endif
        var memberDeleteRoute = "{{ route('member.delete') }}";
    </script>
    <script src="{{ asset('custom\resolution\js\addupdate.js') }}"></script>
    <script>
        @if (isset($resolution))
            $(document).ready(function() {
                $('#member_file').next().text("{{ $resolution->member_file }}");
                $("#member-verify-btn, #member_file").attr("disabled", "");
                @if ($active != 'disabled')
                    $('#rowAdder, #resolution_description').removeAttr('disabled')
                    $('[data-error="#member-file-error-0"]').removeAttr('disabled')
                @endif
                addResolutionDetailsValidation();
                $('#excel_table').show()
            })
        @endif
    </script>
@endsection
