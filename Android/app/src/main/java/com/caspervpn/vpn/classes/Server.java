package com.caspervpn.vpn.classes;

import java.util.ArrayList;


public class Server
{
    private String serverId;
    private String serverName;
    private String serverIp;
    private double serverLongitude, serverLatitude;
    private ArrayList<String> protocolTypes = new ArrayList<String>();
    private ServerParameter parameters;
    private boolean disabled;
    private long createDate;
    private ServerInfo systemInfo;
    private String connectionData;
    private String Country;

    public Server(String serverName, String serverIp, String serverId, String country, ServerParameter parameters, ServerInfo systemInfo, String connectionData, double serverLongitude, double serverLatitude, boolean disabled, long createDate, ArrayList<String> protocolTypes) {
        this.parameters = parameters;
        this.serverName = serverName;
        this.serverIp = serverIp;
        this.Country = country;
        this.systemInfo = systemInfo;
        this.connectionData = connectionData;
        this.serverLongitude = serverLongitude;
        this.serverLatitude = serverLatitude;
        this.disabled = disabled;
        this.createDate = createDate;
        this.protocolTypes = protocolTypes;
        this.serverId = serverId;
    }

    public ServerParameter getParameters() {
        return parameters;
    }

    public void setParameters(ServerParameter parameters) {
        this.parameters = parameters;
    }

    public ArrayList<String> getProtocolTypes() {
        return protocolTypes;
    }

    public void setProtocolTypes(ArrayList<String> protocolTypes) {
        this.protocolTypes = protocolTypes;
    }

    public long getCreateDate() {
        return createDate;
    }

    public void setCreateDate(long createDate) {
        this.createDate = createDate;
    }

    public String getConnectionData() {
        return connectionData;
    }

    public void setConnectionData(String connectionData) {
        this.connectionData = connectionData;
    }

    public ServerInfo getSystemInfo() {
        return systemInfo;
    }

    public void setSystemInfo(ServerInfo systemInfo) {
        this.systemInfo = systemInfo;
    }

    public String getCountry() {
        return Country;
    }

    public void setCountry(String country) {
        Country = country;
    }

    public String getServerId() {
        return serverId;
    }

    public void setServerId(String serverId) {
        this.serverId = serverId;
    }

    public String getServerName() {
        return serverName;
    }

    public void setServerName(String serverName) {
        this.serverName = serverName;
    }

    public String getServerIp() {
        return serverIp;
    }

    public void setServerIp(String serverIp) {
        this.serverIp = serverIp;
    }

    public double getServerLongitude() {
        return serverLongitude;
    }

    public void setServerLongitude(double serverLongitude) {
        this.serverLongitude = serverLongitude;
    }

    public double getServerLatitude() {
        return serverLatitude;
    }

    public void setServerLatitude(double serverLatitude) {
        this.serverLatitude = serverLatitude;
    }

    public boolean isDisabled() {
        return disabled;
    }

    public void setDisabled(boolean disabled) {
        this.disabled = disabled;
    }
}
