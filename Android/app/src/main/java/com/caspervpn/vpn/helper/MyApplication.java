package com.caspervpn.vpn.helper;

import androidx.multidex.MultiDexApplication;

import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.common.Configuration;
import com.crashlytics.android.Crashlytics;
import com.google.android.gms.analytics.GoogleAnalytics;
import com.google.android.gms.analytics.HitBuilders;
import com.google.android.gms.analytics.Tracker;

import de.blinkt.openvpn.core.PRNGFixes;
import de.blinkt.openvpn.core.StatusListener;
import io.fabric.sdk.android.Fabric;
import io.intercom.android.sdk.Intercom;

import static com.caspervpn.vpn.common.Configuration.IntercomApiKeyProd;
import static com.caspervpn.vpn.common.Configuration.IntercomAppIdProd;

//@ReportsCrashes //old crash reporting system
//        (
//            mailTo = Configuration.Support_Email,
//            mode = ReportingInteractionMode.DIALOG,
//            customReportContent =
//                    {
//                        ReportField.APP_VERSION_CODE,
//                        ReportField.APP_VERSION_NAME,
//                        ReportField.USER_CRASH_DATE,
//                        ReportField.ANDROID_VERSION,
//                        ReportField.PHONE_MODEL,
//                        ReportField.BUILD,
//                        ReportField.USER_COMMENT,
//                        ReportField.STACK_TRACE,
//                        ReportField.LOGCAT
//                    },
//            resDialogText = R.string.crash_dialog_text,
//            resDialogIcon =  R.mipmap.alert_green,
//            resDialogTitle = R.string.crash_dialog_title,
//            resDialogCommentPrompt = R.string.crash_dialog_comment_prompt,
//            resDialogTheme = R.style.AppTheme
//        )

public class MyApplication  extends MultiDexApplication
{

    private StatusListener mStatus;
    private static GoogleAnalytics sAnalytics;
    private static Tracker sTracker;

    //Toufic 7/3/2017 -- google analytics --
    private static MyApplication mInstance;
    //Toufic 7/3/2017 -- google analytics --

    @Override
    public void onCreate()
    {
        super.onCreate();
        PRNGFixes.apply();
        Commun commun = new Commun(this);

        //Crashlytics
        if (!Configuration.IsDebugMode) Fabric.with(this, new Crashlytics());

        //Intercom test  toufic sleiman
        //Intercom.initialize(this, IntercomApiKey1 + commun.Reverse(commun.Increment(IntercomApiKey2)) + commun.Reverse(commun.Decrement(IntercomApiKey3)), commun.Reverse(IntercomAppId));

        //Intercom prod  toufic sleiman
        try{ Intercom.initialize(this, IntercomApiKeyProd, IntercomAppIdProd);}catch (Exception ex){ex.printStackTrace();commun.Log(ex.toString());}

        //INTERCOM PRODUCTION
        //Intercom.initialize(this, "android_sdk-116158e537f0c829bf93abe93dde3db531ec126f", "xmzqd1lt");

        mStatus = new StatusListener();
        mStatus.init(getApplicationContext());

        //Toufic 7/3/2017 -- google analytics --
        mInstance = this;
        AnalyticsTrackers.initialize(this);
        AnalyticsTrackers.getInstance().get(AnalyticsTrackers.Target.APP);
        //Toufic 7/3/2017 -- google analytics --
    }

    //Toufic 7/3/2017 -- google analytics --
    public static synchronized MyApplication getInstance() {
        return mInstance;
    }

    public synchronized Tracker getGoogleAnalyticsTracker() {
        AnalyticsTrackers analyticsTrackers = AnalyticsTrackers.getInstance();
        return analyticsTrackers.get(AnalyticsTrackers.Target.APP);
    }

    public void trackEvent(String category, String action, String label) {
        Tracker t = getGoogleAnalyticsTracker();

        // Build and send an Event.
        t.send(new HitBuilders.EventBuilder().setCategory(category).setAction(action).setLabel(label).build());
    }


    public void trackEventValue(String category, String action, String label, Long value) {
        Tracker t = getGoogleAnalyticsTracker();

        // Build and send an Event.
        t.send(new HitBuilders.EventBuilder().setCategory(category).setAction(action).setLabel(label).setValue(value).build());
    }

    public void trackScreenView(String screenName) {
        Tracker t = getGoogleAnalyticsTracker();

        // Set screen name.
        t.setScreenName(screenName);

        // Send a screen view.
        t.send(new HitBuilders.ScreenViewBuilder().build());

        GoogleAnalytics.getInstance(this).dispatchLocalHits();
    }
    //Toufic 7/3/2017 -- google analytics --
}
