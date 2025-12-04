package com.caspervpn.vpn.classes;

public class ServerInfo
{
    private String serverId, Uptime;
    private double HelathPercent;
    private double CPU_Load, RAM_ALL, RAM_USED, HDD_All, HDD_Used, Net_All, Net_Used;

    public ServerInfo(String serverId, String uptime, double helathPercent, double CPU_Load, double RAM_ALL, double RAM_USED, double HDD_All, double HDD_Used, double net_All, double net_Used)
    {
        this.serverId = serverId;
        this.Uptime = uptime;
        this.HelathPercent = helathPercent;
        this.CPU_Load = CPU_Load;
        this.RAM_ALL = RAM_ALL;
        this.RAM_USED = RAM_USED;
        this.HDD_All = HDD_All;
        this.HDD_Used = HDD_Used;
        this.Net_All = net_All;
        this.Net_Used = net_Used;
    }

    public String getServerId() {return serverId;}

    public void setServerId(String serverId) {this.serverId = serverId;}

    public String getUptime() {return Uptime;}

    public void setUptime(String uptime) {Uptime = uptime;}

    public double getHelathPercent() {return HelathPercent;}

    public void setHelathPercent(double helathPercent) {HelathPercent = helathPercent;}

    public double getCPU_Load() {return CPU_Load;}

    public void setCPU_Load(double CPU_Load) {this.CPU_Load = CPU_Load;}

    public double getRAM_ALL() {return RAM_ALL;}

    public void setRAM_ALL(double RAM_ALL) {this.RAM_ALL = RAM_ALL;}

    public double getRAM_USED() {return RAM_USED;}

    public void setRAM_USED(double RAM_USED) {this.RAM_USED = RAM_USED;}

    public double getHDD_All() {return HDD_All;}

    public void setHDD_All(double HDD_All) {this.HDD_All = HDD_All;}

    public double getHDD_Used() {return HDD_Used;}

    public void setHDD_Used(double HDD_Used) {this.HDD_Used = HDD_Used;}

    public double getNet_All() {return Net_All;}

    public void setNet_Al(double net_All) {Net_All = net_All;}

    public double getNet_Used() {return Net_Used;}

    public void setNet_Used(double net_Used) {Net_Used = net_Used;}

}
