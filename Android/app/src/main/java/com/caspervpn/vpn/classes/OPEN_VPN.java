package com.caspervpn.vpn.classes;

public class OPEN_VPN
{
    private String conf;

    public OPEN_VPN(String conf, String cert)
    {
        this.conf = conf;
        this.cert = cert;
    }

    private String cert;

    public String getConf() {return conf;}

    public void setConf(String conf) {this.conf = conf;}

    public String getCert() {return cert;}

    public void setCert(String cert) {this.cert = cert;}
}