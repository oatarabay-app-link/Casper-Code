package com.caspervpn.vpn.helper;

import android.app.AlertDialog;
import android.content.ActivityNotFoundException;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.net.Uri;
import android.preference.PreferenceManager;

import com.caspervpn.vpn.R;

import static com.caspervpn.vpn.common.Configuration.AlwaysShowRateUsDialog;
import static com.caspervpn.vpn.common.Configuration.DAYS_UNTIL_RATEUS_PROMPT;
import static com.caspervpn.vpn.common.Configuration.DAYS_UNTIL_RATEUS_REMINDER_PROMPT;
import static com.caspervpn.vpn.common.Configuration.LAUNCHES_UNTIL_RATEUS_PROMPT;

/**
 * Created by zaherZ on 4/20/2017.
 */

public class ApplicationRater
{
    public static void app_launched(Context mContext)
    {
        SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(mContext);
        SharedPreferences.Editor editor = prefs.edit();

        if (AlwaysShowRateUsDialog)
        {
            showRateDialog(mContext, editor);
            return;
        }

        // Never Show Again
        if (prefs.getBoolean("dontshowagain", false)) { return ; }

        // Increment launch counter
        long launch_count = prefs.getLong("launch_count", 0) + 1;
        editor.putLong("launch_count", launch_count);

        // Get date of first launch
        Long date_firstLaunch = prefs.getLong("date_firstlaunch", 0);
        if (date_firstLaunch == 0)
        {
            date_firstLaunch = System.currentTimeMillis();
            editor.putLong("date_firstlaunch", date_firstLaunch);
        }

        // Wait at least n days before opening
        if (launch_count >= LAUNCHES_UNTIL_RATEUS_PROMPT)
        {
            if (System.currentTimeMillis() >= date_firstLaunch + (DAYS_UNTIL_RATEUS_PROMPT * 24 * 60 * 60 * 1000))
            {
                showRateDialog(mContext, editor);
            }
        }

        editor.commit();
    }

    public static void showRateDialog(final Context MyContext, final SharedPreferences.Editor editor)
    {
        AlertDialog.Builder SubscriptionDialogBuilder = new AlertDialog.Builder(MyContext);
        SubscriptionDialogBuilder.setIcon(R.mipmap.rating);
        SubscriptionDialogBuilder.setTitle(MyContext.getResources().getString(R.string.RateUs));
        SubscriptionDialogBuilder.setPositiveButton((MyContext.getResources().getString(R.string.RateNow)), new DialogInterface.OnClickListener()
        {
            public void onClick(DialogInterface dialog, int whichButton)
            {
                Uri uri = Uri.parse("market://details?id=" + MyContext.getPackageName());
                Intent goToMarket = new Intent(Intent.ACTION_VIEW, uri);
                goToMarket.addFlags(Intent.FLAG_ACTIVITY_NO_HISTORY | Intent.FLAG_ACTIVITY_NEW_DOCUMENT | Intent.FLAG_ACTIVITY_MULTIPLE_TASK);
                try
                {
                    MyContext.startActivity(goToMarket);
                }
                catch (ActivityNotFoundException e)
                {
                    MyContext.startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("http://play.google.com/store/apps/details?id=" + MyContext.getPackageName())));
                }
                editor.putBoolean("dontshowagain", true);
                editor.commit();
            }
        });
        SubscriptionDialogBuilder.setNegativeButton((MyContext.getResources().getString(R.string.Later)), new DialogInterface.OnClickListener()
        {
            public void onClick(DialogInterface dialog, int whichButton)
            {
                editor.putLong("date_firstlaunch", System.currentTimeMillis() - ((DAYS_UNTIL_RATEUS_PROMPT - DAYS_UNTIL_RATEUS_REMINDER_PROMPT) * 24 * 60 * 60 * 1000));
                editor.commit();
            }
        });
        SubscriptionDialogBuilder.setNeutralButton((MyContext.getResources().getString(R.string.NoThanks)), new DialogInterface.OnClickListener()
        {
            public void onClick(DialogInterface dialog, int whichButton)
            {
                editor.putBoolean("dontshowagain", true);
                editor.commit();
            }
        });
        SubscriptionDialogBuilder.setMessage(MyContext.getResources().getString(R.string.RateUsText));
        AlertDialog SubscriptionDialog = SubscriptionDialogBuilder.create();
        SubscriptionDialog.show();
    }
}