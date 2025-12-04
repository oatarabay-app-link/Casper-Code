package com.caspervpn.vpn.classes;

public class ServerParameter
{
    private IKEV2 IKEV2;
    private L2TP L2TP;
    private OPEN_VPN OPEN_VPN;
    private SSTP SSTP;

    public ServerParameter(IKEV2 IKEV2, L2TP l2TP, OPEN_VPN OPEN_VPN, SSTP SSTP)
    {
        this.IKEV2 = IKEV2;
        this.L2TP = l2TP;
        this.OPEN_VPN = OPEN_VPN;
        this.SSTP = SSTP;
    }

    public IKEV2 getIKEV2() {return IKEV2;}

    public void setIKEV2(IKEV2 IKEV2) {this.IKEV2 = IKEV2;}

    public L2TP getL2TP() {return L2TP;}

    public void setL2TP(L2TP l2TP) {L2TP = l2TP;}

    public OPEN_VPN getOPEN_VPN() {return OPEN_VPN;}

    public void setOPEN_VPN(OPEN_VPN OPEN_VPN) {this.OPEN_VPN = OPEN_VPN;}

    public SSTP getSSTP() {return SSTP;}

    public void setSSTP(SSTP SSTP) {this.SSTP = SSTP;}
}