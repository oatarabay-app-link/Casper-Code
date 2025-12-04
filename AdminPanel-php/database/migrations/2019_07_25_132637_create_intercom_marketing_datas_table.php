<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIntercomMarketingDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intercom_marketing_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name')->nullable();
            $table->string('owner')->nullable();
            $table->string('lead_category')->nullable();
            $table->string('conversation_rating')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('user_uuid')->nullable();
            $table->string('first_seen_date')->nullable();
            $table->string('signed_up_date')->nullable();
            $table->string('last_seen_date')->nullable();
            $table->string('last_contacted_date')->nullable();
            $table->string('last_heard_from_date')->nullable();
            $table->string('last_opened_email_date')->nullable();
            $table->string('last_clicked_on_link_in_email_date')->nullable();
            $table->string('web_sessions')->nullable();
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('timezone')->nullable();
            $table->string('browser_language')->nullable();
            $table->string('language_override')->nullable();
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('os')->nullable();
            $table->string('twitter_followers')->nullable();
            $table->string('job_titles')->nullable();
            $table->longText('segment')->nullable();
            $table->string('tag')->nullable();
            $table->string('unsubscribed_from_emails')->nullable();
            $table->string('marked_email_as_spam')->nullable();
            $table->string('has_hard_bounced')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('referral_url')->nullable();
            $table->string('job_title')->nullable();
            $table->string('subscribed')->nullable();
            $table->string('pending')->nullable();
            $table->string('unsubscribed')->nullable();
            $table->string('last_connected')->nullable();
            $table->string('canceled_subscription')->nullable();
            $table->string('connected')->nullable();
            $table->string('free_premium')->nullable();
            $table->string('signed_up_appversion')->nullable();
            $table->string('year_1')->nullable();
            $table->string('lifetime_subscription')->nullable();
            $table->string('last_seen_on_iOS_date')->nullable();
            $table->string('iOS_sessions')->nullable();
            $table->string('iOS_app_version')->nullable();
            $table->string('iOS_device')->nullable();
            $table->string('iOS_os_version')->nullable();
            $table->string('last_seen_on_android_date')->nullable();
            $table->string('android_sessions')->nullable();
            $table->string('android_app_version')->nullable();
            $table->string('android_device')->nullable();
            $table->string('android_os_version')->nullable();
            $table->string('enabled_push_messaging')->nullable();
            $table->string('is_mobile_unidentified')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_id')->nullable();
            $table->string('company_last_seen_date')->nullable();
            $table->string('company_created_at_date')->nullable();
            $table->string('people')->nullable();
            $table->string('company_web_sessions')->nullable();
            $table->string('plan')->nullable();
            $table->string('monthly_spend')->nullable();
            $table->longText('company_segment')->nullable();
            $table->string('company_tag')->nullable();
            $table->string('company_size')->nullable();
            $table->string('company_industry')->nullable();
            $table->string('company_website')->nullable();
            $table->string('plan_name')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('intercom_marketing_datas');
    }
}
