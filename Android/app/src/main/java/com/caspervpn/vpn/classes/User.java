package com.caspervpn.vpn.classes;

/**
 * Created by zaherZ on 1/3/2017.
 */

public class User
{
    private String email;
    private String token;
    private String refreshToken;
    private String username;
    private String userid;
    private String intHash;
    private String intAppId;
    private long tokenExpire;
    private boolean UserConnectServerDetails = false;  // toufic sleiman 6-11-2017


    //setting all user's info
    public User(String userid ,String username,String email, String token, String refreshToken, long tokenExpire, String intHash, String intAppId)
    {
        this.userid=userid;
        this.username=username;
        this.email=email;
        this.token=token;
        this.refreshToken = refreshToken;
        this.tokenExpire=tokenExpire;
        this.intHash= intHash;
        this.intAppId= intAppId;
    }

    public String getIntHash() {return intHash;}

    public void setIntHash(String intHash) {this.intHash = intHash;}

    public String getIntAppId() {return intAppId;}

    public void setIntAppId(String intAppId) {this.intAppId = intAppId;}


    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getToken() {
        return token;
    }

    public void setToken(String token) {
        this.token = token;
    }

    public String getRefreshToken() {
        return refreshToken;
    }

    public void setRefreshToken(String refreshToken) {
        this.refreshToken = refreshToken;
    }

    public String getUsername() {
        return username;
    }

    public void setUsername(String username) {
        this.username = username;
    }

    public String getUserid() {
        return userid;
    }

    public void setUserid(String userid) {
        this.userid = userid;
    }

    public long getTokenExpire() {
        return tokenExpire;
    }

    public void setTokenExpire(long tokenExpire) {
        this.tokenExpire = tokenExpire;
    }


    public boolean isUserConnectServerDetails() {
        return UserConnectServerDetails;
    }

    public void setUserConnectServerDetails(boolean userConnectServerDetails) {
        UserConnectServerDetails = userConnectServerDetails;
    }
}
