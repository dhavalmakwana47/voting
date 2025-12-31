<?php
    if (isset($resolution)) {
        $memberListRoute = route('member.list', $resolution->id);
    }
?>

<?php $__env->startSection('page_title'); ?>
    Admin-Assign
<?php $__env->stopSection(); ?>
<?php $__env->startSection('header-script'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('plugins/select2/css/select2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('plugins/daterangepicker/daterangepicker.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('custom/resolution/css/addupdate.css')); ?>">
    <link href="<?php echo e(asset('customdownload/css/jquery.dataTables2.min.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('customdownload/css/jquery.dataTables.min.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content-body'); ?>
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
                    <form action="<?php echo e(route('member.store')); ?>" id="member-form" data-type="add">
                        <input type="hidden" id="member-id" value="">
                        <?php if(isset($resolution)): ?>
                            <input type="hidden" id="resolution-id" value="<?php echo e($resolution->id); ?>">
                        <?php endif; ?>
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
                        <?php echo e(isset($resolution) ? 'Edit' : 'New'); ?> Voting
                    </h3>
                </div>
                <?php $__errorArgs = ['resolution_files.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <script>
                        alert("<?php echo e($message); ?>")
                    </script>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                
                <form action="<?php echo e(isset($resolution) ? route('voting.update', $resolution->id) : route('voting.store')); ?>"
                    method="POST" enctype="multipart/form-data" id="resolution-form">
                    <?php if(isset($resolution)): ?>
                        <?php echo method_field('PATCH'); ?>
                    <?php endif; ?>
                    <?php echo csrf_field(); ?>
                    <div class="card-body">
                        <div class="row">
                            <?php if(isset($resolution) && auth()->user()->type == '0'): ?>
                                <div class="col-sm-6 mb-2">
                                    <label for="resolution_meetingdate">Active Status</label>

                                    <div>
                                        <input type="checkbox" data-bootstrap-switch="" name="is_active"
                                            <?php echo e($resolution->is_active ? 'checked' : ''); ?>>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="col-sm-6 mb-2">
                                <label for="resolution_meetingdate">Is Modifiable</label>

                                <div>
                                    <input type="checkbox" data-bootstrap-switch="" name="is_modifiable"
                                        <?php echo e(isset($resolution) && $resolution->is_modifiable ? 'checked' : ''); ?>>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <label>Voting Type*</label>
                                <div class="input-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="0" name="evsn_type"
                                            <?php echo e(old('evsn_type') == '0' ? 'checked' : ''); ?>

                                            <?php echo e((isset($resolution) && $resolution->evsn_type) == '0' ? 'checked ' : (empty(old('evsn_type')) ? 'checked' : '')); ?>

                                            <?php echo e(isset($resolution) ? 'disabled' : ''); ?>>
                                        <label class="form-check-label">Normal Voting</label>
                                    </div>&nbsp;&nbsp;
                                    <!-- <div class="form-check">
                                                                            <input class="form-check-input" type="radio" value="1" name="evsn_type"
                                                                                <?php echo e(old('evsn_type') == '1' ? 'checked' : ''); ?>

                                                                                <?php echo e(isset($resolution) && $resolution->evsn_type == '1' ? 'checked ' : ''); ?> <?php echo e(isset($resolution) ? 'disabled' : ''); ?>>
                                                                            <label class="form-check-label">Instruction</label>
                                                                        </div>&nbsp;&nbsp;
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" value="2" name="evsn_type"
                                                                                <?php echo e(old('evsn_type') == '2' ? 'checked' : ''); ?>

                                                                                <?php echo e(isset($resolution) && $resolution->evsn_type == '2' ? 'checked ' : ''); ?> <?php echo e(isset($resolution) ? 'disabled' : ''); ?>>
                                                                            <label class="form-check-label">Option Voting</label>
                                                                        </div> -->
                                    <?php $__errorArgs = ['evsn_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small style="color:red; font-weight:600;"><?php echo e($message); ?></small>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="company_list">Company Name*</label>
                                    <select name="company" class="form-control select2" id="company_list"
                                        data-placeholder="Select a Company" data-error="#company-error"
                                        <?php echo e($active); ?>>
                                        <option value=""></option>
                                        <?php if(isset($resolution)): ?>
                                            <option value=" <?php echo e($resolution->company->id); ?>" selected>
                                                <?php echo e($resolution->company->name); ?></option>
                                        <?php else: ?>
                                            <?php $__currentLoopData = $companyArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($company->id); ?>"
                                                    <?php echo e(in_array($company->id, [old('company'), isset($resolution) ? $resolution->company_id : 0]) ? 'selected' : ''); ?>>
                                                    <?php echo e($company->name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                    <?php $__errorArgs = ['company'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small style="color:red; font-weight:600;"><?php echo e($message); ?></small>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <span class="error" id="company-error"></span>

                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="meeting_details">Meeting Details</label>
                                    <input class="form-control" placeholder="Write details here" type="text"
                                        type="text" name="meeting_details" id="meeting_details"
                                        value="<?php echo e(old('meeting_details', isset($resolution) ? $resolution->meeting_details : '')); ?>">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="resolution_startdate">Voting Start Date*</label>
                                    <div class="input-group date" id="resolution_startdate" data-target-input="nearest">
                                        <input type="text"
                                            value="<?php echo e(old('start_date', isset($resolution) ? Carbon\Carbon::parse($resolution->start_date)->format('d/m/Y g:i A') : '')); ?>"
                                            name="start_date" class="form-control datetimepicker-input"
                                            data-target="#resolution_startdate" data-error="#startdate-error"
                                            data-toggle="datetimepicker" autocomplete="off" <?php echo e($active); ?> />
                                        <div class="input-group-append" data-target="#resolution_startdate"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                    <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small style="color:red; font-weight:600;"><?php echo e($message); ?></small>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <span class="error" id="startdate-error"></span>

                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="resolution_enddate">Voting End Date*</label>
                                    <div class="input-group date" id="resolution_enddate" data-target-input="nearest">
                                        <input type="text"
                                            value="<?php echo e(old('end_date', isset($resolution) ? Carbon\Carbon::parse($resolution->end_date)->format('d/m/Y g:i A') : '')); ?>"
                                            name="end_date" class="form-control datetimepicker-input"
                                            data-error="#enddate-error" data-target="#resolution_enddate"
                                            data-toggle="datetimepicker" autocomplete="off" />
                                        <div class="input-group-append" data-target="#resolution_enddate"
                                            data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                    <?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small style="color:red; font-weight:600;"><?php echo e($message); ?></small>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                                                                                value="<?php echo e(old('meeting_date', isset($resolution) ? Carbon\Carbon::parse($resolution->meeting_date)->format('d/m/Y g:i A') : '')); ?>"
                                                                                name="meeting_date" class="form-control datetimepicker-input"
                                                                                data-error="#meeting-date-error" data-target="#resolution_meetingdate"  data-toggle="datetimepicker" autocomplete="off" />
                                                                            <div class="input-group-append" data-target="#resolution_meetingdate"
                                                                                data-toggle="datetimepicker">
                                                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                                            </div>
                                                                        </div>
                                                                        <?php $__errorArgs = ['meeting_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <small style="color:red; font-weight:600;"><?php echo e($message); ?></small>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                                        <span class="error" id="meeting-date-error"></span>

                                                                    </div>
                                                                </div> -->
                            <div class="col-sm-12 ">
                                <a href="<?php echo e(route('voting.sample-download')); ?>"
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
                                    <?php if(isset($resolution)): ?>
                                        <a href="<?php echo e(asset('uploads/members_files/' . $resolution->member_file)); ?>">View</a>
                                    <?php endif; ?>

                                    <?php $__errorArgs = ['member_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <small style="color:red; font-weight:600;"><?php echo e($message); ?></small>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <span class="error" id="member-file-error"></span>

                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group mt-4">
                                    <button type="button" class="btn btn-primary" onclick="uploadFile()"
                                        id="member-verify-btn">Upload &
                                        Verify</button>
                                    <button type="button" class="btn btn-primary" onclick="restExcelFile()"
                                        <?php echo e($active); ?>>Reset
                                        Excel</button>
                                </div>
                            </div>
                            <?php if(isset($resolution) && !empty($resolution->members)): ?>
                                <div class="col-sm-10 mb-3" id="add-member-section">
                                    <button type="button" class="btn btn-primary btn-sm"
                                        onclick="openMemberFormModal('add')" <?php echo e($active); ?>>Add New Voter</button>
                                </div>
                            <?php endif; ?>
                            <div class="col-12">
                                <table id="excel_table">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Name</th>
                                            <th>Share</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <?php if(isset($resolution) && !empty($resolution->members)): ?>
                                                <th id="action-column">Action</th>
                                            <?php endif; ?>
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
                                        disabled name="description[]" <?php echo e($active); ?>><?php echo e(isset($resolutionDetailsArr) ? $resolutionDetailsArr[0]->description : ''); ?></textarea>
                                </div>
                                <div class="col-4">
                                    <?php if(isset($resolutionDetailsArr)): ?>
                                        <input type="hidden" name="resolution_details_id[]"
                                            id="resolution_details_id-<?php echo e($resolutionDetailsArr[0]->id); ?>"
                                            value="<?php echo e($resolutionDetailsArr[0]->id); ?>">
                                    <?php endif; ?>
                                    <input type="file"
                                        value="<?php echo e(isset($resolutionDetailsArr) ? asset('uploads/resolution_details_files/' . $resolutionDetailsArr[0]->file_name) : ''); ?>"
                                        class="custom-file-input required-section" disabled
                                        <?php if(isset($resolutionDetailsArr)): ?> name="<?php echo e('resolution_files[' . $resolutionDetailsArr[0]->id . ']'); ?>"
                                        <?php else: ?>
                                        name="resolution_files[]" <?php endif; ?>
                                        <?php if(isset($resolutionDetailsArr)): ?> data-id="<?php echo e($resolutionDetailsArr[0]->id); ?>" <?php endif; ?>
                                        data-error="#member-file-error-0" onchange="fileChange(this)"
                                        <?php echo e($active); ?>>
                                    <label
                                        class="custom-file-label"><?php echo e(isset($resolutionDetailsArr) ? $resolutionDetailsArr[0]->file_name : 'Choose file'); ?></label>
                                    <br>
                                    <span class="error" id="member-file-error-0"></span>
                                </div>
                                <div class="col-2">
                                    <?php if(isset($resolutionDetailsArr)): ?>
                                        <a href="<?php echo e(route('memberresolutiondetails.download', Crypt::encrypt($resolutionDetailsArr[0]->id))); ?>"
                                            class="btn btn-success">View</a>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-primary required-section" disabled
                                        id="rowAdder" onclick="AddResolutionDetalisRaw()" <?php echo e($active); ?>>Add
                                        More</button>
                                </div>
                                <br>
                            </div>

                            <?php if(isset($resolutionDetailsArr)): ?>
                                <input type="hidden" name="id" value="<?php echo e($resolution->id); ?>">
                                <?php $__currentLoopData = $resolutionDetailsArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($loop->index > 0): ?>
                                        <div class="row file-wrapper">
                                            <div class="col-6">

                                                <input type="hidden" name="resolution_details_id[]"
                                                    id="resolution_details_id-<?php echo e($value->id); ?>"
                                                    value="<?php echo e($value->id); ?>">
                                                <textarea cols="60" rows="10" class="resolution_description required-section"
                                                    name="<?php echo e('description[' . $loop->index . ']'); ?>" <?php echo e($active); ?>><?php echo e($value->description); ?></textarea>
                                            </div>
                                            <div class="col-4">
                                                <input type="file"
                                                    value="<?php echo e(asset('uploads/resolution_details_files/' . $value->file_name)); ?>"
                                                    class="custom-file-input required-section" onchange="fileChange(this)"
                                                    data-error="<?php echo e('#member-file-error-' . $loop->index); ?>"
                                                    name="<?php echo e('resolution_files[' . $value->id . ']'); ?>"
                                                    data-id="<?php echo e($value->id); ?>" <?php echo e($active); ?>>
                                                <label class="custom-file-label"><?php echo e($value->file_name); ?></label>
                                                <br>
                                                <span class="error" id="<?php echo e('member-file-error-' . $loop->index); ?>"></span>
                                            </div>
                                            <div class="col-2">
                                                <a href="<?php echo e(route('memberresolutiondetails.download', Crypt::encrypt($value->id))); ?>"
                                                    class="btn btn-success">View</a>
                                                <?php if($value->votes->count() < 1): ?>
                                                    <button type="button"
                                                        class="btn btn-danger required-section reolution-delete-btn"
                                                        <?php echo e($active); ?>>Delete</button>
                                                <?php endif; ?>
                                            </div>
                                            <br>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>

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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('footer-script'); ?>
    <script src="<?php echo e(asset('plugins/select2/js/select2.full.min.js')); ?>"></script>
    <script src="<?php echo e(asset('plugins/moment/moment.min.js')); ?>"></script>
    <script src="<?php echo e(asset('plugins/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script src=" <?php echo e(asset('customdownload/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>


    <script>
        const csrf_token = "<?php echo e(csrf_token()); ?>";
        const memberFileValidationRoute = "<?php echo e(route('voting.upload')); ?>";
        let randomCount = 0;
        <?php if(isset($resolutionDetailsArr)): ?>
            randomCount = "<?php echo e(count($resolutionDetailsArr)); ?>";
            memberUpdateRoute = "<?php echo e(route('member.update')); ?>";
            let hideColumn = <?php echo $active == 'disabled' ? '0' : '1'; ?>;

            var table = $('#excel_table').DataTable({
                processing: true,
                serverSide: true,
                "pageLength": 10,
                ajax: "<?php echo e($memberListRoute); ?>",
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
        <?php else: ?>
        <?php endif; ?>
        var memberDeleteRoute = "<?php echo e(route('member.delete')); ?>";
    </script>
    <script src="<?php echo e(asset('custom\resolution\js\addupdate.js')); ?>"></script>
    <script>
        <?php if(isset($resolution)): ?>
            $(document).ready(function() {
                $('#member_file').next().text("<?php echo e($resolution->member_file); ?>");
                $("#member-verify-btn, #member_file").attr("disabled", "");
                <?php if($active != 'disabled'): ?>
                    $('#rowAdder, #resolution_description').removeAttr('disabled')
                    $('[data-error="#member-file-error-0"]').removeAttr('disabled')
                <?php endif; ?>
                addResolutionDetailsValidation();
                $('#excel_table').show()
            })
        <?php endif; ?>
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app.layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Dhaval\Web Development\LARAVEL\testproject2.0\resources\views/app/resolution/addupdate.blade.php ENDPATH**/ ?>