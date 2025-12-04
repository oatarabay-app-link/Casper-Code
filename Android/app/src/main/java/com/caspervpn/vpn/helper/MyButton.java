package com.caspervpn.vpn.helper;
/**
 * Created by Eng. Zaher Zohbi on 12/26/2016.
 */

import android.content.Context;
import android.graphics.Typeface;
import android.util.AttributeSet;
import android.widget.Button;

/**
 * TextView subclass which allows the user to define a truetype font file to use as the view's typeface.
 */
public class MyButton extends Button
{
    public Typeface tf;

    public MyButton(Context context) {
        this(context, null);
    }

    public MyButton(Context context, AttributeSet attrs) {
        this(context, attrs, 0);
    }

    public MyButton(Context context, AttributeSet attrs, int defStyle)
    {
        super(context, attrs, defStyle);

        if (this.getTag() != null)
        {
            if (tf == null)
                tf = Typeface.createFromAsset(context.getAssets(), this.getTag().toString());
            setTypeface(tf, getTypeface().getStyle());
        }
    }
}
