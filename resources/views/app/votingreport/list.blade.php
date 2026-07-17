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
                <div class="card-header d-flex justify-content-between align-items-center" style="display: flex !important; justify-content: space-between !important; align-items: center !important;">
                    <h3 class="card-title" style="font-size: 21px; margin-bottom: 0;">Voting Report</h3>
                    <button type="button" class="btn btn-info btn-sm" id="view-downloads-btn" style="border-radius: 20px; padding: 6px 15px;">
                        <i class="fas fa-download mr-1"></i> Downloads <span class="badge badge-light ml-1" id="downloads-badge" style="display:none; color: #17a2b8;">0</span>
                    </button>
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

    {{-- Downloads Modal --}}
    <div class="modal fade" id="downloadsModal" tabindex="-1" role="dialog" aria-labelledby="downloadsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <div class="modal-header bg-dark text-white" style="border-bottom: none; padding: 15px 20px; display: flex !important; justify-content: space-between !important; align-items: center !important;">
                    <h5 class="modal-title" id="downloadsModalLabel">
                        <i class="fas fa-cloud-download-alt mr-2"></i> Centralized Downloads
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8; outline: none; background: none; border: none; font-size: 24px; line-height: 1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px; background-color: #f8f9fa; max-height: 450px; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="downloads-table" style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>Report Details</th>
                                    <th>Format</th>
                                    <th>Requested At</th>
                                    <th>Status / Progress</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="downloads-list">
                                <!-- Dynamic Content -->
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center py-4" id="downloads-empty" style="display: none;">
                        <i class="fas fa-folder-open text-muted fa-3x mb-3"></i>
                        <p class="text-muted">No download requests found. Click download buttons in the list to request reports.</p>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: none; background-color: #f8f9fa; display: flex !important; justify-content: flex-end !important;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 20px; padding: 5px 20px;">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer-script')
    <script src=" {{ asset('customdownload/js/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
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

        // CENTRALIZED DOWNLOADS LOGIC
        
        // Request download
        $(document).on('click', '.request-download-btn', function(e) {
            e.preventDefault();
            var btn = $(this);
            var resolution_id = btn.data('id');
            var format = btn.data('format');

            var originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Wait');

            $.ajax({
                url: "{{ route('votingreport.request_download') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    resolution_id: resolution_id,
                    format: format
                },
                success: function(response) {
                    if (response.success) {
                        createMessage(response.message, "success");
                        refreshDownloadsCount();
                    } else {
                        createMessage("Failed to request report.", "error");
                    }
                },
                error: function(xhr) {
                    var errorMsg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : "Failed to queue download.";
                    createMessage(errorMsg, "error");
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });

        var downloadsInterval = null;
        var completedDownloads = [];

        // View modal
        $('#view-downloads-btn').click(function() {
            $('#downloadsModal').modal('show');
            loadDownloadsList(true);
            startDownloadsPolling();
        });

        $('#downloadsModal').on('hidden.bs.modal', function () {
            stopDownloadsPolling();
        });

        function startDownloadsPolling() {
            stopDownloadsPolling();
            downloadsInterval = setInterval(function() {
                loadDownloadsList(false);
            }, 3000); // Poll every 3 seconds
        }

        function stopDownloadsPolling() {
            if (downloadsInterval) {
                clearInterval(downloadsInterval);
                downloadsInterval = null;
            }
        }

        function refreshDownloadsCount() {
            $.ajax({
                url: "{{ route('votingreport.get_downloads') }}",
                type: "GET",
                success: function(response) {
                    if (response.success && response.downloads.length > 0) {
                        var active = response.downloads.filter(d => {
                            var s = d.status.toLowerCase();
                            return s === 'queued' || s === 'processing';
                        }).length;
                        if (active > 0) {
                            $('#downloads-badge').text(active).show();
                        } else {
                            $('#downloads-badge').hide();
                        }
                    }
                }
            });
        }

        // Initialize and keep count badge updated
        refreshDownloadsCount();
        setInterval(refreshDownloadsCount, 12000); // 12 seconds badge update

        function loadDownloadsList(isInitial) {
            $.ajax({
                url: "{{ route('votingreport.get_downloads') }}",
                type: "GET",
                success: function(response) {
                    if (!response.success) return;
                    
                    var list = $('#downloads-list');
                    var empty = $('#downloads-empty');
                    
                    if (response.downloads.length === 0) {
                        list.html('');
                        empty.show();
                        $('#downloads-table').hide();
                        return;
                    }
                    
                    empty.hide();
                    $('#downloads-table').show();
                    
                    var html = '';
                    response.downloads.forEach(function(download) {
                        var statusBadge = '';
                        var statusStr = download.status.toLowerCase();
                        
                        // Notify user when a download transitions to completed
                        if (statusStr === 'completed') {
                            if (!completedDownloads.includes(download.id)) {
                                completedDownloads.push(download.id);
                                if (!isInitial) {
                                    createMessage("Report ready: " + download.report_name, "success");
                                    refreshDownloadsCount();
                                }
                            }
                        }

                        if (statusStr === 'queued') {
                            statusBadge = '<span class="badge badge-secondary" style="padding: 5px 10px;"><i class="fas fa-clock mr-1"></i> Queued</span>';
                        } else if (statusStr === 'processing') {
                            statusBadge = '<div>' + 
                                '<span class="badge badge-warning text-dark mb-1" style="padding: 5px 10px; font-weight: 600;"><i class="fas fa-sync fa-spin mr-1"></i> Processing (' + download.progress + '%)</span>' +
                                '<div class="progress mb-0" style="height: 6px; border-radius: 3px;">' +
                                    '<div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" style="width: ' + download.progress + '%"></div>' +
                                '</div>' +
                            '</div>';
                        } else if (statusStr === 'completed') {
                            statusBadge = '<span class="badge badge-success" style="padding: 5px 10px;"><i class="fas fa-check-circle mr-1"></i> Completed</span>';
                        } else {
                            statusBadge = '<span class="badge badge-danger" style="padding: 5px 10px;" title="' + (download.error_message || 'Unknown error occurred') + '"><i class="fas fa-times-circle mr-1"></i> Failed</span>';
                        }
                        
                        var actionBtn = '';
                        if (statusStr === 'completed') {
                            actionBtn = '<a href="' + download.download_url + '" class="btn btn-sm btn-success btn-block" style="border-radius: 20px;"><i class="fas fa-download mr-1"></i> Download</a>';
                        } else if (statusStr === 'failed') {
                            actionBtn = '<button class="btn btn-sm btn-warning btn-block retry-btn" data-id="' + download.id + '" style="border-radius: 20px; color: black; font-weight: 500;"><i class="fas fa-redo mr-1"></i> Retry</button>';
                        } else {
                            actionBtn = '<button class="btn btn-sm btn-light btn-block" disabled style="border-radius: 20px;"><i class="fas fa-spinner fa-spin"></i> Pending</button>';
                        }
                        
                        html += '<tr style="border-bottom: 1px solid #dee2e6;">' +
                            '<td style="vertical-align: middle;">' +
                                '<div style="font-weight: 600; font-size: 14px; color: #333;">' + download.company_name + '</div>' +
                                '<div style="font-size: 11px; color: #777; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="' + download.report_name + '">' + download.report_name + '</div>' +
                            '</td>' +
                            '<td style="vertical-align: middle;">' +
                                '<span class="badge badge-light" style="border: 1px solid #ddd; font-size: 11px;">' + download.report_type + '</span>' +
                            '</td>' +
                            '<td style="vertical-align: middle; font-size: 13px; color: #555;">' + download.requested_at + '</td>' +
                            '<td style="vertical-align: middle; min-width: 140px;">' + statusBadge + '</td>' +
                            '<td style="vertical-align: middle;" class="text-center">' + actionBtn + '</td>' +
                        '</tr>';
                    });
                    
                    list.html(html);
                }
            });
        }

        // Retry action
        $(document).on('click', '.retry-btn', function(e) {
            e.preventDefault();
            var btn = $(this);
            var id = btn.data('id');
            
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Retry');
            
            $.ajax({
                url: "/votingreport/download/" + id + "/retry",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        createMessage(response.message, "success");
                        loadDownloadsList(false);
                        refreshDownloadsCount();
                    } else {
                        createMessage("Failed to retry download.", "error");
                    }
                },
                error: function(xhr) {
                    createMessage("Retry error.", "error");
                }
            });
        });
    </script>
@endsection
