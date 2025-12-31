<?php

//Convert date from UTC to IST
date_default_timezone_set('Asia/Kolkata');
define('IN_PRGM', true);
define('IN_ADMIN', true);
define('ROOT_PATH', 'https://indiaevoting.com/bounce_mail/');

$logfile = 'bounce_res-' . date('Y-m-d') . '.log';
$password = '$b3SQo3G=u?';
$DB = mysqli_connect("localhost", "u698001614_indvoting", "Sm8qjx^wa!5", "u698001614_indvoting");
if ($DB) {
    echo 'db connected';
    error_log("DB connection successs", 3, $logfile);

} else {
    echo 'failed';
    error_log("DB connection failed", 3, $logfile);
}

//header("Content-Type: application/json; charset=UTF-8");

$log = "[" . gmdate(DATE_RFC822) . "] msg:------------ Start To Present Email Satus --------------------\n";
error_log($log, 3, $logfile);

$postBody = file_get_contents('php://input');
$log = "[" . gmdate(DATE_RFC822) . "] msg: Post Body  = \n " . print_r($postBody, true);
error_log($log, 3, $logfile);

// JSON decode the body to an array of message data
$response_data = json_decode($postBody, true);
$date = date("Y-m-d H:i:s");

if ($response_data) {
    $log = "[" . gmdate(DATE_RFC822) . "] msg: Coming to Show Message  = \n " . $response_data['Message'] . "\n";
    error_log($log, 3, $logfile);

    if ($response_data['Type'] == "Notification") {
        $message = json_decode($response_data['Message'], true);
        $log = "[" . gmdate(DATE_RFC822) . "] msg: Message Data Decode  = \n " . print_r($message, true) . "\n";
        error_log($log, 3, $logfile);

        if ($message['notificationType'] == "Delivery") {
            $log = "[" . gmdate(DATE_RFC822) . "] msg: In Delivery if Condition  \n";
            error_log($log, 3, $logfile);
            $emailSent = 'Y';
            $status = $message['notificationType'];
            $i = 0;
            $emailAddress = $message['delivery']['recipients'][0];
            $resname = $message['mail']['headers'][8]['name'];
            if ($resname == 'resolutionid') {
                $resolutionId = $message['mail']['headers'][8]['value'];
            } else {
                $resolutionId = 0;
            }

            /* code added By Smith  */
            //For ReminderDetailID -- to update status of remider mail
            // if ($resname == 'REIMNDERDETAILID') {
            //     $remindermailDetailId = $message['mail']['headers'][8]['value'];
            // } else {
            //     $remindermailDetailId = 0;
            // }

            // //PHPMAILER INDEX FOR RESOLUTION
            if ($resolutionId == 0) {
                $resname = $message['mail']['headers'][10]['name'];
                if ($resname == 'resolutionid') {
                    $log = "\n[" . gmdate(DATE_RFC822) . "] msg:  = Coming into PHPMAILER RESOLUTION ID IF \n";
                    error_log($log, 3, $logfile);
                    $resolutionId = $message['mail']['headers'][10]['value'];
                }
            }

            // if ($remindermailDetailId == 0) {
            //     //For ReminderDetailID -- to update status of remider mail
            //     if ($resname == 'REIMNDERDETAILID') {
            //         $remindermailDetailId = $message['mail']['headers'][10]['value'];
            //     } else {
            //         $remindermailDetailId = 0;
            //     }
            // }

            $memberId = 0;
            if (isset($message['mail']['headers'][10]['name'])) {
                $memname = $message['mail']['headers'][10]['name'];
                if ($memname == 'MEMBERID') {
                    $memberId = $message['mail']['headers'][10]['value'];
                } else {
                    $memberId = 0;
                }
            }
            if (empty($memberId) || trim($memberId) == '') {
                $memberId = 0;
            }

            $log = "\n[" . gmdate(DATE_RFC822) . "] msg:  = " . var_dump($message['delivery']['smtpResponse']) . "\n ";
            error_log($log, 3, $logfile);

            //get region name
            $regname = $message['mail']['headers'][11]['name'];
            if ($regname == 'REGION') {
                $RegionName = $message['mail']['headers'][11]['value'];
            } else {
                $RegionName = 'NULL';
            }

            $reason = addslashes(trim($message['delivery']['smtpResponse']));
            $sql = "INSERT into email_status(resolution_id,emaildate,emailid,status,reason)
                    values (" . $resolutionId . ",'" . $date . "','" . $emailAddress . "','" . $status . "','" . $reason . "')";

            // $sql = "INSERT into EMAIL_STATUS(EMAILDATE,RESOLUTIONID,MEMBERID,EMAILID,STATUS,REASON,REGION)values ('" . $date . "'," . $resolutionId . "," . $memberId . ",'" . $emailAddress . "','" . $status . "','" . $reason . "','" . $RegionName . "')";

            $log = "[" . gmdate(DATE_RFC822) . "] msg: SQL INSERT query  = " . $sql . "\n";
            error_log($log, 3, $logfile);

            $resultInsertNew = $DB->query($sql);

            if ($resultInsertNew) {
                $log = "[" . gmdate(DATE_RFC822) . "] msg: Inserted into email_status table success  = \n";
                error_log($log, 3, $logfile);
            } else {
                $log = "[" . gmdate(DATE_RFC822) . "] msg: Not inserted in to table   = \n";
                error_log($log, 3, $logfile);
            }

            /* Add By Smith
            Update Reason and status where Resaolution ID and Membre ID
             */
            if ($resolutionId > 0 && $emailAddress != null) {
                // $update = "UPDATE SHAREHOLDERMASTER SET REASON = '" . $status . "', EMAILSENT = '" . $emailSent . "', DELIVERYDATE = '" . $date . "' WHERE RESOLUTIONID = '" . $resolutionId . "' AND MEMBEREMAIL = '" . $emailAddress . "'";
                $update = "UPDATE members
                SET status = '" . $status . "', reason = '" . $status . "', delivery_date = '" . $date . "'
                WHERE resolution_id  = '" . $resolutionId . "' AND email = '" . $emailAddress . "'";

                $updateShareHolder = $DB->query($update);

                $log = "[" . gmdate(DATE_RFC822) . "] msg: SQL Update query  = " . $update . "\n";
                error_log($log, 3, $logfile);

                if ($updateShareHolder) {
                    $log = "[" . gmdate(DATE_RFC822) . "] msg: members - Update Table Successfully. \n";
                    error_log($log, 3, $logfile);
                } else {
                    $log = "[" . gmdate(DATE_RFC822) . "] msg: members - date Not Updated in Shareholder. \n";
                    error_log($log, 3, $logfile);
                }
            }
           

        }

        if ($message['notificationType'] == "Bounce") {
            $log = "[" . gmdate(DATE_RFC822) . "] msg: In bounce if Condition  = \n";
            error_log($log, 3, $logfile);
            $emailSent = 'E';
            $status = $message['notificationType'];
            $bounce = $message['bounce'];
            $bounceType = $bounce['bounceType'];
            $bounceSubType = $bounce['bounceSubType'];
            $i = 0;
            $resname = $message['mail']['headers'][8]['name'];
            if ($resname == 'resolutionid') {
                $resolutionId = $message['mail']['headers'][8]['value'];
            } else {
                $resolutionId = 0;
            }

            /* code added By smith at 11Jan2021 */
            //For ReminderDetailID -- to update status of remider mail
            // if ($resname == 'REIMNDERDETAILID') {
            //     $remindermailDetailId = $message['mail']['headers'][8]['value'];
            // } else {
            //     $remindermailDetailId = 0;
            // }

            //PHPMAILER INDEX FOR RESOLUTION
            if ($resolutionId == 0) {
                $resname = $message['mail']['headers'][10]['name'];
                if ($resname == 'resolutionid') {
                    $log = "\n[" . gmdate(DATE_RFC822) . "] msg:  = Coming into PHPMAILER RESOLUTION ID IF \n";
                    error_log($log, 3, $logfile);
                    $resolutionId = $message['mail']['headers'][10]['value'];
                }
            }

            /* code added By smith at  */
            //For ReminderDetailID -- to update status of remider mail
            // if ($remindermailDetailId == 0) {
            //     $resname = $message['mail']['headers'][10]['name'];
            //     if ($resname == 'REIMNDERDETAILID') {
            //         $remindermailDetailId = $message['mail']['headers'][10]['value'];
            //     } else {
            //         $remindermailDetailId = 0;
            //     }
            // }

            $memname = $message['mail']['headers'][10]['name'];
            $memberId = 0;
            if ($memname == 'MEMBERID') {
                $memberId = $message['mail']['headers'][10]['value'];
            } else {
                $memberId = 0;
            }

            if (empty($memberId) || trim($memberId) == '') {
                $memberId = 0;
            }

            //get region name
            $regname = $message['mail']['headers'][11]['name'];

            if ($regname == 'REGION') {
                $RegionName = $message['mail']['headers'][11]['value'];
            } else {
                $RegionName = 'NULL';
            }

            foreach ($message['bounce']['bouncedRecipients'] as $bouncedRecipient) {
                $log = "\n[" . gmdate(DATE_RFC822) . "] msg:  = Count Loop : " . $i + 1 . $bouncedRecipient['diagnosticCode'] . "\n";
                error_log($log, 3, $logfile);
                $emailAddress = trim($bouncedRecipient['emailAddress']);
                $reason = addslashes(trim($bouncedRecipient['diagnosticCode']));
                $i++;
            }

            if (empty($reason)) {
                if ($bounceType != $bounceSubType) {
                    $reason = $status . ' Email - ' . $bounceType . '(' . $bounceSubType . ')';
                } else {
                    $reason = $status . ' Email - ' . $bounceType;
                }
            }

            $sql = "INSERT into email_status(resolution_id,emaildate,emailid,status,reason)
            values (" . $resolutionId . ",'" . $date . "','" . $emailAddress . "','" . $status . "','" . $reason . "')";

            $log = "[" . gmdate(DATE_RFC822) . "] msg: Email Status Insert query  = " . $sql . "\n";
            error_log($log, 3, $logfile);

            $resultInsertNew = $DB->query($sql);

            if ($resultInsertNew) {
                $log = "[" . gmdate(DATE_RFC822) . "] msg: Inserted into table successfully \n";
                error_log($log, 3, $logfile);
            } else {
                $log = "[" . gmdate(DATE_RFC822) . "] msg: Email Status Not Inserted  = \n";
                error_log($log, 3, $logfile);
            }

            /* Add By Smith
            Update Reason and status where Resaolution ID and Membre ID
             */

            if ($resolutionId > 0 && $emailAddress != null) {
               

                $update = "UPDATE members
                SET status = '" . $status . "', reason = '" . $reason . "', delivery_date = '" . $date . "'
                WHERE resolution_id  = '" . $resolutionId . "' AND email = '" . $emailAddress . "'";
                 $updateShareHolder = $DB->query($update);

                $log = "[" . gmdate(DATE_RFC822) . "] msg: SQl UPDATE SHAREHOLDER Query : " . $update . "\n";
                error_log($log, 3, $logfile);

               

                if ($update_sh_email_status) {
                    $log = "[" . gmdate(DATE_RFC822) . "] msg: SH_EMAIL_STATUS  - Update Successfully. \n";
                    error_log($log, 3, $logfile);
                } else {
                    $log = "[" . gmdate(DATE_RFC822) . "] msg:  SH_EMAIL_STATUS - date Not Updated in Shareholder. \n";
                    error_log($log, 3, $logfile);
                }
                /* --------------------------------- */

                if ($updateShareHolder) {
                    $log = "[" . gmdate(DATE_RFC822) . "] msg: Update Successfully. \n";
                    error_log($log, 3, $logfile);
                } else {
                    $log = "[" . gmdate(DATE_RFC822) . "] msg: date Not Updated in Shareholder. \n";
                    error_log($log, 3, $logfile);
                }
            } else {
                $log = "[" . gmdate(DATE_RFC822) . "] msg: Resolution ID not found. \n";
                error_log($log, 3, $logfile);
            }

           
        }
    }
} else {
    $log = "[" . gmdate(DATE_RFC822) . "] msg: Response Data not found \n";
    error_log($log, 3, $logfile);
}
