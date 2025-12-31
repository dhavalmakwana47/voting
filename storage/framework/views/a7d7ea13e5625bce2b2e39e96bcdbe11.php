
<?php $__env->startSection('header-script'); ?>
    <style>
        .custom-border {
            border: 2px solid #ccc;
            border-radius: 0.25rem;
            padding: 1rem;
            font-weight: bold;
        }

        .voting-info {
            background-color: #f9f9f9;
            /* Light background for better contrast */
            padding: 15px;
            /* Add some padding around the container */
            border-radius: 5px;
            /* Rounded corners for a softer look */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            /* Subtle shadow for depth */
            margin-bottom: 20px;
            /* Space below the voting info section */
        }

        .voting-info .row {
            margin-bottom: 10px;
            /* Space between each row */
        }

        .label {
            font-weight: bold;
            /* Make the labels bold */
            color: #333;
            /* Darker color for better readability */
        }

        .data {
            color: #555;
            /* Slightly lighter color for data text */
        }
    </style>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link href="<?php echo e(asset('customdownload/css/jquery.dataTables2.min.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('customdownload/css/jquery.dataTables.min.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body-content'); ?>
    <div class="modal fade" id="add-member-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="form-title">Voting Instruction</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="votingInstr">
                        <p>Kindly vote here.</p>
                        <p>Only when the <strong>Yes</strong> , <strong>No</strong>, or
                            <strong>Abstain</strong> option is selected for each Agenda will votes be taken into action.
                        </p>
                        <p>Just before clicking <strong>Submit</strong>," please double-check your votes</p>
                        <p>Your vote will be recorded for each Agenda you have chosen once you click
                            <strong>Submit</strong>and
                            you can modify it by adjusting the Schedule Voting Start and End Date & Time.
                        </p>
                        <p>It is implied that you have not voted for a Agenda when you leave a selection blank.</p>
                        <p>Click the <strong>Agenda File Link</strong>
                            below to view the Agenda file.
                        </p>
                    </div>
                </div>

            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title" style="font-size: 21px;">Voter Voting Screen</h3>
                        </div>

                        
                        <div class="card-body">

                            <button type="button" class="btn btn-danger" data-toggle="modal"
                                data-target="#add-member-modal">Instruction for Voter !</button>

                        </div>

                        <div class="voting-info">
                            <div class="row">
                                <div class="col-md-3 label"><strong>Company Name:</strong></div>
                                <div class="col-md-9 data"><?php echo e($resolution->company->name); ?></div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 label"><strong>Voting No:</strong></div>
                                <div class="col-md-9 data"><?php echo e($resolution->id); ?></div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 label"><strong>Voting Time-Line:</strong></div>
                                <div class="col-md-9 data">
                                    From <?php echo e(Carbon\Carbon::parse($resolution->start_date)->format('d-M-Y g:i A')); ?>

                                    to <?php echo e(Carbon\Carbon::parse($resolution->end_date)->format('d-M-Y g:i A')); ?>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 label"><strong>Total Agenda:</strong></div>
                                <div class="col-md-9 data">
                                    <span id="totalVotingCount"><?php echo e(isset($voteArr) ? count($voteArr) : 0); ?></span>
                                    <span>/<?php echo e($resolution->resolution_details->count()); ?></span>
                                </div>
                            </div>
                        </div>

                        <form action="<?php echo e(route('vote.store')); ?>" method="POST" id="voting_form">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="member_id" value="<?php echo e($member_id); ?>">
                            <div class="table-responsive">
                                <table id="company_list" class="table table-bordered yajra-datatable">
                                    <thead>
                                        <tr>
                                            <th>Item No.</th>
                                            <th>Voting Information</th>
                                            
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $resolution->resolution_details()->orderBy('index')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $vote = $row->votes->where('member_id', $member_id)->first();
                                            ?>
                                            <?php if(isset($vote)): ?>

                                                <input type="hidden" name="vote_id[]" value="<?php echo e($vote->id); ?>">
                                            <?php else: ?>
                                                <input type="hidden" name="vote_id[]" value="0">
                                            <?php endif; ?>

                                            <tr>
                                                <td><?php echo e($loop->index + 1); ?></td>
                                                <td><?php echo nl2br(e($row->description)); ?> <br>
                                                    <a class="btn btn-outline-secondary m-2"
                                                        href="<?php echo e(route('memberresolutiondetails.download', Crypt::encrypt($row->id))); ?>"
                                                        class="linkId"><i class="fas fa-file-pdf"></i> View
                                                        Information</a>
                                                    <div class="voting_input_section m-2 custom-border">
                                                        <b>Choose Options:-</b>
                                                        <input type="hidden" class="evsnidischecked"
                                                            name="evsnidischecked"
                                                            id="evsnischecked_<?php echo e($row->id); ?>"
                                                            value="<?php echo e(isset($vote) ? 'Y' : 'N'); ?>">
                                                        <div class="choiceque" id="choiceQueId">
                                                            <div class="form-check form-check-inline"
                                                                id="radioYesdiv_<?php echo e($row->id); ?>">
                                                                <input type="radio"
                                                                    class="form-check-input voting_input selectyes resolution_choice<?php echo e($row->id); ?>"
                                                                    name="resolution_choice[<?php echo e($row->id); ?>]"
                                                                    id="radioYes_<?php echo e($row->id); ?>" value="YES"
                                                                    onclick="selectAllYesNo(<?php echo e($row->id); ?>)"
                                                                    <?php echo e(isset($vote) && $vote->resolution_choice == 'YES' ? 'checked' : ''); ?>>
                                                                <label class="form-check-label"
                                                                    for="radioYes_<?php echo e($row->id); ?>">I agree to the
                                                                    Agenda (Yes)</label>
                                                            </div>
                                                            <div class="form-check form-check-inline"
                                                                id="radioNodiv_<?php echo e($row->id); ?>">
                                                                <input type="radio"
                                                                    class="form-check-input voting_input selectno resolution_choice<?php echo e($row->id); ?>"
                                                                    name="resolution_choice[<?php echo e($row->id); ?>]"
                                                                    id="radioNo_<?php echo e($row->id); ?>" value="NO"
                                                                    onclick="selectAllYesNo(<?php echo e($row->id); ?>)"
                                                                    <?php echo e(isset($vote) && $vote->resolution_choice == 'No' ? 'checked' : ''); ?>>
                                                                <label class="form-check-label"
                                                                    for="radioNo_<?php echo e($row->id); ?>">I disagree to the
                                                                    Agenda (No)</label>
                                                            </div>
                                                            <div class="form-check form-check-inline"
                                                                id="radioAbstaindiv_<?php echo e($row->id); ?>">
                                                                <input type="radio"
                                                                    class="form-check-input voting_input selectabstain resolution_choice<?php echo e($row->id); ?>"
                                                                    name="resolution_choice[<?php echo e($row->id); ?>]"
                                                                    id="radioAbstain_<?php echo e($row->id); ?>"
                                                                    value="ABSTAIN"
                                                                    onclick="selectAllYesNo(<?php echo e($row->id); ?>)"
                                                                    <?php echo e(isset($vote) && $vote->resolution_choice == 'ABSTAIN' ? 'checked' : ''); ?>>
                                                                <label class="form-check-label"
                                                                    for="radioAbstain_<?php echo e($row->id); ?>">I Abstain
                                                                    to
                                                                    the Agenda</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>


                                                
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <?php if($resolution->is_modifiable || !$vote_count): ?>
                                <div class="text-center" id="backdivId"
                                    <?php if(!$vote_count): ?> style="display: none" <?php endif; ?>>
                                    <button type="button" id="backId" class="btn btn-primary"><i
                                            class="glyphicon glyphicon-edit"></i> Modify</button>

                                    <button type="submit" id="submitForm" class="btn btn-primary"> <i
                                            class="glyphicon glyphicon-ok"></i> Submit</button>
                                </div>
                                <div class="text-center" id="continuedivId"
                                    <?php if($vote_count): ?> style="display: none" <?php endif; ?>>
                                    <button type="button" id="continueId" class="btn btn-primary"
                                        onclick="continueForModify()">
                                        Submit <i class="glyphicon glyphicon-arrow-right"></i>
                                    </button>
                                    <button type="button" class="btn btn btn-warning" id="clear-all"
                                        <?php if($vote_count): ?> style="display: none" <?php endif; ?>>
                                        <i class="glyphicon glyphicon-refresh"></i> Reset All
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('footer-script'); ?>
<script src="<?php echo e(asset('custom\member\js\voting_screen.js')); ?>"></script>
<?php if($vote_count): ?>
    <script>
        $(".voting_input").each(function() {
            if (!$(this).is(":checked")) {
                $(this).hide();
                $(this).next().hide();
            }
        });
    </script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app.member.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Dhaval\Web Development\LARAVEL\testproject2.0\resources\views/app/member/voting_screen.blade.php ENDPATH**/ ?>