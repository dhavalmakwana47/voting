<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Voter Mail</title>
    <style>
        table {
            border-collapse: collapse;
            border: 1px solid black;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
        }
    </style>
</head>

<body>
    <p>
        Dear Voter,</p>
    <?php if($member->resolution->is_updated): ?>
        <b>Please find below updated information on the ongoing voting process below.</b>
    <?php endif; ?>
    <p>
        We hope this email finds you well. As a valued Voter of <?php echo e($member->company->name); ?> , your participation is
        crucial in
        shaping the future direction of <?php echo e($member->company->name); ?>.
    </p>
    <p>
        Your input and vote are essential in determining the outcome of this matter, and we encourage you to review the
        details provided below:
    </p>
    <b> Website:</b> <a href="https://indiaevoting.com/voter/login">https://indiaevoting.com/voter/login</a>
    <h3>Login Details</h3>
    <table>
        <tr>
            <td>Voter Name</td>
            <td>Username</td>
            <td>Password</td>
            <td>Voting % or Amount</td>
        </tr>
        <?php if(isset($members)): ?>
            <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($member->name); ?></td>
                    <td><?php echo e($member->user_name); ?></td>
                    <td><?php echo e($member->password); ?></td>
                    <td><?php echo e($member->share); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <tr>
                <td><?php echo e($member->name); ?></td>
                <td><?php echo e($member->user_name); ?></td>
                <td><?php echo e($member->password); ?></td>
                <td><?php echo e($member->share); ?></td>
            </tr>
        <?php endif; ?>

    </table>


    <h3>E-Voting Date & Time Schedule</h3>
    <table>
        <tr>
            <td>E-Voting Start Date & Time</td>
            <td>E-Voting End Date & Time</td>
        </tr>
        <tr>
            <td><?php echo e(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $member->resolution->start_date)->format('d-M-Y h:i A')); ?>

            </td>
            <td><?php echo e(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $member->resolution->end_date)->format('d-M-Y h:i A')); ?>

            </td>
        </tr>
    </table>


    <h3>E-Voting Description Details</h3>
    <table>
        <tr>
            <td>Item No.</td>
            <td>Description</td>
        </tr>
        <?php $__currentLoopData = $member->resolution->resolution_details()->orderBy('index')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resolution_detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($resolution_detail->id); ?></td>
                <td><?php echo nl2br(e($resolution_detail->description)); ?> <br>
                    <a href="<?php echo e(route('memberresolutiondetails.download', Crypt::encrypt($resolution_detail->id))); ?>" class="linkId">View
                        Information</a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </table>

    <p>How to Vote:</p>
    <p> 1. Log in to your Member Account: Visit <a
            href="<?php echo e(route('member.login')); ?>">www.indiaevoting.com/voter/login</a> and log in account using your
        username
        and
        password or Mobile/email OTP</p>
    <p> 2. Access the Voting Section: Once logged in, navigate to the "Voting" or "Voter Voting" section of the website.
        After the Click the E-voting Number.</p>

    <p>3. Review the Information: Take the time to carefully review the details of the issue, proposal, or event up for
        vote. Additional documents or resources may be provided for your reference.</p>


    <p>
        4. Cast Your Vote: Follow the instructions provided on the voting page to submit your vote electronically. Your
        vote
        is confidential and will be securely recorded.
    </p>

    <p>For <?php echo e($member->company->name); ?></p>
</body>

</html>
<?php /**PATH C:\Dhaval\Web Development\LARAVEL\testproject2.0\resources\views/emails/voter_email.blade.php ENDPATH**/ ?>