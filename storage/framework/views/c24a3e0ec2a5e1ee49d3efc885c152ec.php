<?php
    $memberReportRoute = route('member.member_report', $resolutionId);
?>

<?php $__env->startSection('page_title'); ?>
    User List
<?php $__env->stopSection(); ?>
<?php $__env->startSection('header-script'); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"Voter Voting Screen>
    <link href="<?php echo e(asset('customdownload/css/jquery.dataTables2.min.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('customdownload/css/jquery.dataTables.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('plugins/select2/css/select2.min.css')); ?>">

    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content-body'); ?>
    <div class="modal fade" id="add-member-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="form-title">Edit Voter</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo e(route('member.store')); ?>" id="member-form" data-type="add">
                    <input type="hidden" id="member-id" value="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="member-name">Voter Name</label>
                            <input type="text" class="form-control" name="member_name" id="member-name">
                        </div>

                        <div class="form-group">
                            <label for="member-share">No. Of Share</label>
                            <input type="number" class="form-control" name="member_share" id="member-share" min="0">
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

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="font-size: 21px;">Voter Report</h3>

                    
                    <a href="<?php echo e(route('voting.index')); ?>" class="btn btn-primary float-right nav-link  ml-2">Back</a>
                    <?php if(auth()->user()->type == '0'): ?>
                        <!-- Export Button -->
                        <a href="javascript:void(0);" class="btn btn-success float-right nav nav-link"
                            id="exportButton">Export Data</a>

                        <!-- Modal -->
                        <div class="modal fade" id="exportModal" tabindex="-1" role="dialog"
                            aria-labelledby="exportModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exportModalLabel">Select Fields to Export</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="exportForm" method="POST"
                                            action="<?php echo e(route('member.list-export', $resolutionId)); ?>">
                                            <?php echo csrf_field(); ?> <!-- Add the CSRF token -->
                                            <div class="form-group">
                                                <label for="fields">Select Fields</label>
                                                <select class="form-control select2" id="fields" name="selected_fields[]"
                                                    multiple>
                                                    <option value="name">Name</option>
                                                    <option value="share">Share</option>
                                                    <option value="email">Email</option>
                                                    <option value="user_name">Username</option>
                                                    <option value="password">Password</option>
                                                    <option value="phone">Phone</option>
                                                    <option value="email_sent">Email Status</option>
                                                    <option value="reason">Reason</option>
                                                    <option value="sent_date">Sent Date</option>
                                                    <option value="delivery_date">Delivery Date</option>
                                                </select>
                                            </div>
                                            <button type="button" class="btn btn-primary"
                                                id="exportSubmit">Export</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="company_list" class="table table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Voter Name</th>
                                    <th>Voter Share</th>
                                    <th>Email</th>
                                    <th>Contact No</th>
                                    <th>Email Status</th>
                                    <th>Reason</th>
                                    <th>Voter LoginID</th>
                                    <th>Voter Password</th>
                                    <th>Edit</th>
                                    <th>Resend</th>
                                    <th>SMS Resend</th>
                                    <th>Send Date Time</th>
                                    <th>Delivery Date Time</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('footer-script'); ?>
    <script src=" <?php echo e(asset('customdownload/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('plugins/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script src="<?php echo e(asset('plugins/select2/js/select2.full.min.js')); ?>"></script>
    <script>
        const memberUpdateRoute = "<?php echo e(route('member.update')); ?>";
        const csrf_token = "<?php echo e(csrf_token()); ?>";
        let hideColumn = <?php echo auth()->user()->type == 0 ? '1' : '0'; ?>;

        var table = $('#company_list').DataTable({
            processing: true,
            serverSide: true,
            "pageLength": 10,
            "columnDefs": [{
                "targets": [7, 8], // Index of the column you want to hide
                "visible": hideColumn
            }],
            ajax: "<?php echo e($memberReportRoute); ?>",
            columns: [{
                    data: 'DT_RowIndex',
                    'orderable': false,
                    'searchable': false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'share',
                    name: 'share'
                }, {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'phone',
                    name: 'phone',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'email_sent',
                    name: 'email_sent'
                },
                {
                    data: 'reason',
                    name: 'reason'
                },
                {
                    data: 'user_name',
                    name: 'user_name'
                },
                {
                    data: 'password',
                    name: 'password'
                },
                {
                    data: 'edit_btn',
                    name: 'edit_btn',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'resend_btn',
                    name: 'resend_btn',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'resend_btn_sms',
                    name: 'resend_btn_sms',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'sent_date',
                    name: 'sent_date'
                },
                {
                    data: 'delivery_date',
                    name: 'delivery_date'
                },

            ],

            "fnDrawCallback": function() {
                // $("input[data-bootstrap-switch]").each(function() {
                //     $(this).bootstrapSwitch();
                // })
            }
        });

        $("#member-form").validate({
            rules: {
                member_name: {
                    required: true,
                },

                member_share: {
                    required: true,
                },

                member_email: {
                    required: true,
                    email: true
                },
                // member_phone: {
                //     required: true,
                // }
            }
        });


        function openMemberFormModal(url = "") {
            $('#member-form').validate().resetForm();
            var targetForm = $("#member-form");
            $.ajax({
                type: "GET",
                url: url,
                success: function(res) {
                    if (typeof res["member"] !== "undefined") {
                        var memberDetails = res["member"];
                        $("#member-id").val(memberDetails["id"]);
                        $("#member-name").val(memberDetails["name"]);
                        $("#member-share").val(memberDetails["share"]);
                        $("#member-email").val(memberDetails["email"]);
                        $("#member-phone").val(memberDetails["phone"]);
                        // $('#is_active').prop("checked", memberDetails["is_active"]).change()
                        $("#add-member-modal").modal();
                    } else {
                        createMessage("Something went wrong please try again !", "error")
                    }
                },
            });
        }

        function memberFormSubmit() {
            if ($("#member-form").valid()) {
                member_id = $("#member-id").val();
                member_name = $("#member-name").val();
                member_share = $("#member-share").val();
                member_email = $("#member-email").val();
                member_phone = $("#member-phone").val();
                resolution_id = $("#resolution-id").val();
                // is_active = $('#is_active').prop("checked") ? 1 : 0;

                $.ajax({
                    type: "POST",
                    url: memberUpdateRoute,
                    data: {
                        _token: csrf_token,
                        member_id,
                        resolution_id,
                        member_name,
                        member_share,
                        member_email,
                        member_phone
                        // is_active
                    },
                    success: function(res) {
                        if (typeof res["success"] !== "undefined") {
                            createMessage(res["success"])
                            table.ajax.reload(null, false);
                        } else {
                            createMessage(res["error"], "error")

                        }
                        $("#add-member-modal").modal("hide");
                    },
                });
            }
        }


        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                placeholder: "Select fields to export",
                allowClear: true
            });

            // Open modal on button click
            $('#exportButton').click(function() {
                $('#exportModal').modal('show');
            });

            // Handle export form submit
            $('#exportSubmit').click(function(e) {
                e.preventDefault();

                // Get selected fields
                var selectedFields = $('#fields').val();

                if (selectedFields.length === 0) {
                    alert('Please select at least one field to export.');
                    return;
                }

                // Dynamically append the selected fields to the form and submit it
                $('#exportForm').submit();
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app.layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Dhaval\Web Development\LARAVEL\testproject2.0\resources\views/app/member/list.blade.php ENDPATH**/ ?>