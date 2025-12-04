package com.caspervpn.vpn.helper;

/**
 * Created by zaherZ on 3/28/2017.
 */

import android.content.Context;
import android.graphics.Typeface;
import android.util.AttributeSet;
import android.widget.TextView;

public class MyJustifiedTextView extends TextView //TODO slow performance JustifiedTextView //Display Bug in TextViewEx
{
    Context Mycontext;

    public Typeface tf;

    public MyJustifiedTextView(Context context)
    {
        this(context, null);
    }

    public MyJustifiedTextView(Context context, AttributeSet attrs) {
        this(context, attrs, 0);
    }

    public MyJustifiedTextView(Context context, AttributeSet attrs, int defStyle)
    {
        super(context, attrs, defStyle);
        this.Mycontext = context;

        //Commun commun = new Commun(context);
        //super.setText(this.getText().toString());
        //super.setTextAlign(commun.isRTL() ? Paint.Align.RIGHT : Paint.Align.LEFT);


        if (this.getTag() != null)
        {
            if (tf == null)
                tf = Typeface.createFromAsset(context.getAssets(), this.getTag().toString());
            setTypeface(tf, getTypeface().getStyle());
        }
        this.setDrawingCacheEnabled(false);
    }
}