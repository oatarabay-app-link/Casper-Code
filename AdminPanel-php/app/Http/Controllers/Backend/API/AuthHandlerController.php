<?php

namespace App\Http\Controllers\Backend\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Firebase\JWT\JWT;
use App\Models\Auth\User;

/**
 * Class AuthHandlerController
 * @package App\Http\Controllers\Backend/API
 *
 */
class AuthHandlerController extends Controller
{



    /*
     * @Route POST /auth/token
     */
    public function auth_token(Request $request)
    {

        // Getting the Request to Decode According to Casper VPN App requirements
        $json = $request->getContent();
        $json = json_decode($json);
        // Calling a function to send the creadentials to Aouth server to to get tokens;
        $response = (array)json_decode($this->api_oauth_login($json->login, $json->password));
        //print_r($response);
        $credentials = array(
            "email"=>$json->login,
            "password"=>$json->password
        );

        \Log::debug($request,["Login Issues"]);

        if(!Auth::attempt($credentials))
            return response()->json([
                'code' => 'fail',
                'result' => 'Invalid Password',

            ], 401);

        $user = $request->user();
        $result = array();

        if (array_key_exists("access_token", $response)) {
            $result['code'] = "success";
            $result['data'] = array(
                "accessToken" => $response["access_token"],
                "accessTokenExpire" => ((float)$response["expires_in"]*1000), //Added 1000 multiplicatoin to read ms
                "refreshToken" => ($response["refresh_token"]), // /refresh_token/
                "authInfo" => array(
                    "displayName" => "$user->email",
                    "userId" => "$user->uuid",
                    "intHash" => $this->intercom_hash($user->uuid),
                    "intAppId" => "xmzqd1lt",
                    "role" => "USER"
                ),
            );
        } else {
            echo "Not Found";
        }
        \Log::debug($result);
        return response(json_encode($result, JSON_PRETTY_PRINT), 200)
            ->header('Content-Type', 'application/json');
        //return json_encode($result,JSON_PRETTY_PRINT);

    }

    /*
     *          * @Route /auth/token
     */
    public function refresh_token(Request $request)
    {

        // Getting the Request to Decode According to Casper VPN App requirements
        \Log::debug($request);
        $json = $request->getContent();
        $json = json_decode($json);


        $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEA1JxlFY6hkIdS/gTdLY4F
D/hAiw9B9akJa4WLnR5BXvWsPb/f59IbNT0PWj9gz3Y37bTmCQ6976rL16evnpQz
I4FwQC3veq0RNTSv3IOg9XKnTSR7mNh4oZYrCjYjrXNtHKyPZ07eIKPMIBtx3aFS
QdzPnu6QSwsYEchgPx2KIOyv16ok8+GBTkLO4FN7kjPq0nEhKCg5tlFMdIF7aB9N
QiV4iTbemKshutwsFaCzEM2BZ3qk+il/OX6HYm8Xa/OhSD9eEbRm+yv5R8VWg1g1
XSK8Lu2I+z+o3COILDYeMwIUv3EmUp/OUFIOC6TaaG+p3kkchXFPN1BpqMe341i1
B1SYlbaWt1chEJs4mErFtAKPHaulGuAkPEV270bXYtLdyXgyirwXcRn8Fvb5BBXP
V/20kqV3apNUDI9mDnu2ASw9r22lLTRCPwLzls+1cu6m8Ksf2b3XjTkUeGUa1iZX
HerRx7THqj7kzhI+bjtQJYnlXxCtVqcX6QmSj5UNPkB4cv6c/fePZMfbKTO+fJ5l
VI3hgdSqJ1BUrKpr3JuZGhVmtVw0TtGY9Mi8P7huI3Kf99yqIst6aSx9PF2SlDWV
tCoZUmQJk3XMmuK4YzQTG2n6hd0LMPKVAh53rf+9PUbLQ4im2r6CghjKK8vBSyZo
AWTmR4kGMgCpt5bNmgaw3ukCAwEAAQ==
-----END PUBLIC KEY-----
EOD;





        // Calling a function to send the credentials to oauth server to to get tokens;
        // We sent the refresh_token Received to teh Oauth Api to get a Refresh Token.
        $response = (array)json_decode($this->api_oauth_refresh_token($json->refreshToken));
        $result = array();

        if (array_key_exists("access_token", $response)) {
            $decoded = JWT::decode($response["access_token"], $publicKey, array('RS256'));
            $user_id=$decoded->sub;
            $user = \App\Models\Auth\User::findOrFail($user_id);
            $result['code'] = "success";
            $result['data'] = array(
                "accessToken" => $response["access_token"],
                "accessTokenExpire" => ((float)$response["expires_in"]*1000), //Added 1000 multiplicatoin to read ms
                "refreshToken" => $response["refresh_token"], // /refresh_token/
                "authInfo" =>  array(
                    "displayName" => $user->email,
                    "userId" => $user->uuid,
                    "intHash" => $this->intercom_hash($user->uuid),
                    "intAppId" => "xmzqd1lt",
                    "role" => "USER"
                ),
            );
        } else {
            echo "Not Found";
        }

        return response(json_encode($result, JSON_PRETTY_PRINT), 200)
            ->header('Content-Type', 'application/json');
        //return json_encode($result,JSON_PRETTY_PRINT);

    }

    // Function to send the auth to oauth for authentication.
    public function api_oauth_login($user, $pass)
    {
        $tokenRequest = Request::create('/oauth/token', 'POST');
        $tokenRequest->request->add([
            "client_id" => '6',
            "client_secret" => 'Isf5JeJRs9M1OqybvZZ3MfCoAQjhjKHUkaXCt8B5',
            "grant_type" => 'password',
            "username" => $user,
            "password" => $pass,
            "code" => '*',
        ]);

        $response = app()->handle($tokenRequest);
        $json = ($response->getContent());
        return $json;
    }
    // Function to send the auth to oauth for authentication. For refresh token
    public function api_oauth_refresh_token($refresh_token)
    {
        $tokenRequest = Request::create('/oauth/token', 'POST');
        $tokenRequest->request->add([
            "client_id"         => '6',
            "client_secret"     => 'Isf5JeJRs9M1OqybvZZ3MfCoAQjhjKHUkaXCt8B5',
            "grant_type" => 'refresh_token',
            "refresh_token" => $refresh_token

        ]);

        $response = app()->handle($tokenRequest);
        $json = ($response->getContent());
        return $json;
    }


    public function intercom_hash($message)
    {

        $secret = env("INTERCOM_SECRET");
        return hash_hmac("SHA256", $message,$secret);
    }


}
