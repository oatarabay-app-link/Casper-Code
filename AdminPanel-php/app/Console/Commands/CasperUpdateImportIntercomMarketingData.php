<?php

namespace App\Console\Commands;

use App\Subscription;
use Mockery\Exception;
use App\Models\Auth\User;
use App\IntercomMarketingDatum;
use Illuminate\Console\Command;

class CasperUpdateImportIntercomMarketingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CasperImport:IntercomMarketingData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the CSV files of Intercom Data';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            //Get all files from the intercom-segments folder
            $csv_files = $this->getDirContents(base_path().'/intercom_segments/', '/\.csv$/');
            foreach ($csv_files as $csv) {
                echo "\n".$csv."\n";
                $idata = new IntercomMarketingDatum();
                $r = true;
                $file = fopen($csv, 'r');
                $header = true;
                $folder_name = str_replace(base_path(), '', base_path($csv));
                $idata = null; //IntercomMarketingDatum::where('file_name', "$folder_name")->first();

                if ($idata == null) {
                    $data = fgetcsv($file);
                    $assoc_array = [];

                    if (($handle = fopen("$csv", 'r')) !== false) {                 // open for reading
                        if (($data = fgetcsv($handle, 0, ',')) !== false) {         // extract header data
                            $keys = $data; // save as keys
                        }
                        while (($data = fgetcsv($handle, 0, ',')) !== false) {      // loop remaining rows of data
                            $assoc_array[] = array_combine($keys, $data);              // push associative subarrays
                        }
                        fclose($handle);                                               // close when done
                    }

                    if (in_array('First name', $keys)) {
                        foreach ($assoc_array as $it => $arr) {
                            //var_dump($arr);
                            $idata = new IntercomMarketingDatum();
                            foreach ($arr as $key => $value) {
                                switch ($key) {
                                    case 'First name':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->first_name = $value; //First name
                                        break;
                                    case 'Last name':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->last_name = $value; //Last name
                                        break;
                                    case 'Name':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->name = $value; //Name
                                        break;
                                    case 'Owner':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->owner = $value; //Owner
                                        break;
                                    case 'Lead category':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->lead_category = $value; //Lead category
                                        break;
                                    case 'Conversation Rating':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->conversation_rating = $value; //Conversation Rating
                                        break;
                                    case 'Email':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->email = $value; //Email
                                        break;
                                    case 'Phone':
                                        //    echo "\n".$key.' : '.$value;
                                        $idata->phone = $value; //Phone
                                        break;
                                    case 'User ID':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->user_uuid = $value; //User ID
                                        break;
                                    case 'First Seen (PST)':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->first_seen_date = $value; // First Seen (PST)
                                        break;
                                    case 'First Seen (PDT)':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->first_seen_date = $value; // First Seen (PST)
                                        break;
                                    case 'Signed up (PST)':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->signed_up_date = $value; // Signed up (PST)
                                        break;
                                    case 'Signed up (PDT)':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->signed_up_date = $value; // Signed up (PST)
                                        break;
                                    case 'Last seen (PST)':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->last_seen_date = $value; // Last seen (PST)
                                        break;
                                    case 'Last seen (PDT)':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->last_seen_date = $value; // Last seen (PST)
                                        break;
                                    case 'Last contacted (PST)':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->last_contacted_date = $value; //Last contacted (PST)
                                        break;
                                    case 'Last contacted (PDT)':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->last_contacted_date = $value; //Last contacted (PST)
                                        break;
                                    case 'Last heard from (PST)':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->last_heard_from_date = $value; //Last heard from (PST)
                                        break;
                                    case 'Last heard from (PDT)':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->last_heard_from_date = $value; //Last heard from (PST)
                                        break;
                                    case 'Last opened email (PST)':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->last_opened_email_date = $value; //Last opened email (PST)
                                        break;
                                    case 'Last opened email (PDT)':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->last_opened_email_date = $value; //Last opened email (PST)
                                        break;
                                    case 'Last clicked on link in email (PST)':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->last_clicked_on_link_in_email_date = $value; //Last clicked on link in email (PST)
                                        break;
                                    case 'Last clicked on link in email (PDT)':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->last_clicked_on_link_in_email_date = $value; //Last clicked on link in email (PST)
                                        break;
                                    case 'Web sessions':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->web_sessions = $value; //Web sessions
                                        break;
                                    case 'Country':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->country = $value; //Country
                                        break;
                                    case 'Region':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->region = $value; //Region
                                        break;
                                    case 'City':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->city = $value; //City
                                        break;
                                    case 'Timezone':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->timezone = $value; //Timezone
                                        break;
                                    case 'Browser Language':
                                        ////echo "\n".$key.' : '.$value;
                                        $idata->browser_language = $value; //Browser Language
                                        break;
                                    case 'Language Override':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->language_override = $value; //Language Override
                                        break;
                                    case 'Browser':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->browser = $value; //Browser
                                        break;
                                    case 'Browser Version':
                                        //    echo "\n".$key.' : '.$value;
                                        $idata->browser_version = $value; //Browser Version
                                        break;
                                    case 'OS':
                                        //  echo "\n".$key.' : '.$value;
                                        $idata->os = $value; //OS
                                        break;
                                    case 'Twitter Followers':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->twitter_followers = $value; //Twitter Followers
                                        break;
                                    case 'Job Title':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->job_title = $value; //Job Title
                                        break;
                                    case 'Segment':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->segment = $value; //Segment
                                        break;
                                    case 'Tag':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->tag = $value; //Tag
                                        break;
                                    case 'Unsubscribed from Emails':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->unsubscribed_from_emails = $value; //Unsubscribed from Emails
                                        break;
                                    case 'Marked email as spam':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->marked_email_as_spam = $value; //Marked email as spam
                                        break;
                                    case 'Has hard bounced':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->has_hard_bounced = $value; //Has hard bounced
                                        break;
                                    case 'UTM Campaign':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->utm_campaign = $value; //UTM Campaign
                                        break;
                                    case 'UTM Content':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->utm_content = $value; //UTM Content
                                        break;
                                    case 'UTM Medium':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->utm_medium = $value; //UTM Medium
                                        break;
                                    case 'UTM Source':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->utm_source = $value; //UTM Source
                                        break;
                                    case 'UTM Term':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->utm_term = $value; //UTM Term
                                        break;
                                    case 'Referral URL':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->referral_url = $value; //Referral URL
                                        break;
                                    case 'job_title':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->job_title = $value; //job_title
                                        break;
                                    case 'Subscribed':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->subscribed = $value; //Subscribed
                                        break;
                                    case 'Pending':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->pending = $value; //Pending
                                        break;
                                    case 'Unsubscribed':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->unsubscribed = $value; //Unsubscribed
                                        break;
                                    case 'Last Connected':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->last_connected = $value; //Last Connected
                                        break;
                                    case 'Canceled Subscription':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->canceled_subscription = $value; //Canceled Subscription
                                        break;
                                    case 'Connected':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->connected = $value; //Connected
                                        break;
                                    case 'Free Premium':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->free_premium = $value; //Free Premium
                                        break;
                                    case 'Signed Up App Version':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->signed_up_appversion = $value; //Signed Up App Version
                                        break;
                                    case '1 year':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->year_1 = $value; //1 year
                                        break;
                                    case 'Lifetime subscription':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->lifetime_subscription = $value; //Lifetime subscription
                                        break;
                                    case 'Last seen on iOS (PST)':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->last_seen_on_iOS_date = $value; //Last seen on iOS (PST)
                                        break;
                                    case 'Last seen on iOS (PDT)':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->last_seen_on_iOS_date = $value; //Last seen on iOS (PST)
                                        break;
                                    case 'iOS sessions':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->iOS_sessions = $value; //iOS sessions
                                        break;
                                    case 'iOS App version':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->iOS_app_version = $value; //iOS App version
                                        break;
                                    case 'iOS Device':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->iOS_device = $value; //iOS Device
                                        break;
                                    case 'iOS OS version':
                                        //echo "\n".$key.' : '.$value;
                                        $idata->iOS_os_version = $value; //iOS OS version
                                        break;
                                    case 'Last seen on Android (PST)':
                                        // echo "\n".$key.' : '.$value;
                                        $idata->last_seen_on_android_date = $value; //Last seen on Android (PST)
                                        break;
                                    case 'Last seen on Android (PDT)':
                                        // echo "\n".$key.' : '.$value;
                                        $idata->last_seen_on_android_date = $value; //Last seen on Android (PST)
                                        break;
                                    case 'Android sessions':
                                        // echo "\n".$key.' : '.$value;
                                        $idata->android_sessions = $value; //Android sessions
                                        break;
                                    case 'Android App version':
                                        // echo "\n".$key.' : '.$value;
                                        $idata->android_app_version = $value; //Android App version
                                        break;
                                    case 'Android Device':
                                        // echo "\n".$key.' : '.$value;
                                        $idata->android_device = $value; //Android Device
                                        break;
                                    case 'Android OS version':
                                        // echo "\n".$key.' : '.$value;
                                        $idata->android_os_version = $value; //Android OS version
                                        break;
                                    case 'Enabled Push Messaging':
                                        //  echo "\n".$key.' : '.$value;
                                        $idata->enabled_push_messaging = $value; //Enabled Push Messaging
                                        break;
                                    case 'Is Mobile Unidentified':
                                        //  echo "\n".$key.' : '.$value;
                                        $idata->is_mobile_unidentified = $value; //Is Mobile Unidentified
                                        break;
                                    case 'Company name':
                                        //  echo "\n".$key.' : '.$value;
                                        $idata->company_name = $value; //Company name
                                        break;
                                    case 'Company ID':
                                        // echo "\n".$key.' : '.$value;
                                        $idata->company_id = $value; //Company ID
                                        break;
                                    case 'Company last seen (PST)':
                                        //  echo "\n".$key.' : '.$value;
                                        $idata->company_last_seen_date = $value; //Company last seen (PST)
                                        break;
                                    case 'Company last seen (PDT)':
                                        //  echo "\n".$key.' : '.$value;
                                        $idata->company_last_seen_date = $value; //Company last seen (PST)
                                        break;
                                    case 'Company created at (PST)':
                                        //   echo "\n".$key.' : '.$value;
                                        $idata->company_created_at_date = $value; //Company created at (PST)
                                        break;
                                    case 'Company created at (PDT)':
                                        //   echo "\n".$key.' : '.$value;
                                        $idata->company_created_at_date = $value; //Company created at (PST)
                                        break;
                                    case 'People':
                                        //  echo "\n".$key.' : '.$value;
                                        $idata->people = $value; //People
                                        break;
                                    case 'Company web sessions':
                                        //  echo "\n".$key.' : '.$value;
                                        $idata->company_web_sessions = $value; //Company web sessions
                                        break;
                                    case 'Plan':
                                        //  echo "\n".$key.' : '.$value;
                                        $idata->plan = $value; //Plan
                                        break;
                                    case 'Monthly Spend':
                                        // echo "\n".$key.' : '.$value;
                                        $idata->monthly_spend = $value; //Monthly Spend
                                        break;
                                    case 'Company Segment':
                                        //  echo "\n".$key.' : '.$value;
                                        $idata->company_segment = $value; //Company Segment
                                        break;
                                    case 'Company tag':
                                        //  echo "\n".$key.' : '.$value;
                                        $idata->company_tag = $value; //Company tag
                                        break;
                                    case 'Company size':
                                        //   echo "\n".$key.' : '.$value;
                                        $idata->company_size = $value; //Company size
                                        break;
                                    case 'Company industry':
                                        //  echo "\n".$key.' : '.$value;
                                        $idata->company_industry = $value; //Company industry
                                        break;
                                    case 'Company website':
                                        //   echo "\n".$key.' : '.$value;
                                        $idata->company_website = $value; //Company website
                                        break;
                                    case 'Plan Name':
                                        //   echo "\n".$key.' : '.$value;
                                        $idata->plan_name = $value; //Plan Name
                                        break;

                                    case 'Continent code':
                                        //   echo "\n".$key.' : '.$value;
                                        //$idata->plan_name = $value; //Plan Name
                                        break;

                                    case 'Country code':
                                        //   echo "\n".$key.' : '.$value;
                                        //$idata->plan_name = $value; //Plan Name
                                        break;
                                    default:
                                        echo "\n MISSSING".$key.' : '.$value;
                                        echo "\n MISSSING".$key.' : '.$value;
                                        echo "\n MISSSING".$key.' : '.$value;
                                        echo "\n MISSSING".$key.' : '.$value;
                                        exit();
                                        break;
                                }
                                $idata->folder_name = $folder_name;
                                $idata->file_name = $folder_name;
                                $idata->save();
                                // echo "\n".$key.' : '.$value;
                            }

                            //exit();
                        }
                        echo "\n"." Added : $csv ".count($assoc_array);
                    } else {
                        echo "\n"." Skipping : $csv ".count($assoc_array);
                        //exit();
                    }
                }

                //['first_name', 'last_name', 'name', 'owner', 'lead_category', 'conversation_rating', 'email', 'phone', 'user_uuid', 'first_seen_date', 'signed_up_date', 'last_seen_date', 'last_contacted_date', 'last_heard_from_date', 'last_opened_email_date', 'last_clicked_on_link_in_email_date', 'web_sessions', 'country', 'region', 'city', 'timezone', 'browser_language', 'language_override', 'browser', 'browser_version', 'os', 'twitter_followers', 'job_title', 'segment', 'tag', 'unsubscribed_from_emails', 'marked_email_as_spam', 'has_hard_bounced', 'utm_campaign', 'utm_content', 'utm_medium', 'utm_source', 'utm_term', 'referral_url', 'job_title', 'subscribed', 'pending', 'unsubscribed', 'last_connected', 'canceled_subscription', 'connected', 'free_premium', 'signed_up_appversion', 'year_1', 'lifetime_subscription', 'last_seen_on_iOS_date', 'iOS_sessions', 'iOS_app_version', 'iOS_device', 'iOS_os_version', 'last_seen_on_android_date', 'android_sessions', 'android_app_version', 'android_device', 'android_os_version', 'enabled_push_messaging', 'is_mobile_unidentified', 'company_name', 'company_id', 'company_last_seen_date', 'company_created_at_date', 'people', 'company_web_sessions', 'plan', 'monthly_spend', 'company_segment', 'company_tag', 'company_size', 'company_industry', 'company_website', 'plan_name'];
            }
        } catch (Exception $ex) {
            echo $ex;
            echo "\n".'Error In file';
        }
    }

    public function getDirContents($dir, $filter = '', &$results = [])
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);

            if (! is_dir($path)) {
                if (empty($filter) || preg_match($filter, $path)) {
                    $results[] = $path;
                }
            } elseif ($value != '.' && $value != '..') {
                $this->getDirContents($path, $filter, $results);
            }
        }

        return $results;
    }
}
