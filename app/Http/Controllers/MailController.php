<?php

namespace App\Http\Controllers;

use App\Exports\DeliveryReportExport;
use App\Exports\multisheetExport;
use App\Mail\VoterEmail;
use App\Models\Resolution;
use App\Models\SesConnection;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

require_once 'class.phpmailer.php';
require_once 'class.smtp.php';

class MailController extends Controller
{
    public function mail()
    {
        $user = auth()->user();
        $data = [];
        $data['blade'] = 'emails.membermail';
        Mail::to('info@indiaevoting.com')->send(new VoterEmail($data));
        return 'Welcome email sent successfully!';
    }

    public function sendMailApprovedCron()
    {
        $getSESCon = SesConnection::where('ACTIVE', 'Y')->get();
        $totalSESCon = $getSESCon->count();
        $s = 0;

        // Cache key
        $phpMailerCacheKey = 'PHPMAILER_CACHE';

        // Check if PHPMailer objects are in cache
        if (Cache::has($phpMailerCacheKey)) {
            Log::info('PHP Mailer - Cache Available');
            $phpMailerCache = Cache::get($phpMailerCacheKey);
            $totalCacheObject = count($phpMailerCache);

            Log::info('Check Object count of Active SES Connection with cache Objects');
            if ($totalCacheObject != $totalSESCon) {
                Artisan::call('cache:clear');
                Log::info('COUNT NOT SAME - CLEARED CACHE');
                $phpMailerCache = [];
            }
        } else {
            $phpMailerCache = [];
        }

        $HOSTNAME = $USERNAME = $PASSWORD = $PORT = $REGION = [];

        if (empty($phpMailerCache)) {
            Log::info('CREATE PHPMAILER OBJECT AND PUT IN CACHE');
            $phpMailerObj = [];

            foreach ($getSESCon as $sescon) {
                Log::info("Coming sesconnection loop", ['index' => $s]);

                if (!empty($sescon->HOSTNAME)) {
                    $HOSTNAME[$s] = $sescon->HOSTNAME;
                    $USERNAME[$s] = $sescon->USERNAME;
                    $PASSWORD[$s] = $sescon->PASSWORD;
                    $PORT[$s] = $sescon->PORT;
                    $REGION[$s] = $sescon->REGION;

                    Log::info(":: ======= :: Coming to set dynamic PHPMailer Objects :: =======");

                    $mailer = new \PHPMailer(true);
                    $mailer->IsSMTP();
                    $mailer->SMTPAuth = true;
                    $mailer->SMTPSecure = "ssl";
                    $mailer->isHTML(true);
                    $mailer->setFrom('info@indiaevoting.com', "E-Voting Service");
                    $mailer->Host = $sescon->HOSTNAME;
                    $mailer->Port = $sescon->PORT;
                    $mailer->Username = $sescon->USERNAME;
                    $mailer->Password = $sescon->PASSWORD;
                    $phpMailerObj[$s] = $mailer;
                    $s++;
                }
            }

            // Add PHPMailer objects to cache
            Log::info("Put PHP mailer objects in cache");
            Cache::put($phpMailerCacheKey, $phpMailerObj, 1440);
        } else {
            foreach ($getSESCon as $sescon) {
                Log::info("Coming ses connection loop else", ['index' => $s]);

                if (!empty($sescon->HOSTNAME)) {
                    $HOSTNAME[$s] = $sescon->HOSTNAME;
                    $USERNAME[$s] = $sescon->USERNAME;
                    $PASSWORD[$s] = $sescon->PASSWORD;
                    $PORT[$s] = $sescon->PORT;
                    $REGION[$s] = $sescon->REGION;
                    $s++;
                }
            }
        }

        // Retrieve PHPMailer objects from cache
        $phpMailerArr = Cache::get($phpMailerCacheKey);
        /* END CONFIGURATION */



        $now = Carbon::now();
        $resolution = Resolution::with('members')
            ->where('end_date', '>=', $now)
            ->whereIn('sentemail_approval', ['P', 'N'])
            ->where('is_active', 1)
            ->orderBy('id', 'asc')
            ->first();
        $mailCount = 0;

        // log::info($resolutions);
        $mailer = $phpMailerArr[0];

        log::info("sentemail_approval p int in resolution_id = ");
        if (isset($resolution)) {

            $resolution->update([
                'sentemail_approval' => 'P'
            ]);
            foreach ($resolution->members->where('email_sent', 'N')->groupBy('email') as $email => $members) {
                log::info("get voter data where email_sent N resolution_id =");
                $data = [];
                $data['members'] = $members;
                $data['member'] = $members[0];
                $member = $members[0];
                $mailer->clearAddresses();
                $mailer->clearAttachments();
                $mailer->clearCustomHeaders();
                $mailer->addCustomHeader("resolutionid: $member->resolution_id");
                $mailer->addAddress($member->email, $member->name);
                $mailer->Subject = 'Details of Voting of (' . $member->company->name . ") - (Voting No." . $resolution->id . ")";
                $mailer->isHTML(true);
                $mailer->Body = view('emails.voter_email', $data);
                $mailer->send();

                $logData['member_id'] = $member->id;
                $logData['resolution_id'] = $member->resolution_id;
                // If the email is sent successfully, log the action
                $logData['action'] = "Login and password email sent to member '{$member->email}' (ID: {$member->id}).";
                addUserAction($logData);
                $member->update([
                    'email_sent' => 'Y',
                    'sent_date' => Carbon::now()
                ]);
                log::info("get voter data update email_sent Y ");



                //phone sms start
                if (isset($member->phone)) {
                    // $curl = curl_init();

                    // curl_setopt_array($curl, [
                    //     CURLOPT_URL => "https://control.msg91.com/api/v5/flow",
                    //     CURLOPT_RETURNTRANSFER => true,
                    //     CURLOPT_ENCODING => "",
                    //     CURLOPT_MAXREDIRS => 10,
                    //     CURLOPT_TIMEOUT => 60,
                    //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    //     CURLOPT_CUSTOMREQUEST => "POST",
                    //     CURLOPT_POSTFIELDS => json_encode([
                    //         "template_id" => "6788ab22d6fc05493a718772",
                    //         "realTimeResponse" => "1",
                    //         "recipients" => [
                    //             [
                    //                 "mobiles" => "91$member->phone",
                    //                 "Name" =>  $member->company->name,
                    //                 "DateTime" =>  Carbon::createFromFormat('Y-m-d H:i:s', $resolution->start_date)->format('d-M-Y h:i A')
                    //             ]
                    //         ]
                    //     ]),
                    //     CURLOPT_HTTPHEADER => [
                    //         "accept: application/json",
                    //         "authkey: 437167A48MMacuzvRF676932a5P1",
                    //         "content-type: application/json"
                    //     ],
                    // ]);

                    // $response = curl_exec($curl);
                    // $err = curl_error($curl);

                    // curl_close($curl);
                }

                //phone sms end


                $mailCount++;

                if ($mailCount >= 25) {
                    return $mailCount . " mail sended.";
                }
            }

            log::info("get resolution table update sentemail_approval Y resolution_id = ");


            $mailer->clearAddresses();
            $mailer->clearAttachments();
            $mailer->clearCustomHeaders();
            $mailer->addCustomHeader("resolutionid: $resolution->resolution_id");
            $recipients = [
                ['email' => $resolution->user->email, 'name' => $resolution->user->name],
                ['email' =>  $resolution->company->email, 'name' =>  $resolution->company->name],
            ];

            foreach ($recipients as $recipient) {
                $mailer->addAddress($recipient['email'], $recipient['name']);
            }

            $mailer->Subject = 'Voter Mail Delivery Status Report (' . $resolution->company->name . ") - (Voting No." . $resolution->id . ")";
            $mailer->isHTML(true);
            $mailer->Body = view('emails.deliveryreportmsg', ['resolution' => $resolution]);
            $filename = $resolution->company->name . 'voter_delivery_report' . $resolution->id . '.xlsx';
            Excel::store(new DeliveryReportExport($resolution->id), 'tempfile/' . $filename);

            $file_path = storage_path('app/tempfile/' . $filename);
            if (file_exists($file_path)) {
                $mailer->addAttachment($file_path);
            } else {
                error_log("File not found: " . $file_path);
            }
            $mailer->send();
            $resolution->update([
                'sentemail_approval' => 'Y',
                'is_updated' => 0
            ]);
        } else {
            log::info("No voting found.");
            return 'No voting found.';
        }
        return 'All Set';
    }

    public function sendFinalReportCron()
    {

        $getSESCon = SesConnection::where('ACTIVE', 'Y')->get();
        $totalSESCon = $getSESCon->count();
        $s = 0;

        // Cache key
        $phpMailerCacheKey = 'PHPMAILER_CACHE';

        // Check if PHPMailer objects are in cache
        if (Cache::has($phpMailerCacheKey)) {
            Log::info('PHP Mailer - Cache Available');
            $phpMailerCache = Cache::get($phpMailerCacheKey);
            $totalCacheObject = count($phpMailerCache);

            Log::info('Check Object count of Active SES Connection with cache Objects');
            if ($totalCacheObject != $totalSESCon) {
                Artisan::call('cache:clear');
                Log::info('COUNT NOT SAME - CLEARED CACHE');
                $phpMailerCache = [];
            }
        } else {
            $phpMailerCache = [];
        }

        $HOSTNAME = $USERNAME = $PASSWORD = $PORT = $REGION = [];

        if (empty($phpMailerCache)) {
            Log::info('CREATE PHPMAILER OBJECT AND PUT IN CACHE');
            $phpMailerObj = [];

            foreach ($getSESCon as $sescon) {
                Log::info("Coming sesconnection loop", ['index' => $s]);

                if (!empty($sescon->HOSTNAME)) {
                    $HOSTNAME[$s] = $sescon->HOSTNAME;
                    $USERNAME[$s] = $sescon->USERNAME;
                    $PASSWORD[$s] = $sescon->PASSWORD;
                    $PORT[$s] = $sescon->PORT;
                    $REGION[$s] = $sescon->REGION;

                    Log::info(":: ======= :: Coming to set dynamic PHPMailer Objects :: =======");

                    $mailer = new \PHPMailer(true);
                    $mailer->IsSMTP();
                    $mailer->SMTPAuth = true;
                    $mailer->SMTPSecure = "ssl";
                    $mailer->isHTML(true);
                    $mailer->setFrom('info@indiaevoting.com', "E-Voting Service");
                    $mailer->Host = $sescon->HOSTNAME;
                    $mailer->Port = $sescon->PORT;
                    $mailer->Username = $sescon->USERNAME;
                    $mailer->Password = $sescon->PASSWORD;
                    $phpMailerObj[$s] = $mailer;
                    $s++;
                }
            }

            // Add PHPMailer objects to cache
            Log::info("Put PHP mailer objects in cache");
            Cache::put($phpMailerCacheKey, $phpMailerObj, 1440);
        } else {
            foreach ($getSESCon as $sescon) {
                Log::info("Coming ses connection loop else", ['index' => $s]);

                if (!empty($sescon->HOSTNAME)) {
                    $HOSTNAME[$s] = $sescon->HOSTNAME;
                    $USERNAME[$s] = $sescon->USERNAME;
                    $PASSWORD[$s] = $sescon->PASSWORD;
                    $PORT[$s] = $sescon->PORT;
                    $REGION[$s] = $sescon->REGION;
                    $s++;
                }
            }
        }

        // Retrieve PHPMailer objects from cache
        $phpMailerArr = Cache::get($phpMailerCacheKey);
        /* END CONFIGURATION */
        $mailer = $phpMailerArr[0];

        $now = Carbon::now();
        $resolution = Resolution::where('is_active', 1)->where('end_date', '<=', $now)->where('sentemail_reportuser', 'N')->orderBy('id', 'asc')->first();
        if (isset($resolution)) {
            $user = $resolution->user;
            $filename = $resolution->company->name . '_finalvotingreport_' . $resolution->id . '.xlsx';
            Excel::store(new multisheetExport($resolution->id), 'tempfile/' . $filename);
            $files_to_attach = [
                'tempfile/' . $filename
            ];
            $data['resolution'] = $resolution;
            $mailer->clearAddresses();
            $mailer->clearAttachments();
            $mailer->clearCustomHeaders();
            $mailer->addAddress($user->email, $user->name);
            // $mailer->Subject = 'Final Voting Report ' . $resolution->company->name;
            $mailer->Subject = 'Final Voting Report (' . $resolution->company->name . ") - (Voting No." . $resolution->id . ")";
            $mailer->isHTML(true);
            $mailer->Body = view('emails.finalreportmail', $data);
            foreach ($files_to_attach as $file) {
                $file_path = storage_path('app/' . $file);
                if (file_exists($file_path)) {
                    $mailer->addAttachment($file_path);
                } else {
                    error_log("File not found: " . $file_path);
                }
            }
            $mailer->send();

            $resolution->update([
                'sentemail_reportuser' => 'Y'
            ]);
        }

        return 'All Set.';
    }
}
