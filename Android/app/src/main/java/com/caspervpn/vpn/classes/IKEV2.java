package com.caspervpn.vpn.classes;

public class IKEV2
{
    private String remoteId;//secret,

    public IKEV2(String remoteId)//String secret,
    {
        //this.secret = secret;
        this.remoteId = remoteId;
    }
//
//    public String getSecret() {return secret;}
//
//    public void setSecret(String secret) {this.secret = secret;}

    public String getremoteId() {return remoteId;}

    public void setremoteId(String remoteId) {this.remoteId = remoteId;}
}