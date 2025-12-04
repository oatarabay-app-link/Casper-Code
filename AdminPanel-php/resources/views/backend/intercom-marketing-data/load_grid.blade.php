@extends('backend.layouts.app')

@section('content')
    <div class="col-md-12">
        <div class="card">
                    <div class="card-header">Intercom Marketing Data</div>
                    <div class="card-body">
                        {{--<a href="{{ url('/admin/intercom-marketing-data/create') }}" class="btn btn-success btn-sm" title="Add New IntercomMarketingDatum">--}}
                            {{--<i class="fa fa-plus" aria-hidden="true"></i> Add New--}}
                        {{--</a>--}}

{{--                        <form method="GET" action="{{ url('/admin/intercom-marketing-data') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">--}}
{{--                            <div class="input-group">--}}
{{--                                <input type="text" class="form-control" name="search" placeholder="Search..." value="{{ request('search') }}">--}}
{{--                                <span class="input-group-append">--}}
{{--                                    <button class="btn btn-secondary" type="submit">--}}
{{--                                        <i class="fa fa-search"></i>--}}
{{--                                    </button>--}}
{{--                                </span>--}}
{{--                            </div>--}}
{{--                        </form>--}}

{{--                        <br/>--}}
{{--                        <br/>--}}
{{--                        <div class="table-responsive">--}}
{{--                            <table class="table">--}}
{{--                                <thead>--}}
{{--                                    <tr>--}}
{{--                                        <th>#</th><th>First Name</th><th>Last Name</th><th>Name</th><th>Owner</th><th>Lead Category</th><th>Conversation Rating</th><th>Email</th><th>Phone</th><th>User Uuid</th><th>First Seen Date</th><th>Signed Up Date</th><th>Last Seen Date</th><th>Last Contacted Date</th><th>Last Heard From Date</th><th>Last Opened Email Date</th><th>Last Clicked On Link In Email Date</th><th>Web Sessions</th><th>Country</th><th>Region</th><th>City</th><th>Timezone</th><th>Browser Language</th><th>Language Override</th><th>Browser</th><th>Browser Version</th><th>Os</th><th>Twitter Followers</th><th>Job Title</th><th>Segment</th><th>Tag</th><th>Unsubscribed From Emails</th><th>Marked Email As Spam</th><th>Has Hard Bounced</th><th>Utm Campaign</th><th>Utm Content</th><th>Utm Medium</th><th>Utm Source</th><th>Utm Term</th><th>Referral Url</th><th>Job Title</th><th>Subscribed</th><th>Pending</th><th>Unsubscribed</th><th>Last Connected</th><th>Canceled Subscription</th><th>Connected</th><th>Free Premium</th><th>Signed Up Appversion</th><th>1 Year</th><th>Lifetime Subscription</th><th>Last Seen On IOS Date</th><th>IOS Sessions</th><th>IOS App Version</th><th>IOS Device</th><th>IOS Os Version</th><th>Last Seen On Android Date</th><th>Android Sessions</th><th>Android App Version</th><th>Android Device</th><th>Android Os Version</th><th>Enabled Push Messaging</th><th>Is Mobile Unidentified</th><th>Company Name</th><th>Company Id</th><th>Company Last Seen Date</th><th>Company Created At Date</th><th>People</th><th>Company Web Sessions</th><th>Plan</th><th>Monthly Spend</th><th>Company Segment</th><th>Company Tag</th><th>Company Size</th><th>Company Industry</th><th>Company Website</th><th>Plan Name</th><th>Actions</th>--}}
{{--                                    </tr>--}}
{{--                                </thead>--}}
{{--                                <tbody>--}}
{{--                                @foreach($intercommarketingdata as $item)--}}
{{--                                    <tr>--}}
{{--                                        <td>{{ $loop->iteration }}</td>--}}
{{--                                        <td>{{ $item->first_name }}</td><td>{{ $item->last_name }}</td><td>{{ $item->name }}</td><td>{{ $item->owner }}</td><td>{{ $item->lead_category }}</td><td>{{ $item->conversation_rating }}</td><td>{{ $item->email }}</td><td>{{ $item->phone }}</td><td>{{ $item->user_uuid }}</td><td>{{ $item->first_seen_date }}</td><td>{{ $item->signed_up_date }}</td><td>{{ $item->last_seen_date }}</td><td>{{ $item->last_contacted_date }}</td><td>{{ $item->last_heard_from_date }}</td><td>{{ $item->last_opened_email_date }}</td><td>{{ $item->last_clicked_on_link_in_email_date }}</td><td>{{ $item->web_sessions }}</td><td>{{ $item->country }}</td><td>{{ $item->region }}</td><td>{{ $item->city }}</td><td>{{ $item->timezone }}</td><td>{{ $item->browser_language }}</td><td>{{ $item->language_override }}</td><td>{{ $item->browser }}</td><td>{{ $item->browser_version }}</td><td>{{ $item->os }}</td><td>{{ $item->twitter_followers }}</td><td>{{ $item->job_title }}</td><td>{{ $item->segment }}</td><td>{{ $item->tag }}</td><td>{{ $item->unsubscribed_from_emails }}</td><td>{{ $item->marked_email_as_spam }}</td><td>{{ $item->has_hard_bounced }}</td><td>{{ $item->utm_campaign }}</td><td>{{ $item->utm_content }}</td><td>{{ $item->utm_medium }}</td><td>{{ $item->utm_source }}</td><td>{{ $item->utm_term }}</td><td>{{ $item->referral_url }}</td><td>{{ $item->job_title }}</td><td>{{ $item->subscribed }}</td><td>{{ $item->pending }}</td><td>{{ $item->unsubscribed }}</td><td>{{ $item->last_connected }}</td><td>{{ $item->canceled_subscription }}</td><td>{{ $item->connected }}</td><td>{{ $item->free_premium }}</td><td>{{ $item->signed_up_appversion }}</td><td>{{ $item->year_1 }}</td><td>{{ $item->lifetime_subscription }}</td><td>{{ $item->last_seen_on_iOS_date }}</td><td>{{ $item->iOS_sessions }}</td><td>{{ $item->iOS_app_version }}</td><td>{{ $item->iOS_device }}</td><td>{{ $item->iOS_os_version }}</td><td>{{ $item->last_seen_on_android_date }}</td><td>{{ $item->android_sessions }}</td><td>{{ $item->android_app_version }}</td><td>{{ $item->android_device }}</td><td>{{ $item->android_os_version }}</td><td>{{ $item->enabled_push_messaging }}</td><td>{{ $item->is_mobile_unidentified }}</td><td>{{ $item->company_name }}</td><td>{{ $item->company_id }}</td><td>{{ $item->company_last_seen_date }}</td><td>{{ $item->company_created_at_date }}</td><td>{{ $item->people }}</td><td>{{ $item->company_web_sessions }}</td><td>{{ $item->plan }}</td><td>{{ $item->monthly_spend }}</td><td>{{ $item->company_segment }}</td><td>{{ $item->company_tag }}</td><td>{{ $item->company_size }}</td><td>{{ $item->company_industry }}</td><td>{{ $item->company_website }}</td><td>{{ $item->plan_name }}</td>--}}
{{--                                        <td>--}}
{{--                                            <a href="{{ url('/admin/intercom-marketing-data/' . $item->id) }}" title="View IntercomMarketingDatum"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>--}}
{{--                                            <a href="{{ url('/admin/intercom-marketing-data/' . $item->id . '/edit') }}" title="Edit IntercomMarketingDatum"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>--}}

{{--                                            <form method="POST" action="{{ url('/admin/intercom-marketing-data' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">--}}
{{--                                                {{ method_field('DELETE') }}--}}
{{--                                                {{ csrf_field() }}--}}
{{--                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete IntercomMarketingDatum" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>--}}
{{--                                            </form>--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
{{--                                @endforeach--}}
{{--                                </tbody>--}}
{{--                            </table>--}}
{{--                            <div class="pagination-wrapper"> {!! $intercommarketingdata->appends(['search' => Request::get('search')])->render() !!} </div>--}}
{{--                        </div>--}}
                        {!! $grid !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
