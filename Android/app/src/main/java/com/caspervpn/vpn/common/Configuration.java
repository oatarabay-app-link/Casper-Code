package com.caspervpn.vpn.common;

import com.caspervpn.vpn.classes.Payment;
import com.caspervpn.vpn.classes.Server;
import com.caspervpn.vpn.classes.Server_Array;
import com.caspervpn.vpn.classes.User;
import com.caspervpn.vpn.classes.UserProfile;
import com.caspervpn.vpn.screens.Landing_Page;
import com.caspervpn.vpn.screens.Server_List;
import com.caspervpn.vpn.services.MyVpnService;
import java.util.Random;
public class Configuration
{
//    not working because vpn servers api should be working in dev mode to be able to login
//    public static final String CONNECTION = "https://vpndev.online/api/";
    public static final String[] API_URLS = {"https://confirmemail.online/api/",
                                             "https://peoplesay.us/api/",
                                             "https://caspervpnofficial.com/api/",
                                             //"https://openmarkt.live/api/",
                                             //"https://opnmarket.club/api/",
                                             //"https://opnmarket.live/api/",
                                             "https://peoplesay.us/api/",
                                             "https://positivetalk.xyz/api/",
                                             //"https://souksales.info/api/",
                                             //"https://souksales.site/api/",
                                            };
    //public static final String CONNECTION = "http://192.168.0.110/api/";  //API_URLS[new Random().nextInt(API_URLS.length)];//"https://confirmemail.online/api/";
    public static final String CONNECTION = API_URLS[new Random().nextInt(API_URLS.length)];//"https://confirmemail.online/api/";
    public static final String AFFILIATECONNECTION = "https://www.applinkoffshore.com/affiliate.caspervpn.com/apis/";
    public static final String CasperVPNProfileName = "CasperVPN";
    public static final String SHEETSUURL = "https://sheetsu.com/apis/v1.0su/58919cab4dce";

    public static final String LICENSE = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArri3ZNncFZpmsALnMmFZQOrpH85Tu4GahS/y2+e670Ru8LoGWwum/aBUy0c1QKV0JexxtGnt8EHtEjZje/sxVYu8DR7L0Jg4txXNXW8ujJHAmoCSTlgXdxXBVFvYEnzyfx8s6LxY+r3k5KOr51cYUnniMrMb4SFKNPUeVFI/+8NBtpSenzHCpfmN+SnSc35MScLkT7KtjhJPVM396cEN2+ElqqJTkmTwqByffT7DimZPqrJElIuWMkXIkJALTnyyK8Y7jqimGmkBhkkBKt7rPi3qxz/7lIwVDgFlz7WLMSD+8sIOfQv/xdhKrKU1MG61rDitDGdfxoCYgHyoJPuSlwIDAQAB";

    public static final String POLLFISH = "a2133f21-8313-45e8-8c3d-58c2503a44ad";
    public static final String APPODEAL = "2732d236db7cd35dfbcb85b4a2344e4505033b8f36b5456d";

    public static final String TWITTER_SECRET1 = "9QoHBdhdOY";
    public static final String TWITTER_SECRET2 = "[{srsmNjVE";
    public static final String TWITTER_SECRET3 = "pz1B5i1wUY";
    public static final String TWITTER_SECRET4 = "Hjrx:TLFmF";
    public static final String TWITTER_SECRET5 = "RPm27msj6Q";

    public static final String TWITTER_KEY1 = "HmmzGx7I";
    public static final String TWITTER_KEY2 = "ZB0Jxt4ja";
    public static final String TWITTER_KEY3 = "730bNVHC";

    public static final String TERMS_CONDITIONS = "https://caspervpn.com/term-and-conditions.html";
    public static final String PRIVACY_POLICY = "https://caspervpn.com/term-and-conditions.html#privacy-policy";

    public static final String VPNConnectionURL = "https://caspervpn.com/";
    public static final String WebsiteURL = "https://caspervpn.com/";

    public static final String IntercomApiKeyProd = "android_sdk-116158e537f0c829bf93abe93dde3db531ec126f";
    public static final String IntercomAppIdProd = "xmzqd1lt";
    public static final String Support_Email = "support@caspervpn.com";
    public static final String Facebook_URL = "https://www.facebook.com/caspervpn/";
    public static final String GooglePlus_Url = "https://plus.google.com/u/0/109798282392932151387";

    public final static long DAYS_UNTIL_RATEUS_PROMPT = 30;                  //Min number of days
    public final static long LAUNCHES_UNTIL_RATEUS_PROMPT = 3;               //Min number of launches
    public final static long DAYS_UNTIL_RATEUS_REMINDER_PROMPT = 15;         //Min number of reminder days

    public static final long LOADING_WAITING_TIME = 500;                    //0.5 sec
    public static final long CONNET_WAITING_TIME = 60 * 1000;                    //1 sec
    public static final long CONNECTION_BUG_REFRESH_INTERVAL = 30 * 1000;    //30 sec
    public static final long SIMULTANIOUS_CONNECTION_BUG_INTERVAL = 100;     //100 millisecond
    public static final long BACKGROUND_REFRESH_INTERVAL = 60 * 1000;       // 50 second
    public static final long LOCATION_REFRESH_INTERVAL = 1 * 60 * 1000;      //1 minutes
    public static final int HTTP_TIME_OUT = 60 * 1000;                       //15 sec
    public static final long VPN_CONNECTION_TIMEOUT = 40 * 1000;             //40 sec

    public static final int OPEN_SETTINGS = -1;
    public static final int VPN_PERMISSION_REQUEST = 70;
    public static final int ACCESS_LOCATION_REQUEST = 100;

    public static int RESULT_OK = -1,  RESULT_CANCELLED = 0;

    public static final int LOGIN_CLASS_ID = 1;
    public static final int FORGOT_PASSWORD_CLASS_ID = 2;
    public static final int SIGNUP_CLASS_ID = 3;
    public static final int RESET_PASSWORD_CLASS_ID = 4;
    public static final int CHANGE_PASSWORD_CLASS_ID = 5;
    public static final int SERVER_LIST_CLASS_ID = 6;
    public static final int SERVICE_CLASS_ID = 7;
    public static final int PAYMENT_CLASS_ID = 8;   /* toufic sleiman 20-9-17 */
    public static final int SUBSCRIPTION_CLASS_ID = 9;   /* toufic sleiman 20-9-17 */
    public static final int RESEND_CONFIRM_EMAIL = 10;   /* toufic sleiman 10-11-17 */
    public static final int FREE_BEE_CENTER = 11;   /* toufic sleiman 2-6-18 */
    public static final int FREE_BEE_CENTER_SOCIAL_MEDIA = 12;   /* toufic sleiman 2-6-18 */
    public static final int SUBSCRIPTION_CLASS = 13;   /* toufic sleiman 2-6-18 */
    public static final int AFFILIATEID = 14;
    public static final int SHEETSU = 15;

    public static User user;
    public static Server_Array servers;
    public static UserProfile userprofile;
    public static Payment payment;  /* toufic sleiman 21-9-17 */
    //public static Payment beefreecenter;  /* toufic sleiman 2-6-18 */
    public static Payment paymentClass;  /* toufic sleiman 2-6-18 */

    public static final boolean pollfishRelease = true;  // should be = true in prod mode
    public static final boolean pollfishCustomMode = true;

    public static MyVpnService MyVpnServiceInstance;
    public static Landing_Page LandingPageInstance;
    public static Server_List ServerListInstance;
    public static Server SelectedServer, ServerListSelectedServer;

    public static boolean AlwaysShowInstructions = false;
    public static boolean DefaultMapView = false;
    public static boolean AlwaysShowMapHint = false;
    public static boolean AlwaysShowRateUsDialog = false;
    public static boolean SetDefaultLoginCredentials = false;
    public static boolean AlwaysInactiveSubscription = false;
    public static boolean IsDebugMode = false;    //change
    ////////////////////////////////////////////////////////////////////////////////

    // public static final String SKU_ONE_WEEK = "caspervpn.1week.subscription"; //caspervpn.1month.subscription
    public static final String SKU_ONE_MONTH = "caspervpn.1month.subscription";
    public static final String SKU_SIX_MONTHS = "caspervpn.6months.subscription";
    public static final String SKU_ONE_YEAR = "caspervpn.12months.subscription";
    public static final String SKU_LIFETIME = "caspervpn.lifetime.subscription";

    public static final int WATCH_VIDEO_HOURS = 3;
    public static final int ONE_HOURS = 3600000;  // one hour

    // google Analytics Words

    public static final String SignUpsCategory = "SignUps";
    public static final String SubscriptionsCategory = "Subscriptions";
    public static final String FreePremiumCategory = "Free Premium";
    public static final String SocialMediaCategory = "Share Social Media";
    public static final String ShareAffiliateCategory = "Share Affiliate";
    public static final String ConnectedCategory = "Connected";

    public static final String ResendAction = "User Resend";
    public static final String SignInAction = "User SignIn";
    public static final String UnconfirmedAction = "User Unconfirmed";

    public static final String Subscribed1MAction = "User Subscribed 1 M";
    public static final String Subscribed6MAction = "User Subscribed 6 M";
    public static final String Subscribed1YAction = "User Subscribed 1 Y";
    public static final String SubscribedLifetimeAction = "User Subscribed Lifetime";
    public static final String UnSubscribedAction = "User UnSubscribed";
    public static final String Recurring1MAction = "User Recurring 1 M";
    public static final String Recurring6MAction = "User Recurring 6 M";
    public static final String Recurring1YAction = "User Recurring 1 Y";

    public static final String RewardVideoMAction = "Reward Video";
    public static final String SurveyAction = "Survey";
    public static final String FbShareAction = "Fb Share";
    public static final String TwitterShareAction = "Twitter Share";
    public static final String GooglePlusShareAction = "Google Plus Share";

    public static final String ShareAffiliate = "Image~" + "Share_Affiliate_Link";
    public static final String SocialMediaScreenName = "Image~" + "SocialMedia_Page";
    public static final String Edit_Profile_Page = "Image~Edit_Profile_Page";
    public static final String MapPageScreenName = "Image~" + "Map_Page";
    public static final String EarthPageScreenName = "Image~" + "Earth_Page";
    public static final String SubscribeScreenName = "Image~" + "Subscribe_Page";
    public static final String ServerDetailsScreenName = "Image~" + "ServerDetails";
    public static final String ServerListScreenName = "Image~" + "ServerList";
    public static final String AboutUsScreenName = "Image~" + "About_Us";
    public static final String FreeBeeCenterScreenName = "Image~" + "GetFreePremium_Page";
    public static final String MainMenuScreenName = "Image~" + "Menu_Screen";
    public static final String GettingStartedScreenName = "Image~" + "GettingStarted";
    public static final String ResendEmailScreenName = "Image~" + "resend_confirm_email_Page";
    public static final String LoginScreenName = "Image~" + "Login_Page";
    public static final String SignUpScreenName = "Image~" + "SignUp_Page";

    // google Analytics Words
}


