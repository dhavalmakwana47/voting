
<?php $__env->startSection('page_title'); ?>
    Voting List
<?php $__env->stopSection(); ?>
<?php $__env->startSection('header-script'); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link href="<?php echo e(asset('customdownload/css/jquery.dataTables2.min.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('customdownload/css/jquery.dataTables.min.css')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content-body'); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="font-size: 21px;">Voting Report</h3>
                </div>

                
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="company_list">Company</label>
                                <select name="company" class="form-control select2" id="company_list"
                                    data-placeholder="Select a Company">
                                    <?php $__currentLoopData = $companyArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($company->id); ?>"
                                            <?php echo e(old('company') == $company->id ? 'selected' : ''); ?>><?php echo e($company->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('footer-script'); ?>
    <script src=" <?php echo e(asset('customdownload/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#company_list').select2({
                placeholder: "Select an option",
                allowClear: true
            });
        });
        const user_type = "<?php echo e(auth()->user()->type); ?>"
        var table = $('#voting_list').DataTable({
            processing: true,
            serverSide: true,
            "pageLength": 10,
            ajax: {
                url: "<?php echo e(route('votingreport.index')); ?>",
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app.layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Dhaval\Web Development\LARAVEL\testproject2.0\resources\views/app/votingreport/list.blade.php ENDPATH**/ ?>