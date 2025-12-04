package com.caspervpn.vpn.receivers;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.net.NetworkInfo;
import android.net.wifi.WifiManager;
import android.preference.PreferenceManager;

import com.caspervpn.vpn.common.Commun;

import de.blinkt.openvpn.activities.LaunchVPN;
import de.blinkt.openvpn.activities.VpnProfile;
import de.blinkt.openvpn.core.ProfileManager;
import de.blinkt.openvpn.core.VpnStatus;

import static com.caspervpn.vpn.common.Configuration.userprofile;

public class WifiReceiver extends BroadcastReceiver
{
	@Override
	public void onReceive(Context context, Intent intent)
    {
		final String action = intent.getAction();

		Commun commun = new Commun(context);
		SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(context);

		boolean useConnectOnWifi = prefs.getBoolean("ConnectOnWifi", false);
		if (!useConnectOnWifi) return;

		NetworkInfo info = intent.getParcelableExtra(WifiManager.EXTRA_NETWORK_INFO);
		if(info != null && info.isConnected())
		{
			boolean Islogin = prefs.getBoolean("Islogin", false);


			if (userprofile == null) commun.Load_Data();

			if(ProfileManager.getInstance(context).getProfiles().iterator().hasNext() &&  !VpnStatus.isVPNActive() && Islogin && commun.IsActive())
			{
				commun.Log("CasperVPN - Connect On Wifi!");

				VpnProfile bootProfile = ProfileManager.getInstance(context).getProfiles().iterator().next();
				launchVPN(bootProfile, context);
			}
		}
	}

	void launchVPN(VpnProfile profile, Context context) {
		Intent intent = new Intent(context, LaunchVPN.class);
		intent.putExtra(LaunchVPN.EXTRA_KEY, profile.getUUID().toString());
		intent.setAction(Intent.ACTION_MAIN);
		intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
		context.startActivity(intent);
	}
}