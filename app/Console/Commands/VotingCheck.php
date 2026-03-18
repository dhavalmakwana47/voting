<?php

namespace App\Console\Commands;

use App\Models\Resolution;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\VoterEmail;
use App\Models\Member;
use App\Models\SesConnection;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Carbon;

class VotingCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:voting-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
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

            foreach ($resolution->members->where('email_sent', 'N') as $member) {


                if (isset($member->sent_date) && $member->reason != 'Delivery') {
                    $member->update([
                        'email_sent' => 'Y',
                    ]);
                    continue;
                }
                // log::info("get voter data where email_sent N resolution_id =");
                $data = [];
                $data['member'] = $member;
                $mailer->clearAddresses();
                $mailer->clearAttachments();
                $mailer->clearCustomHeaders();
                $mailer->addCustomHeader("resolutionid: $member->resolution_id");
                $mailer->addAddress($member->email, $member->name);
                $mailer->Subject = 'Details of Voting of (' . $member->company->name .") - (Voting No.". $resolution->id .")";
                $mailer->isHTML(true);
                $mailer->Body = view('emails.voter_email', $data);
                $mailer->send();

                $member->update([
                    'email_sent' => 'Y',
                    'sent_date' => Carbon::now()
                ]);
                log::info("get voter data update email_sent Y ");
                $mailCount++;

                if ($mailCount >= 2) {
                    return $mailCount . " mail sended.";
                }
            }

            log::info("get resolution table update sentemail_approval Y resolution_id = ");
            $resolution->update([
                'sentemail_approval' => 'Y'
            ]);
        } else {
            log::info("No voting found.");
            return 'No voting found.';
        }
        return 'All Set';
    }
}
