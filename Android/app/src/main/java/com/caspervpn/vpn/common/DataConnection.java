package com.caspervpn.vpn.common;

import android.content.Context;
import android.os.AsyncTask;
import com.caspervpn.vpn.classes.User;
import com.caspervpn.vpn.screens.Change_Password;
import com.caspervpn.vpn.screens.Forgot_Password;
import com.caspervpn.vpn.screens.Landing_Page;
import com.caspervpn.vpn.screens.Loading;
import com.caspervpn.vpn.screens.Login;
import com.caspervpn.vpn.screens.Reset_Password;
import com.caspervpn.vpn.screens.Server_List;
import com.caspervpn.vpn.screens.Share_link;
import com.caspervpn.vpn.screens.Signup;
import com.caspervpn.vpn.screens.Social_Media;
import com.caspervpn.vpn.screens.resend_confirm_email;
import com.caspervpn.vpn.services.MyVpnService;
import com.caspervpn.vpn.services.Check_Recurring_Subscription;
import com.google.gson.Gson;
import org.json.JSONObject;
import java.util.Date;
import static com.caspervpn.vpn.common.Configuration.AFFILIATECONNECTION;
import static com.caspervpn.vpn.common.Configuration.AFFILIATEID;
import static com.caspervpn.vpn.common.Configuration.CHANGE_PASSWORD_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.FORGOT_PASSWORD_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.LOGIN_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.PAYMENT_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.RESEND_CONFIRM_EMAIL;
import static com.caspervpn.vpn.common.Configuration.RESET_PASSWORD_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.SERVER_LIST_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.SERVICE_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.SHEETSU;
import static com.caspervpn.vpn.common.Configuration.SHEETSUURL;
import static com.caspervpn.vpn.common.Configuration.SIGNUP_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.SUBSCRIPTION_CLASS;
import static com.caspervpn.vpn.common.Configuration.SUBSCRIPTION_CLASS_ID;
import static com.caspervpn.vpn.common.Configuration.user;

//importing OKhttp
import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public class DataConnection
{
    public static final MediaType JSON = MediaType.get("application/json; charset=utf-8");
    private String CONNECTION = Configuration.CONNECTION;
    private int HTTP_TIME_OUT = Configuration.HTTP_TIME_OUT;

    AsyncTask<String, Void, String[]> asynctask;

    String result = "";

    private String Function_ID;
    private String Caller_ID;
    private String URL;
    private String METHOD;
    private String BODY;
    private String AUTH;

    private Commun commun;
    private Login LoginClass;
    private Forgot_Password ForgotPasswordClass;
    private Signup SignupClass;
    private resend_confirm_email ResendEmailClass;
    private Reset_Password ResetPasswordClass;
    private Change_Password ChangePasswordClass;
    private Server_List ServerListClass;
    private MyVpnService ServiceClass;
    private com.caspervpn.vpn.screens.Subscribe Subscribe;
    private com.caspervpn.vpn.Subscriptions.Screens.Subscriptions Subscriptions;
//    private Free_B_Center Free_B_Center;
    private Social_Media Social_Media;
    private com.caspervpn.vpn.screens.Loading Loading;
    private Check_Recurring_Subscription Check_Recurring_Subscription;
    private Landing_Page LandingPage;
    private SubscriptionClass SubscriptionClass;
    private Share_link Share_link;

    public DataConnection(Login loginInstance){ LoginClass = loginInstance; }

    public DataConnection(Check_Recurring_Subscription check_Recurring_Subscription){ Check_Recurring_Subscription = check_Recurring_Subscription; }

    public DataConnection(Forgot_Password forgetpassword) { ForgotPasswordClass = forgetpassword;}

    public DataConnection(Signup signup)
    {
        SignupClass = signup;
    }

    //toufic sleiman
    public DataConnection(resend_confirm_email resendemail)
    {
        ResendEmailClass = resendemail;
    }

    public DataConnection(Loading loadingInstance){ Loading = loadingInstance; }

    public DataConnection(com.caspervpn.vpn.screens.Subscribe subscribeInstance){ Subscribe = subscribeInstance; }
    public DataConnection(com.caspervpn.vpn.Subscriptions.Screens.Subscriptions subscribeInstance){ Subscriptions = subscribeInstance; }

//    public DataConnection(Free_B_Center free_B_Center){ Free_B_Center = free_B_Center; }

    public DataConnection(Social_Media social_media){ Social_Media = social_media; }

    public DataConnection(Landing_Page landingPage){ LandingPage = landingPage; }

    public DataConnection(SubscriptionClass subscriptionClass)
    {
        SubscriptionClass = subscriptionClass;
    }

    public DataConnection(Share_link share_link){ Share_link = share_link; }
    //toufic sleiman

    public DataConnection(Change_Password changepassword)
    {
        ChangePasswordClass = changepassword;
    }

    public DataConnection(Reset_Password resetpassword)
    {
        ResetPasswordClass = resetpassword;
    }

    public DataConnection(Server_List server_list)
    {
        ServerListClass = server_list;
    }

    public DataConnection(MyVpnService service_class)
    {
        ServiceClass = service_class;
    }

    public DataConnection() {}

    /************************************************************************************************************/
    public  void GetData(int CallerID, String FctID, String url, String method, String body, Boolean auth, Context CallerContext)
    {
        Caller_ID = String.valueOf(CallerID);
        Function_ID = FctID;
        URL = url;
        METHOD = method;
        BODY = body;
        AUTH = auth.toString();

        commun = new Commun(CallerContext);

        asynctask = new GetDataTask().execute(Caller_ID, Function_ID, URL, METHOD, BODY, AUTH);
    }

    private  class GetDataTask extends AsyncTask<String, Void, String[]>
    {
        protected String[] doInBackground(String... urls)
        {

            if(android.os.Debug.isDebuggerConnected())
                android.os.Debug.waitForDebugger();


            String [] Result = {"-1", urls[0], urls[1], urls[2], urls[3]};
            try
            {
                result = DataAccess (urls[2], urls[3], urls[4], urls[5]);
                if (result.equals("-1")) return Result;


                Result[0] = "1";
                return Result;
            }
            catch(Exception e) {e.printStackTrace(); return Result;}
        }
        protected void onPostExecute(String[] urls)
        {
            switch (Integer.parseInt(urls[1]))
            {
                case LOGIN_CLASS_ID:
                {
                    switch (Integer.parseInt(urls[2]))
                    {
                        case 1:	LoginClass.OnLoginResult(result); break;
                        case 2:	LoginClass.OnUserProfileResult(result); break;
                        case 3:	LoginClass.OnServerResult(result); break;
                        case 4:	LandingPage.OnResultIsNewVersionExist(result); break;
                        default:
                    }
                    break;
                }

                case FORGOT_PASSWORD_CLASS_ID:
                {
                    switch (Integer.parseInt(urls[2]))
                    {
                        case 1:	ForgotPasswordClass.OnResetResult(result); break;

                        default:
                    }
                    break;
                }

                case SIGNUP_CLASS_ID:
                {
                    switch (Integer.parseInt(urls[2]))
                    {
                        case 1:	SignupClass.OnSignUpResult(result, 1); break; // register
                        case 2:	SignupClass.OnSignUpResult(result, 2); break; // verify email
                        default:
                    }
                    break;
                }

                case RESET_PASSWORD_CLASS_ID:
                {
                    switch (Integer.parseInt(urls[2]))
                    {
                        case 1:	ResetPasswordClass.OnSaveNewPassword(result); break;

                        default:
                    }
                    break;
                }

                case CHANGE_PASSWORD_CLASS_ID:
                {
                    switch (Integer.parseInt(urls[2]))
                    {
                        case 1:	ChangePasswordClass.OnChangePasswordResult(result); break;
                        case 2:	ChangePasswordClass.OnResetResult(result); break;
                        default:
                    }
                    break;
                }

                case SERVER_LIST_CLASS_ID:
                {
                    switch (Integer.parseInt(urls[2]))
                    {
                        case 1:	ServerListClass.OnGetServersResult(result); break;

                        default:
                    }
                    break;
                }

                case SERVICE_CLASS_ID:
                {
                    switch (Integer.parseInt(urls[2]))
                    {
                        case 1:	ServiceClass.OnUserProfileResult(result); break;
                        case 2:	ServiceClass.OnServerResult(result); break;
                        default:
                    }
                    break;
                }

                    /*   Toufic sleiman  */

                case SUBSCRIPTION_CLASS_ID:
                {
                    switch (Integer.parseInt(urls[2]))
                    {
                        //note it was subsscibe m7 replaced it with Subscriptions  20191101
                        case 1:	Subscriptions.OnSubscriptionsResult(result); break;
                        case 2:	Subscriptions.OnUpdateExpiryDateResult(result); break;
                        case 3:	Subscriptions.OnUpdateAppExpiryDateResult(result); break;
                        default:
                    }
                    break;
                }
                case RESEND_CONFIRM_EMAIL:
                {
                    switch (Integer.parseInt(urls[2]))
                    {
                        case 1:	SignupClass.OnResendEmailResult(result); break;
                        case 2:	ResendEmailClass.OnResendEmailResult(result); break;
                        default:
                    }
                    break;
                }
                case PAYMENT_CLASS_ID:
                {
                    switch (Integer.parseInt(urls[2]))
                    {
                        //note Case 1 was Subscribe M7 changed to new 20191101
                        case 1:	Subscriptions.OnGetPaymentResult(result); break;
                        case 2:	Check_Recurring_Subscription.OnGetPaymentResult(result); break;
                        case 3:	Check_Recurring_Subscription.OnUpdateExpiryDateResult(result); break;
                        case 4:	Check_Recurring_Subscription.OnUpdateAppExpiryDateResult(result); break;
                        default:
                    }
                    break;
                }
                case SUBSCRIPTION_CLASS:
                {
                    switch (Integer.parseInt(urls[2]))
                    {
                        case 1:	SubscriptionClass.OnGetPaymentResult(result); break;
                        case 3:	SubscriptionClass.OnUpdateExpiryDateResult(result); break;
                        case 4:	SubscriptionClass.OnUpdateAppExpiryDateResult(result); break;
                        default:
                    }
                    break;
                }
                case AFFILIATEID:
                {
                    switch (Integer.parseInt(urls[2]))
                    {
                        case 1:	Share_link.OnRefreshAffiliate(result); break;
                        case 2:	Share_link.OnCreateAffiliateProfile(result); break;
                        default:
                    }
                    break;
                }
                case SHEETSU:
                {
                    switch (Integer.parseInt(urls[2]))
                    {
                        case 1:	Loading.OnGetCountryURLResult(result); break;
                        default:
                    }
                    break;
                }
                /*   Toufic sleiman   */

                default:

            }
        }
    }



    /*---------------------------------------------------Data Access------------------------------------------------*/
    public  String DataAccess (String url, String method, String body, String auth)
    {
        try
        {
            String fixconnection = new Gson().fromJson(commun.LoadClassFromPreference("fixcountryurl"), String.class);
            if(fixconnection != null) CONNECTION = fixconnection;
            long current_time = new Date().getTime();

            if (auth.equals("true") && user != null && user.getTokenExpire()  < current_time )
            {
                Refresh_Token();
            }
            String URLString = CONNECTION + url;

            if (url.contains(".php")){
                URLString = AFFILIATECONNECTION + url;
            }
            else if (url.equals("sheetsu")){
                URLString = SHEETSUURL;
            }

            commun.Log(method + "   " + URLString);

            OkHttpClient client = new OkHttpClient();

            String Stringresponse = null;

            if (method.equals("POST"))
            {

                RequestBody rbody = RequestBody.create(JSON, body);

                if (Boolean.parseBoolean(auth)){

                    Request request = new Request.Builder()
                            .header("Accept", "application/json")
                            .header("Accept", "application/json")
                            .header("Authorization", "Bearer " + user.getToken())
                            .post(rbody)
                            .url(URLString)
                            .build();
                    Response response = client.newCall(request).execute();
                    Stringresponse= response.body().string();
                }else{

                    Request request = new Request.Builder()
                            .header("Accept", "application/json")
                            .header("Accept", "application/json")
                            .post(rbody)
                            .url(URLString)
                            .build();
                    Response response = client.newCall(request).execute();
                 Stringresponse= response.body().string();
                }



//
            }
            else if (method.equals("PUT"))
            {
                RequestBody rbody = RequestBody.create(JSON, body);
                if (Boolean.parseBoolean(auth)){
                    Request request = new Request.Builder()
                            .header("Accept", "application/json")
                            .header("Accept", "application/json")
                            .header("Authorization", "Bearer " + user.getToken())
                            .put(rbody)

                            .url(URLString)
                            .build();
                    Response response = client.newCall(request).execute();
                    Stringresponse= response.body().string();
                }else{
                    Request request = new Request.Builder()
                            .header("Accept", "application/json")
                            .header("Accept", "application/json")
                            .put(rbody)
                            .url(URLString)
                            .build();
                    Response response = client.newCall(request).execute();
                    Stringresponse= response.body().string();
                }
            }
            else if (method.equals("GET"))
            {
                if (Boolean.parseBoolean(auth)){
                    Request request = new Request.Builder()
                            .header("Accept", "application/json")
                            .header("Accept", "application/json")
                            .header("Authorization", "Bearer " + user.getToken())
                            .get()
                            .url(URLString)
                            .build();
                    Response response = client.newCall(request).execute();
                    Stringresponse= response.body().string();
                }else{

                    Request request = new Request.Builder()
                            .header("Accept", "application/json")
                            .header("Accept", "application/json")
                            .get()
                            .url(URLString)
                            .build();
                    Response response = client.newCall(request).execute();
                    Stringresponse= response.body().string();
                }


            }
            return Stringresponse;
        }
        catch(Exception e)
        {
            e.printStackTrace();
            return null;
        }
    }

    private void Refresh_Token() throws Exception
    {
        String fixconnection = new Gson().fromJson(commun.LoadClassFromPreference("fixcountryurl"), String.class);
        if(fixconnection != null) CONNECTION = fixconnection;
        String URLString = CONNECTION + "auth/token";
        commun.Log("PUT   " + URLString);
        commun.Log("Refresh Token   " + URLString);
        String Stringresponse = null;
        OkHttpClient client = new OkHttpClient();
        RequestBody rbody = RequestBody.create(JSON, new JSONObject().put("refreshToken", user.getRefreshToken()).toString());
        Request request = new Request.Builder()
                .header("Accept", "application/json")
                .header("Accept", "application/json")
                .header("Authorization", "Bearer " + user.getToken())
                .put(rbody)
                .url(URLString)
                .build();
        Response response = client.newCall(request).execute();
        Stringresponse= response.body().string();
        String RefreshTokenResult = Stringresponse;
        JSONObject j = new JSONObject(RefreshTokenResult);
        String code = j.getString("code");
        if (code.equals("success"))
        {
            JSONObject data = j.getJSONObject("data");
            try
            {
                JSONObject userInfo = data.getJSONObject("authInfo");
                //String userid ,String username, String email, String password, String token, int tokenExpire
                user = new User
                        (
                                userInfo.getString("userId"),
                                userInfo.getString("displayName"),
                                userInfo.getString("displayName"),//Email
                                data.getString("accessToken"),
                                data.getString("refreshToken"),
                                data.getLong("accessTokenExpire"),
                                userInfo.getString("intHash"),
                                userInfo.getString("intAppId")
                        );
            }
            catch (Exception e)
            {
                user.setToken(data.getString("accessToken"));
                user.setRefreshToken(data.getString("refreshToken"));
                user.setTokenExpire(data.getLong("accessTokenExpire"));
            }
            commun.SaveClassToPreference(user, "user");
        }
    }



    public void Flush()
    {
        if (asynctask != null) asynctask.cancel(true);
    }
}