package com.caspervpn.vpn.classes;

public class SSTP
{
    private String cert;

    public SSTP(String cert)
    {
        this.cert = cert;
    }

    public String getCert() {return cert;}

    public void setCert(String cert) {this.cert = cert;}
}