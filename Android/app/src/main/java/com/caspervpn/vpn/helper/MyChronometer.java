package com.caspervpn.vpn.helper;
/**
 * Created by Eng. Zaher Zohbi on 12/26/2016.
 */

import android.content.Context;
import android.graphics.Typeface;
import android.util.AttributeSet;
import android.view.KeyEvent;
import android.view.inputmethod.EditorInfo;
import android.view.inputmethod.InputMethodManager;
import android.widget.Chronometer;
import android.widget.TextView;

/**
 * TextView subclass which allows the user to define a truetype font file to use as the view's typeface.
 */
public class MyChronometer extends Chronometer
{
    Context Mycontext;

    public Typeface tf;

    public MyChronometer(Context context) {
        this(context, null);
    }

    public MyChronometer(Context context, AttributeSet attrs) {
        this(context, attrs, 0);
    }

    public MyChronometer(Context context, AttributeSet attrs, int defStyle)
    {
        super(context, attrs, defStyle);
        this.Mycontext = context;

        if (this.getTag() != null)
        {
            if (tf == null)
                tf = Typeface.createFromAsset(context.getAssets(), this.getTag().toString());
            setTypeface(tf, getTypeface().getStyle());
        }

        this.setOnEditorActionListener(new OnEditorActionListener()
        {
            @Override
            public boolean onEditorAction(TextView v, int actionId, KeyEvent event)
            {
                if(actionId== EditorInfo.IME_ACTION_DONE)
                {
                    v.clearFocus();
                    InputMethodManager imm = (InputMethodManager) Mycontext.getSystemService(Context.INPUT_METHOD_SERVICE);
                    imm.hideSoftInputFromWindow(v.getWindowToken(), 0);
                }
                return false;
            }
        });
    }
}
