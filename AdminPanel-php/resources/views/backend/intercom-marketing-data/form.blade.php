<div class="form-group {{ $errors->has('first_name') ? 'has-error' : ''}}">
    <label for="first_name" class="control-label">{{ 'First Name' }}</label>
    <input class="form-control" name="first_name" type="text" id="first_name" value="{{ isset($intercommarketingdatum->first_name) ? $intercommarketingdatum->first_name : ''}}" >
    {!! $errors->first('first_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('last_name') ? 'has-error' : ''}}">
    <label for="last_name" class="control-label">{{ 'Last Name' }}</label>
    <input class="form-control" name="last_name" type="text" id="last_name" value="{{ isset($intercommarketingdatum->last_name) ? $intercommarketingdatum->last_name : ''}}" >
    {!! $errors->first('last_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
    <label for="name" class="control-label">{{ 'Name' }}</label>
    <input class="form-control" name="name" type="text" id="name" value="{{ isset($intercommarketingdatum->name) ? $intercommarketingdatum->name : ''}}" >
    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('owner') ? 'has-error' : ''}}">
    <label for="owner" class="control-label">{{ 'Owner' }}</label>
    <input class="form-control" name="owner" type="text" id="owner" value="{{ isset($intercommarketingdatum->owner) ? $intercommarketingdatum->owner : ''}}" >
    {!! $errors->first('owner', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('lead_category') ? 'has-error' : ''}}">
    <label for="lead_category" class="control-label">{{ 'Lead Category' }}</label>
    <input class="form-control" name="lead_category" type="text" id="lead_category" value="{{ isset($intercommarketingdatum->lead_category) ? $intercommarketingdatum->lead_category : ''}}" >
    {!! $errors->first('lead_category', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('conversation_rating') ? 'has-error' : ''}}">
    <label for="conversation_rating" class="control-label">{{ 'Conversation Rating' }}</label>
    <input class="form-control" name="conversation_rating" type="text" id="conversation_rating" value="{{ isset($intercommarketingdatum->conversation_rating) ? $intercommarketingdatum->conversation_rating : ''}}" >
    {!! $errors->first('conversation_rating', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
    <label for="email" class="control-label">{{ 'Email' }}</label>
    <input class="form-control" name="email" type="text" id="email" value="{{ isset($intercommarketingdatum->email) ? $intercommarketingdatum->email : ''}}" >
    {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('phone') ? 'has-error' : ''}}">
    <label for="phone" class="control-label">{{ 'Phone' }}</label>
    <input class="form-control" name="phone" type="text" id="phone" value="{{ isset($intercommarketingdatum->phone) ? $intercommarketingdatum->phone : ''}}" >
    {!! $errors->first('phone', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('user_uuid') ? 'has-error' : ''}}">
    <label for="user_uuid" class="control-label">{{ 'User Uuid' }}</label>
    <input class="form-control" name="user_uuid" type="text" id="user_uuid" value="{{ isset($intercommarketingdatum->user_uuid) ? $intercommarketingdatum->user_uuid : ''}}" >
    {!! $errors->first('user_uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('first_seen_date') ? 'has-error' : ''}}">
    <label for="first_seen_date" class="control-label">{{ 'First Seen Date' }}</label>
    <input class="form-control" name="first_seen_date" type="text" id="first_seen_date" value="{{ isset($intercommarketingdatum->first_seen_date) ? $intercommarketingdatum->first_seen_date : ''}}" >
    {!! $errors->first('first_seen_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('signed_up_date') ? 'has-error' : ''}}">
    <label for="signed_up_date" class="control-label">{{ 'Signed Up Date' }}</label>
    <input class="form-control" name="signed_up_date" type="text" id="signed_up_date" value="{{ isset($intercommarketingdatum->signed_up_date) ? $intercommarketingdatum->signed_up_date : ''}}" >
    {!! $errors->first('signed_up_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('last_seen_date') ? 'has-error' : ''}}">
    <label for="last_seen_date" class="control-label">{{ 'Last Seen Date' }}</label>
    <input class="form-control" name="last_seen_date" type="text" id="last_seen_date" value="{{ isset($intercommarketingdatum->last_seen_date) ? $intercommarketingdatum->last_seen_date : ''}}" >
    {!! $errors->first('last_seen_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('last_contacted_date') ? 'has-error' : ''}}">
    <label for="last_contacted_date" class="control-label">{{ 'Last Contacted Date' }}</label>
    <input class="form-control" name="last_contacted_date" type="text" id="last_contacted_date" value="{{ isset($intercommarketingdatum->last_contacted_date) ? $intercommarketingdatum->last_contacted_date : ''}}" >
    {!! $errors->first('last_contacted_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('last_heard_from_date') ? 'has-error' : ''}}">
    <label for="last_heard_from_date" class="control-label">{{ 'Last Heard From Date' }}</label>
    <input class="form-control" name="last_heard_from_date" type="text" id="last_heard_from_date" value="{{ isset($intercommarketingdatum->last_heard_from_date) ? $intercommarketingdatum->last_heard_from_date : ''}}" >
    {!! $errors->first('last_heard_from_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('last_opened_email_date') ? 'has-error' : ''}}">
    <label for="last_opened_email_date" class="control-label">{{ 'Last Opened Email Date' }}</label>
    <input class="form-control" name="last_opened_email_date" type="text" id="last_opened_email_date" value="{{ isset($intercommarketingdatum->last_opened_email_date) ? $intercommarketingdatum->last_opened_email_date : ''}}" >
    {!! $errors->first('last_opened_email_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('last_clicked_on_link_in_email_date') ? 'has-error' : ''}}">
    <label for="last_clicked_on_link_in_email_date" class="control-label">{{ 'Last Clicked On Link In Email Date' }}</label>
    <input class="form-control" name="last_clicked_on_link_in_email_date" type="text" id="last_clicked_on_link_in_email_date" value="{{ isset($intercommarketingdatum->last_clicked_on_link_in_email_date) ? $intercommarketingdatum->last_clicked_on_link_in_email_date : ''}}" >
    {!! $errors->first('last_clicked_on_link_in_email_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('web_sessions') ? 'has-error' : ''}}">
    <label for="web_sessions" class="control-label">{{ 'Web Sessions' }}</label>
    <input class="form-control" name="web_sessions" type="text" id="web_sessions" value="{{ isset($intercommarketingdatum->web_sessions) ? $intercommarketingdatum->web_sessions : ''}}" >
    {!! $errors->first('web_sessions', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('country') ? 'has-error' : ''}}">
    <label for="country" class="control-label">{{ 'Country' }}</label>
    <input class="form-control" name="country" type="text" id="country" value="{{ isset($intercommarketingdatum->country) ? $intercommarketingdatum->country : ''}}" >
    {!! $errors->first('country', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('region') ? 'has-error' : ''}}">
    <label for="region" class="control-label">{{ 'Region' }}</label>
    <input class="form-control" name="region" type="text" id="region" value="{{ isset($intercommarketingdatum->region) ? $intercommarketingdatum->region : ''}}" >
    {!! $errors->first('region', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('city') ? 'has-error' : ''}}">
    <label for="city" class="control-label">{{ 'City' }}</label>
    <input class="form-control" name="city" type="text" id="city" value="{{ isset($intercommarketingdatum->city) ? $intercommarketingdatum->city : ''}}" >
    {!! $errors->first('city', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('timezone') ? 'has-error' : ''}}">
    <label for="timezone" class="control-label">{{ 'Timezone' }}</label>
    <input class="form-control" name="timezone" type="text" id="timezone" value="{{ isset($intercommarketingdatum->timezone) ? $intercommarketingdatum->timezone : ''}}" >
    {!! $errors->first('timezone', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('browser_language') ? 'has-error' : ''}}">
    <label for="browser_language" class="control-label">{{ 'Browser Language' }}</label>
    <input class="form-control" name="browser_language" type="text" id="browser_language" value="{{ isset($intercommarketingdatum->browser_language) ? $intercommarketingdatum->browser_language : ''}}" >
    {!! $errors->first('browser_language', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('language_override') ? 'has-error' : ''}}">
    <label for="language_override" class="control-label">{{ 'Language Override' }}</label>
    <input class="form-control" name="language_override" type="text" id="language_override" value="{{ isset($intercommarketingdatum->language_override) ? $intercommarketingdatum->language_override : ''}}" >
    {!! $errors->first('language_override', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('browser') ? 'has-error' : ''}}">
    <label for="browser" class="control-label">{{ 'Browser' }}</label>
    <input class="form-control" name="browser" type="text" id="browser" value="{{ isset($intercommarketingdatum->browser) ? $intercommarketingdatum->browser : ''}}" >
    {!! $errors->first('browser', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('browser_version') ? 'has-error' : ''}}">
    <label for="browser_version" class="control-label">{{ 'Browser Version' }}</label>
    <input class="form-control" name="browser_version" type="text" id="browser_version" value="{{ isset($intercommarketingdatum->browser_version) ? $intercommarketingdatum->browser_version : ''}}" >
    {!! $errors->first('browser_version', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('os') ? 'has-error' : ''}}">
    <label for="os" class="control-label">{{ 'Os' }}</label>
    <input class="form-control" name="os" type="text" id="os" value="{{ isset($intercommarketingdatum->os) ? $intercommarketingdatum->os : ''}}" >
    {!! $errors->first('os', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('twitter_followers') ? 'has-error' : ''}}">
    <label for="twitter_followers" class="control-label">{{ 'Twitter Followers' }}</label>
    <input class="form-control" name="twitter_followers" type="text" id="twitter_followers" value="{{ isset($intercommarketingdatum->twitter_followers) ? $intercommarketingdatum->twitter_followers : ''}}" >
    {!! $errors->first('twitter_followers', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('job_title') ? 'has-error' : ''}}">
    <label for="job_title" class="control-label">{{ 'Job Title' }}</label>
    <input class="form-control" name="job_title" type="text" id="job_title" value="{{ isset($intercommarketingdatum->job_title) ? $intercommarketingdatum->job_title : ''}}" >
    {!! $errors->first('job_title', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('segment') ? 'has-error' : ''}}">
    <label for="segment" class="control-label">{{ 'Segment' }}</label>
    <textarea class="form-control" rows="5" name="segment" type="textarea" id="segment" >{{ isset($intercommarketingdatum->segment) ? $intercommarketingdatum->segment : ''}}</textarea>
    {!! $errors->first('segment', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('tag') ? 'has-error' : ''}}">
    <label for="tag" class="control-label">{{ 'Tag' }}</label>
    <input class="form-control" name="tag" type="text" id="tag" value="{{ isset($intercommarketingdatum->tag) ? $intercommarketingdatum->tag : ''}}" >
    {!! $errors->first('tag', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('unsubscribed_from_emails') ? 'has-error' : ''}}">
    <label for="unsubscribed_from_emails" class="control-label">{{ 'Unsubscribed From Emails' }}</label>
    <input class="form-control" name="unsubscribed_from_emails" type="text" id="unsubscribed_from_emails" value="{{ isset($intercommarketingdatum->unsubscribed_from_emails) ? $intercommarketingdatum->unsubscribed_from_emails : ''}}" >
    {!! $errors->first('unsubscribed_from_emails', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('marked_email_as_spam') ? 'has-error' : ''}}">
    <label for="marked_email_as_spam" class="control-label">{{ 'Marked Email As Spam' }}</label>
    <input class="form-control" name="marked_email_as_spam" type="text" id="marked_email_as_spam" value="{{ isset($intercommarketingdatum->marked_email_as_spam) ? $intercommarketingdatum->marked_email_as_spam : ''}}" >
    {!! $errors->first('marked_email_as_spam', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('has_hard_bounced') ? 'has-error' : ''}}">
    <label for="has_hard_bounced" class="control-label">{{ 'Has Hard Bounced' }}</label>
    <input class="form-control" name="has_hard_bounced" type="text" id="has_hard_bounced" value="{{ isset($intercommarketingdatum->has_hard_bounced) ? $intercommarketingdatum->has_hard_bounced : ''}}" >
    {!! $errors->first('has_hard_bounced', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('utm_campaign') ? 'has-error' : ''}}">
    <label for="utm_campaign" class="control-label">{{ 'Utm Campaign' }}</label>
    <input class="form-control" name="utm_campaign" type="text" id="utm_campaign" value="{{ isset($intercommarketingdatum->utm_campaign) ? $intercommarketingdatum->utm_campaign : ''}}" >
    {!! $errors->first('utm_campaign', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('utm_content') ? 'has-error' : ''}}">
    <label for="utm_content" class="control-label">{{ 'Utm Content' }}</label>
    <input class="form-control" name="utm_content" type="text" id="utm_content" value="{{ isset($intercommarketingdatum->utm_content) ? $intercommarketingdatum->utm_content : ''}}" >
    {!! $errors->first('utm_content', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('utm_medium') ? 'has-error' : ''}}">
    <label for="utm_medium" class="control-label">{{ 'Utm Medium' }}</label>
    <input class="form-control" name="utm_medium" type="text" id="utm_medium" value="{{ isset($intercommarketingdatum->utm_medium) ? $intercommarketingdatum->utm_medium : ''}}" >
    {!! $errors->first('utm_medium', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('utm_source') ? 'has-error' : ''}}">
    <label for="utm_source" class="control-label">{{ 'Utm Source' }}</label>
    <input class="form-control" name="utm_source" type="text" id="utm_source" value="{{ isset($intercommarketingdatum->utm_source) ? $intercommarketingdatum->utm_source : ''}}" >
    {!! $errors->first('utm_source', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('utm_term') ? 'has-error' : ''}}">
    <label for="utm_term" class="control-label">{{ 'Utm Term' }}</label>
    <input class="form-control" name="utm_term" type="text" id="utm_term" value="{{ isset($intercommarketingdatum->utm_term) ? $intercommarketingdatum->utm_term : ''}}" >
    {!! $errors->first('utm_term', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('referral_url') ? 'has-error' : ''}}">
    <label for="referral_url" class="control-label">{{ 'Referral Url' }}</label>
    <input class="form-control" name="referral_url" type="text" id="referral_url" value="{{ isset($intercommarketingdatum->referral_url) ? $intercommarketingdatum->referral_url : ''}}" >
    {!! $errors->first('referral_url', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('job_title') ? 'has-error' : ''}}">
    <label for="job_title" class="control-label">{{ 'Job Title' }}</label>
    <input class="form-control" name="job_title" type="text" id="job_title" value="{{ isset($intercommarketingdatum->job_title) ? $intercommarketingdatum->job_title : ''}}" >
    {!! $errors->first('job_title', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('subscribed') ? 'has-error' : ''}}">
    <label for="subscribed" class="control-label">{{ 'Subscribed' }}</label>
    <input class="form-control" name="subscribed" type="text" id="subscribed" value="{{ isset($intercommarketingdatum->subscribed) ? $intercommarketingdatum->subscribed : ''}}" >
    {!! $errors->first('subscribed', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('pending') ? 'has-error' : ''}}">
    <label for="pending" class="control-label">{{ 'Pending' }}</label>
    <input class="form-control" name="pending" type="text" id="pending" value="{{ isset($intercommarketingdatum->pending) ? $intercommarketingdatum->pending : ''}}" >
    {!! $errors->first('pending', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('unsubscribed') ? 'has-error' : ''}}">
    <label for="unsubscribed" class="control-label">{{ 'Unsubscribed' }}</label>
    <input class="form-control" name="unsubscribed" type="text" id="unsubscribed" value="{{ isset($intercommarketingdatum->unsubscribed) ? $intercommarketingdatum->unsubscribed : ''}}" >
    {!! $errors->first('unsubscribed', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('last_connected') ? 'has-error' : ''}}">
    <label for="last_connected" class="control-label">{{ 'Last Connected' }}</label>
    <input class="form-control" name="last_connected" type="text" id="last_connected" value="{{ isset($intercommarketingdatum->last_connected) ? $intercommarketingdatum->last_connected : ''}}" >
    {!! $errors->first('last_connected', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('canceled_subscription') ? 'has-error' : ''}}">
    <label for="canceled_subscription" class="control-label">{{ 'Canceled Subscription' }}</label>
    <input class="form-control" name="canceled_subscription" type="text" id="canceled_subscription" value="{{ isset($intercommarketingdatum->canceled_subscription) ? $intercommarketingdatum->canceled_subscription : ''}}" >
    {!! $errors->first('canceled_subscription', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('connected') ? 'has-error' : ''}}">
    <label for="connected" class="control-label">{{ 'Connected' }}</label>
    <input class="form-control" name="connected" type="text" id="connected" value="{{ isset($intercommarketingdatum->connected) ? $intercommarketingdatum->connected : ''}}" >
    {!! $errors->first('connected', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('free_premium') ? 'has-error' : ''}}">
    <label for="free_premium" class="control-label">{{ 'Free Premium' }}</label>
    <input class="form-control" name="free_premium" type="text" id="free_premium" value="{{ isset($intercommarketingdatum->free_premium) ? $intercommarketingdatum->free_premium : ''}}" >
    {!! $errors->first('free_premium', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('signed_up_appversion') ? 'has-error' : ''}}">
    <label for="signed_up_appversion" class="control-label">{{ 'Signed Up Appversion' }}</label>
    <input class="form-control" name="signed_up_appversion" type="text" id="signed_up_appversion" value="{{ isset($intercommarketingdatum->signed_up_appversion) ? $intercommarketingdatum->signed_up_appversion : ''}}" >
    {!! $errors->first('signed_up_appversion', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('year_1') ? 'has-error' : ''}}">
    <label for="year_1" class="control-label">{{ '1 Year' }}</label>
    <input class="form-control" name="year_1" type="text" id="year_1" value="{{ isset($intercommarketingdatum->year_1) ? $intercommarketingdatum->year_1 : ''}}" >
    {!! $errors->first('year_1', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('lifetime_subscription') ? 'has-error' : ''}}">
    <label for="lifetime_subscription" class="control-label">{{ 'Lifetime Subscription' }}</label>
    <input class="form-control" name="lifetime_subscription" type="text" id="lifetime_subscription" value="{{ isset($intercommarketingdatum->lifetime_subscription) ? $intercommarketingdatum->lifetime_subscription : ''}}" >
    {!! $errors->first('lifetime_subscription', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('last_seen_on_iOS_date') ? 'has-error' : ''}}">
    <label for="last_seen_on_iOS_date" class="control-label">{{ 'Last Seen On Ios Date' }}</label>
    <input class="form-control" name="last_seen_on_iOS_date" type="text" id="last_seen_on_iOS_date" value="{{ isset($intercommarketingdatum->last_seen_on_iOS_date) ? $intercommarketingdatum->last_seen_on_iOS_date : ''}}" >
    {!! $errors->first('last_seen_on_iOS_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('iOS_sessions') ? 'has-error' : ''}}">
    <label for="iOS_sessions" class="control-label">{{ 'Ios Sessions' }}</label>
    <input class="form-control" name="iOS_sessions" type="text" id="iOS_sessions" value="{{ isset($intercommarketingdatum->iOS_sessions) ? $intercommarketingdatum->iOS_sessions : ''}}" >
    {!! $errors->first('iOS_sessions', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('iOS_app_version') ? 'has-error' : ''}}">
    <label for="iOS_app_version" class="control-label">{{ 'Ios App Version' }}</label>
    <input class="form-control" name="iOS_app_version" type="text" id="iOS_app_version" value="{{ isset($intercommarketingdatum->iOS_app_version) ? $intercommarketingdatum->iOS_app_version : ''}}" >
    {!! $errors->first('iOS_app_version', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('iOS_device') ? 'has-error' : ''}}">
    <label for="iOS_device" class="control-label">{{ 'Ios Device' }}</label>
    <input class="form-control" name="iOS_device" type="text" id="iOS_device" value="{{ isset($intercommarketingdatum->iOS_device) ? $intercommarketingdatum->iOS_device : ''}}" >
    {!! $errors->first('iOS_device', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('iOS_os_version') ? 'has-error' : ''}}">
    <label for="iOS_os_version" class="control-label">{{ 'Ios Os Version' }}</label>
    <input class="form-control" name="iOS_os_version" type="text" id="iOS_os_version" value="{{ isset($intercommarketingdatum->iOS_os_version) ? $intercommarketingdatum->iOS_os_version : ''}}" >
    {!! $errors->first('iOS_os_version', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('last_seen_on_android_date') ? 'has-error' : ''}}">
    <label for="last_seen_on_android_date" class="control-label">{{ 'Last Seen On Android Date' }}</label>
    <input class="form-control" name="last_seen_on_android_date" type="text" id="last_seen_on_android_date" value="{{ isset($intercommarketingdatum->last_seen_on_android_date) ? $intercommarketingdatum->last_seen_on_android_date : ''}}" >
    {!! $errors->first('last_seen_on_android_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('android_sessions') ? 'has-error' : ''}}">
    <label for="android_sessions" class="control-label">{{ 'Android Sessions' }}</label>
    <input class="form-control" name="android_sessions" type="text" id="android_sessions" value="{{ isset($intercommarketingdatum->android_sessions) ? $intercommarketingdatum->android_sessions : ''}}" >
    {!! $errors->first('android_sessions', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('android_app_version') ? 'has-error' : ''}}">
    <label for="android_app_version" class="control-label">{{ 'Android App Version' }}</label>
    <input class="form-control" name="android_app_version" type="text" id="android_app_version" value="{{ isset($intercommarketingdatum->android_app_version) ? $intercommarketingdatum->android_app_version : ''}}" >
    {!! $errors->first('android_app_version', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('android_device') ? 'has-error' : ''}}">
    <label for="android_device" class="control-label">{{ 'Android Device' }}</label>
    <input class="form-control" name="android_device" type="text" id="android_device" value="{{ isset($intercommarketingdatum->android_device) ? $intercommarketingdatum->android_device : ''}}" >
    {!! $errors->first('android_device', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('android_os_version') ? 'has-error' : ''}}">
    <label for="android_os_version" class="control-label">{{ 'Android Os Version' }}</label>
    <input class="form-control" name="android_os_version" type="text" id="android_os_version" value="{{ isset($intercommarketingdatum->android_os_version) ? $intercommarketingdatum->android_os_version : ''}}" >
    {!! $errors->first('android_os_version', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('enabled_push_messaging') ? 'has-error' : ''}}">
    <label for="enabled_push_messaging" class="control-label">{{ 'Enabled Push Messaging' }}</label>
    <input class="form-control" name="enabled_push_messaging" type="text" id="enabled_push_messaging" value="{{ isset($intercommarketingdatum->enabled_push_messaging) ? $intercommarketingdatum->enabled_push_messaging : ''}}" >
    {!! $errors->first('enabled_push_messaging', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('is_mobile_unidentified') ? 'has-error' : ''}}">
    <label for="is_mobile_unidentified" class="control-label">{{ 'Is Mobile Unidentified' }}</label>
    <input class="form-control" name="is_mobile_unidentified" type="text" id="is_mobile_unidentified" value="{{ isset($intercommarketingdatum->is_mobile_unidentified) ? $intercommarketingdatum->is_mobile_unidentified : ''}}" >
    {!! $errors->first('is_mobile_unidentified', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('company_name') ? 'has-error' : ''}}">
    <label for="company_name" class="control-label">{{ 'Company Name' }}</label>
    <input class="form-control" name="company_name" type="text" id="company_name" value="{{ isset($intercommarketingdatum->company_name) ? $intercommarketingdatum->company_name : ''}}" >
    {!! $errors->first('company_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('company_id') ? 'has-error' : ''}}">
    <label for="company_id" class="control-label">{{ 'Company Id' }}</label>
    <input class="form-control" name="company_id" type="text" id="company_id" value="{{ isset($intercommarketingdatum->company_id) ? $intercommarketingdatum->company_id : ''}}" >
    {!! $errors->first('company_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('company_last_seen_date') ? 'has-error' : ''}}">
    <label for="company_last_seen_date" class="control-label">{{ 'Company Last Seen Date' }}</label>
    <input class="form-control" name="company_last_seen_date" type="text" id="company_last_seen_date" value="{{ isset($intercommarketingdatum->company_last_seen_date) ? $intercommarketingdatum->company_last_seen_date : ''}}" >
    {!! $errors->first('company_last_seen_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('company_created_at_date') ? 'has-error' : ''}}">
    <label for="company_created_at_date" class="control-label">{{ 'Company Created At Date' }}</label>
    <input class="form-control" name="company_created_at_date" type="text" id="company_created_at_date" value="{{ isset($intercommarketingdatum->company_created_at_date) ? $intercommarketingdatum->company_created_at_date : ''}}" >
    {!! $errors->first('company_created_at_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('people') ? 'has-error' : ''}}">
    <label for="people" class="control-label">{{ 'People' }}</label>
    <input class="form-control" name="people" type="text" id="people" value="{{ isset($intercommarketingdatum->people) ? $intercommarketingdatum->people : ''}}" >
    {!! $errors->first('people', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('company_web_sessions') ? 'has-error' : ''}}">
    <label for="company_web_sessions" class="control-label">{{ 'Company Web Sessions' }}</label>
    <input class="form-control" name="company_web_sessions" type="text" id="company_web_sessions" value="{{ isset($intercommarketingdatum->company_web_sessions) ? $intercommarketingdatum->company_web_sessions : ''}}" >
    {!! $errors->first('company_web_sessions', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('plan') ? 'has-error' : ''}}">
    <label for="plan" class="control-label">{{ 'Plan' }}</label>
    <input class="form-control" name="plan" type="text" id="plan" value="{{ isset($intercommarketingdatum->plan) ? $intercommarketingdatum->plan : ''}}" >
    {!! $errors->first('plan', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('monthly_spend') ? 'has-error' : ''}}">
    <label for="monthly_spend" class="control-label">{{ 'Monthly Spend' }}</label>
    <input class="form-control" name="monthly_spend" type="text" id="monthly_spend" value="{{ isset($intercommarketingdatum->monthly_spend) ? $intercommarketingdatum->monthly_spend : ''}}" >
    {!! $errors->first('monthly_spend', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('company_segment') ? 'has-error' : ''}}">
    <label for="company_segment" class="control-label">{{ 'Company Segment' }}</label>
    <textarea class="form-control" rows="5" name="company_segment" type="textarea" id="company_segment" >{{ isset($intercommarketingdatum->company_segment) ? $intercommarketingdatum->company_segment : ''}}</textarea>
    {!! $errors->first('company_segment', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('company_tag') ? 'has-error' : ''}}">
    <label for="company_tag" class="control-label">{{ 'Company Tag' }}</label>
    <input class="form-control" name="company_tag" type="text" id="company_tag" value="{{ isset($intercommarketingdatum->company_tag) ? $intercommarketingdatum->company_tag : ''}}" >
    {!! $errors->first('company_tag', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('company_size') ? 'has-error' : ''}}">
    <label for="company_size" class="control-label">{{ 'Company Size' }}</label>
    <input class="form-control" name="company_size" type="text" id="company_size" value="{{ isset($intercommarketingdatum->company_size) ? $intercommarketingdatum->company_size : ''}}" >
    {!! $errors->first('company_size', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('company_industry') ? 'has-error' : ''}}">
    <label for="company_industry" class="control-label">{{ 'Company Industry' }}</label>
    <input class="form-control" name="company_industry" type="text" id="company_industry" value="{{ isset($intercommarketingdatum->company_industry) ? $intercommarketingdatum->company_industry : ''}}" >
    {!! $errors->first('company_industry', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('company_website') ? 'has-error' : ''}}">
    <label for="company_website" class="control-label">{{ 'Company Website' }}</label>
    <input class="form-control" name="company_website" type="text" id="company_website" value="{{ isset($intercommarketingdatum->company_website) ? $intercommarketingdatum->company_website : ''}}" >
    {!! $errors->first('company_website', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('plan_name') ? 'has-error' : ''}}">
    <label for="plan_name" class="control-label">{{ 'Plan Name' }}</label>
    <input class="form-control" name="plan_name" type="text" id="plan_name" value="{{ isset($intercommarketingdatum->plan_name) ? $intercommarketingdatum->plan_name : ''}}" >
    {!! $errors->first('plan_name', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
