<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Models\Company;
use App\Models\EmailTemplate;
use App\Models\EmailSmsLog;
use Mail;
use Helper;

class SubscriptionReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify company for subscription reminder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$templates = array();
		if(Helper::getSetting('sb_mail_1_status')){
			$templates[] = array('key'=>Helper::getSetting('sb_mail_1'),'days'=>Helper::getSetting('sb_mail_day_1'));
		}
		if(Helper::getSetting('sb_mail_2_status')){
			$templates[] = array('key'=>Helper::getSetting('sb_mail_2'),'days'=>Helper::getSetting('sb_mail_day_2'));
		}
		if(Helper::getSetting('sb_mail_3_status')){
			$templates[] = array('key'=>Helper::getSetting('sb_mail_3'),'days'=>Helper::getSetting('sb_mail_day_3'));
		}
		if(Helper::getSetting('sb_mail_4_status')){
			$templates[] = array('key'=>Helper::getSetting('sb_mail_4'),'days'=>Helper::getSetting('sb_mail_day_4'));
		}
		if(Helper::getSetting('sb_mail_5_status')){
			$templates[] = array('key'=>Helper::getSetting('sb_mail_5'),'days'=>Helper::getSetting('sb_mail_day_5'));
		}
		if(Helper::getSetting('sb_mail_6_status')){
			$templates[] = array('key'=>Helper::getSetting('sb_mail_6'),'days'=>Helper::getSetting('sb_mail_day_6'));
		}
		
		if(count($templates)){
			$when = now()->addMinutes(1);
			foreach($templates as $temp){
				$dateDayPlus = \Carbon::now()->addDays($temp['days'])->format('Y-m-d');
				$client_data = Company::where('expiry_date','=',$dateDayPlus)->with(['client_data'])->get();
				if($client_data){
					$template = EmailTemplate::where('external_key','=',$temp['key'])->first();
					foreach($client_data as $client){
						$emails = $client->client_data->email;
						$shortcodes = Helper::shortcodes($client);
						$shortcodes['{{#subscription_expiry_date}}'] = date('d/m/Y',strtotime($client->expiry_date));
						$when = $when->addSeconds(1);
						$data['subject'] = $template->parseSubject($shortcodes);
						$data['messagecontent'] = $template->parseContent($shortcodes);
						$data['from_email'] = Helper::getSetting('mail_from');
						$data['from_name'] =  Helper::getSetting('mail_from_name');
						if($client->used_email >=1){
							$used_email = ($client->used_email > 1) ? $client->used_email - 1 : 0;
							$message_resp = Mail::send('emails.email', $data, function($message)use($data,$emails) {
								$message->subject($data['subject']);
								if(isset($data['from_email']) && isset($data['from_name'])){
								  $message->from($data['from_email'], $data['from_name']);
								}
								$message->to($emails);
							});

							if($message_resp){
							  $client->used_email = $used_email;
							  $client->save();
							}
							$log = new EmailSmsLog;
							$log->user_id = 1;
							$log->client_id = $client->client_id;
							$log->client_number = $client->client_data->mobile_no;
							$log->client_email = $client->client_data->email;
							$log->company_id = $client->id;
							$log->template_id = $template->email_template_id;
							$log->type = 'Email';
							$log->response = '1';
							$log->save();
						}
					}
				}
			}
		}
		//dd(\DB::getQueryLog());
        /* $date3DayPlus = \Carbon::now()->addDays(3)->format('Y-m-d');
        $date7DayPlus = \Carbon::now()->addDays(7)->format('Y-m-d');

        $client_data = Client::leftjoin('company','company.client_id','clients.id')
        ->select('company.expiry_date','company.company_name','company.id as company_id','clients.name','clients.email');
        $client_data->where('company.expiry_date', $date3DayPlus);
        $client_data->orWhere('company.expiry_date', $date7DayPlus);
        $clients = $client_data->get();

        if($client_data->count() > 0) {
            foreach($clients as $client){
              $emails = $client->email;
              $name = $client->name;
              $expiry_date = $client->expiry_date;
              $company_name = $client->company_name;

              $company_id =  $client->company_id;
              $company = Company::find($company_id);

              $message_content = 'Hi '.$name.',';
              $message_content .= '<p>Your subscription plan is expired on '.$expiry_date.'.</p>';

              $message_content .= '<p>Renew your subscription and you will continue to your service.</p>';

              $message_content .= '<p>Thanks.</p>';

              $data['subject'] = 'Your subscription plan is expired on '.$expiry_date;
              $data['messagecontent'] = $message_content;
              $data['from_email'] = $company->from_email;
              $data['from_name'] =  $company->from_name;

                if($company->used_email >=1){
                    $used_email = ($company->used_email > 1) ? $company->used_email - 1 : 0;

                    $message_resp = Mail::send('emails.email', $data, function($message)use($data,$emails) {
                        $message->subject($data['subject']);
                        if(isset($data['from_email']) && isset($data['from_name'])){
                          $message->from($data['from_email'], $data['from_name']);
                        }
                        $message->to($emails);
                    });

                    if($message_resp){
                      $company->used_email = $used_email;
                      $company->save();
                    }
                }
            }
          } */

        $this->info('Reminder email sent to company successfully');
    }
}