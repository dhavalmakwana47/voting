
<?php $__env->startSection('page_title'); ?>
    User List
<?php $__env->stopSection(); ?>
<?php $__env->startSection('header-script'); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link href="<?php echo e(asset('customdownload/css/jquery.dataTables2.min.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('customdownload/css/jquery.dataTables.min.css')); ?>">
    <!-- jQuery -->
    

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content-body'); ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="resolution_list">Choose an Voting No for Voting Status
                </label>
                <select name="resolution" class="form-control select2" id="resolution_list"
                    data-placeholder="Select a EVSN">
                    <option value="0">Voting No.</option>
                    <?php $__currentLoopData = $resolutionArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resolution): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($resolution->id); ?>" <?php echo e(old('resolution') == $resolution->id ? 'selected' : ''); ?>>
                            <?php echo e($resolution->id); ?> (<?php echo e($resolution->company->name); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['resolution'];
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
        <div class="col-md-12" id="main_div" style="display: none">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="font-size: 21px;">View Voting Status</h3>

                    
                    
                </div>

                
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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('footer-script'); ?>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src=" <?php echo e(asset('customdownload/js/jquery.dataTables.min.js')); ?>"></script>
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
                'url': "<?php echo e(route('vote.list')); ?>",
                'data': {
                    "_token": "<?php echo e(csrf_token()); ?>",
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
                url: "<?php echo e(route('vote.share_count')); ?>",
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app.layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Dhaval\Web Development\LARAVEL\testproject2.0\resources\views/app/vote/votingstatus.blade.php ENDPATH**/ ?>