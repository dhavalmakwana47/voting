<?php
    use Illuminate\Support\Facades\Crypt;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Voting Update</title>
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
   

    <p> Dear <?php echo e($resolution->user->name); ?>,<br></p>

    <p> I am pleased to inform you that the electronic voting (e-voting) process for the upcoming
        "<?php echo e($resolution->company->name); ?>” has been
        officially approved and activated. Your careful consideration and approval of this crucial step are greatly
        appreciated.</p>

    <h2>Please find below updated information on the ongoing voting process below.</h2>
    <b>Voting Details:</b>
    <ul>
        <li><b>Voting Period:</b>
            [<?php echo e(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->start_date)->format('d-M-Y h:i A')); ?>] to
            [<?php echo e(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->end_date)->format('d-M-Y h:i A')); ?>]</li>
        <li><b>Participants:</b> [<?php echo e($resolution->members->count()); ?>]</li>
    </ul>


    <h2>E-Voting Description Details</h2>
    <table>
        <tr>
            <th>Item No</th>
            <th>E-Voting Description</th>
        </tr>

        <?php $__currentLoopData = $resolution->resolution_details()->orderBy('index')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resolution_detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($resolution_detail->id); ?></td>
                <td><?php echo nl2br(e($resolution_detail->description)); ?> <br>
                    <a href="<?php echo e(route('memberresolutiondetails.download', Crypt::encrypt($resolution_detail->id))); ?>" class="linkId">View
                        Information</a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </table>



    <p>Should you have any questions or require additional assistance, please do not hesitate to contact me at Tushar
        Parikh (7990822351)</p>
    <p>Thank you for your commitment to upholding the democratic values of our organization.</p>
    <p>Best regards,</p>
    <p>For India E-Voting Services</p>
</body>
</html><?php /**PATH C:\Dhaval\Web Development\LARAVEL\testproject2.0\resources\views/emails/votingupdatemail.blade.php ENDPATH**/ ?>