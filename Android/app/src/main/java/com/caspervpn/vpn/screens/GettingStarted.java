package com.caspervpn.vpn.screens;

import android.os.Bundle;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentActivity;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentStatePagerAdapter;
import androidx.viewpager.widget.ViewPager;
import android.view.LayoutInflater;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.common.Commun;
import com.caspervpn.vpn.common.Configuration;
import com.caspervpn.vpn.helper.MyApplication;
import com.caspervpn.vpn.helper.MyTextView;
import com.caspervpn.vpn.helper.MyViewPager;

/**
 * Created by zaherZ on 3/11/2017.
 */

public class GettingStarted extends FragmentActivity
{
    public static MyViewPager OuterPager;
    public static Commun commun ;

    //region Outer Pager
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.getting_started);
        commun = new Commun(this);

        OuterPager = (MyViewPager) findViewById(R.id.OuterPager);
        OuterPager.setOffscreenPageLimit(2);
        OuterPager.setPagingEnabled(false);
        OuterPager.setAdapter(new OuterPagerAdapter(getSupportFragmentManager()));

        if (commun.isRTL())
        {
            OuterPager.setCurrentItem(1);
        }

        //Toufic 3/1/2018 -- google analytics --
        MyApplication.getInstance().trackScreenView(Configuration.GettingStartedScreenName);
        //Toufic 3/1/2018
    }


    public class OuterPagerAdapter extends FragmentStatePagerAdapter
    {
        public OuterPagerAdapter(FragmentManager fm)
        {
            super(fm);
        }

        @Override
        public Fragment getItem(int position)
        {
            if (commun.isRTL())
            {
                if (position == 0)
                    return new LandingPageFragment();
                else
                    return new GettingStartedFragment();
            }
            else
            {
                if (position == 1)
                    return new LandingPageFragment();
                else
                    return new GettingStartedFragment();
            }
        }

        @Override
        public int getCount()
        {
            return 2;
        }
    }


    public static class LandingPageFragment extends Fragment
    {
        @Override
        public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState)
        {
            View rootView = inflater.inflate(R.layout.getting_started_5, container, false);

            MyTextView ApplicationVersion = (MyTextView) rootView.findViewById(R.id.ApplicationVersion);
            try {ApplicationVersion.setText("v. " + getActivity().getPackageManager().getPackageInfo(getActivity().getPackageName(), 0).versionName);}
            catch (Exception e) {}

            return rootView;
        }

        @Override
        public void setMenuVisibility(boolean visible)
        {
            super.setMenuVisibility(visible);
            if (visible)
                getActivity().finish();
        }
    }

    public static class GettingStartedFragment extends Fragment implements View.OnTouchListener
    {
        public static GettingStartedFragment control;

        private float x1,x2;
        static final int MIN_DISTANCE = 150;
        public Button GettingStarted1, GettingStarted2, GettingStarted3, GettingStarted4;
        public MyTextView GettingStartedTitle;
        ViewPager InnerViewPager;

        @Override
        public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState)
        {
            super.onCreate(savedInstanceState);
            View rootView = inflater.inflate(R.layout.getting_started_pager, container, false);
            control = this;

            InnerViewPager = (ViewPager) rootView.findViewById(R.id.InnerPager);
            InnerViewPager.setOffscreenPageLimit(5);
            InnerViewPager.setAdapter(new InnerPagerAdapter(getFragmentManager()));
            InnerViewPager.setEnabled(true);

            if (commun.isRTL())
            {
                InnerViewPager.setCurrentItem(4);
            }

            GettingStarted1 = (Button) rootView.findViewById(R.id.GettingStarted1);
            GettingStarted2 = (Button) rootView.findViewById(R.id.GettingStarted2);
            GettingStarted3 = (Button) rootView.findViewById(R.id.GettingStarted3);
            GettingStarted4 = (Button) rootView.findViewById(R.id.GettingStarted4);
            GettingStartedTitle = (MyTextView) rootView.findViewById(R.id.GettingStartedTitle);

            //swipe outside the pageviewer
            rootView.setOnTouchListener(this);

            return rootView;
        }

        @Override
        public boolean onTouch(View view, MotionEvent event)
        {
            switch (event.getAction())
            {
                case MotionEvent.ACTION_DOWN:
                    x1 = event.getX();
                    break;
                case MotionEvent.ACTION_UP:
                    x2 = event.getX();
                    float deltaX = x2 - x1;

                    if (Math.abs(deltaX) > MIN_DISTANCE)
                    {
                        // Left to Right swipe action
                        if (x2 > x1)
                        {
                            int position = InnerViewPager.getCurrentItem();
                            if (position > 0) InnerViewPager.setCurrentItem(position - 1);
                        }

                        // Right to left swipe action
                        else
                        {
                            int position = InnerViewPager.getCurrentItem();
                            if (position < 3) InnerViewPager.setCurrentItem(position + 1);
                        }
                    }
                    break;
            }
            return true;

        }
    }
    //endregion

    //region Inner Pager
    public static class InnerPagerAdapter extends FragmentStatePagerAdapter
    {
        public InnerPagerAdapter(FragmentManager fm)
        {
            super(fm);
        }

        @Override
        public Fragment getItem(int position)
        {
            if (commun.isRTL())
            {
                if (position < 4)
                {
                    Fragment fragment = new GettingStartedPageFragment();
                    Bundle args = new Bundle();
                    args.putInt("PagerID", 4 - position);
                    fragment.setArguments(args);
                    return fragment;
                }
            }
            else
            {
                if (position < 4)
                {
                    Fragment fragment = new GettingStartedPageFragment();
                    Bundle args = new Bundle();
                    args.putInt("PagerID", position + 1);
                    fragment.setArguments(args);
                    return fragment;
                }
            }
            return null;
        }

        @Override
        public int getCount()
        {
            return 4;
        }
    }

    public static class GettingStartedPageFragment extends Fragment
    {
        int PagerID;

        @Override
        public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState)
        {
            View rootView = null;

            Bundle args = getArguments();
            PagerID = args.getInt("PagerID");
            if (PagerID == 1) rootView = inflater.inflate(R.layout.getting_started_1, container, false);
            else if (PagerID == 2) rootView = inflater.inflate(R.layout.getting_started_2, container, false);
            else if (PagerID == 3) rootView = inflater.inflate(R.layout.getting_started_3, container, false);
            else if (PagerID == 4) rootView = inflater.inflate(R.layout.getting_started_4, container, false);

            MyTextView ApplicationVersion = (MyTextView) rootView.findViewById(R.id.ApplicationVersion);
            try {ApplicationVersion.setText("v. " + getActivity().getPackageManager().getPackageInfo(getActivity().getPackageName(), 0).versionName);}
            catch (Exception e) {}

            return rootView;
        }

        @Override
        public void setMenuVisibility(boolean visible)
        {
            super.setMenuVisibility(visible);
            if (visible)
            {
                GettingStartedFragment coltrol = GettingStartedFragment.control;

                if (PagerID == 1)
                {
                    coltrol.GettingStartedTitle.setText(getString(R.string.GettingStarted1));
                    coltrol.GettingStarted1.setEnabled(true);
                    coltrol.GettingStarted2.setEnabled(false);
                    coltrol.GettingStarted3.setEnabled(false);
                    coltrol.GettingStarted4.setEnabled(false);
                    OuterPager.setPagingEnabled(false);
                }
                else if (PagerID == 2)
                {
                    coltrol.GettingStartedTitle.setText(getString(R.string.GettingStarted2));
                    coltrol.GettingStarted1.setEnabled(false);
                    coltrol.GettingStarted2.setEnabled(true);
                    coltrol.GettingStarted3.setEnabled(false);
                    coltrol.GettingStarted4.setEnabled(false);
                    OuterPager.setPagingEnabled(false);
                }
                else if (PagerID == 3)
                {
                    coltrol.GettingStartedTitle.setText(getString(R.string.GettingStarted3));
                    coltrol.GettingStarted1.setEnabled(false);
                    coltrol.GettingStarted2.setEnabled(false);
                    coltrol.GettingStarted3.setEnabled(true);
                    coltrol.GettingStarted4.setEnabled(false);
                    OuterPager.setPagingEnabled(false);
                }
                else if (PagerID == 4)
                {
                    coltrol.GettingStartedTitle.setText(getString(R.string.GettingStarted4));
                    coltrol.GettingStarted1.setEnabled(false);
                    coltrol.GettingStarted2.setEnabled(false);
                    coltrol.GettingStarted3.setEnabled(false);
                    coltrol.GettingStarted4.setEnabled(true);
                    OuterPager.setPagingEnabled(true);
                }
            }
        }
    }
    //endregion
}