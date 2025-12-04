package com.caspervpn.vpn.classes;

public class L2TP
{
    private String secret;

    public L2TP(String secret)
    {
        this.secret = secret;
    }

    public String getSecret() {return secret;}

    public void setSecret(String secret) {this.secret = secret;}
}