package com.caspervpn.vpn.receivers;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.preference.PreferenceManager;

import com.caspervpn.vpn.common.Commun;

import de.blinkt.openvpn.activities.LaunchVPN;
import de.blinkt.openvpn.activities.VpnProfile;
import de.blinkt.openvpn.core.ProfileManager;
import de.blinkt.openvpn.core.VpnStatus;

import static com.caspervpn.vpn.common.Configuration.userprofile;


public class OnBootReceiver extends BroadcastReceiver
{
	// Debug: am broadcast -a android.intent.action.BOOT_COMPLETED
	@Override
	public void onReceive(Context context, Intent intent)
    {
		final String action = intent.getAction();
		SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(context);

		Commun commun = new Commun(context);

		boolean useStartOnBoot = prefs.getBoolean("AutoConnect", false);
		if (!useStartOnBoot)
			return;

		if(Intent.ACTION_BOOT_COMPLETED.equals(action))
		{
			commun.Log("CasperVPN - On Boot Receiver Completed!");

			boolean Islogin = prefs.getBoolean("Islogin", false);

			if (userprofile == null) commun.Load_Data();

			if(ProfileManager.getInstance(context).getProfiles().iterator().hasNext() && !VpnStatus.isVPNActive() == true && Islogin == true  && commun.IsActive() == true )
			{
				commun.Log("CasperVPN - On Boot Receiver!");

				VpnProfile bootProfile = ProfileManager.getInstance(context).getProfiles().iterator().next();
				launchVPN(bootProfile, context);
			}		
		}
	}

	void launchVPN(VpnProfile profile, Context context)
	{
		Intent startVpnIntent = new Intent(Intent.ACTION_MAIN);
		startVpnIntent.setClass(context, LaunchVPN.class);
		startVpnIntent.putExtra(LaunchVPN.EXTRA_KEY, profile.getUUIDString());
		startVpnIntent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
		startVpnIntent.putExtra(LaunchVPN.EXTRA_HIDELOG, true);

		context.startActivity(startVpnIntent);
	}
}