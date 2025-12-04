<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntercomMarketingDatum extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'intercom_marketing_datas';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'name', 'owner', 'lead_category', 'conversation_rating', 'email', 'phone', 'user_uuid', 'first_seen_date', 'signed_up_date', 'last_seen_date', 'last_contacted_date', 'last_heard_from_date', 'last_opened_email_date', 'last_clicked_on_link_in_email_date', 'web_sessions', 'country', 'region', 'city', 'timezone', 'browser_language', 'language_override', 'browser', 'browser_version', 'os', 'twitter_followers', 'job_title', 'segment', 'tag', 'unsubscribed_from_emails', 'marked_email_as_spam', 'has_hard_bounced', 'utm_campaign', 'utm_content', 'utm_medium', 'utm_source', 'utm_term', 'referral_url', 'job_title', 'subscribed', 'pending', 'unsubscribed', 'last_connected', 'canceled_subscription', 'connected', 'free_premium', 'signed_up_appversion', 'year_1', 'lifetime_subscription', 'last_seen_on_iOS_date', 'iOS_sessions', 'iOS_app_version', 'iOS_device', 'iOS_os_version', 'last_seen_on_android_date', 'android_sessions', 'android_app_version', 'android_device', 'android_os_version', 'enabled_push_messaging', 'is_mobile_unidentified', 'company_name', 'company_id', 'company_last_seen_date', 'company_created_at_date', 'people', 'company_web_sessions', 'plan', 'monthly_spend', 'company_segment', 'company_tag', 'company_size', 'company_industry', 'company_website', 'plan_name'];


}
