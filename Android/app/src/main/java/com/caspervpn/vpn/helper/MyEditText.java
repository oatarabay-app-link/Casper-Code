package com.caspervpn.vpn.helper;
/**
 * Created by Eng. Zaher Zohbi on 12/26/2016.
 */

import android.content.Context;
import android.graphics.Typeface;
import android.graphics.drawable.Drawable;
import android.util.AttributeSet;
import android.view.KeyEvent;
import android.view.MotionEvent;
import android.view.View;
import android.view.inputmethod.EditorInfo;
import android.view.inputmethod.InputMethodManager;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.caspervpn.vpn.R;

import static org.apache.commons.lang3.StringUtils.isNotEmpty;

/**
 * TextView subclass which allows the user to define a truetype font file to use as the view's typeface.
 */
public class MyEditText extends EditText implements View.OnTouchListener, View.OnFocusChangeListener, TextWatcherAdapter.TextWatcherListener
{
    public Typeface tf;
    Context Mycontext;
    static boolean isRTL = false;

    public MyEditText(Context context)
    {
        this(context, null);
    }

    public MyEditText(Context context, AttributeSet attrs)
    {
        this(context, attrs, 0);
    }

    public MyEditText(Context context, AttributeSet attrs, int defStyle)
    {
        super(context, attrs, android.R.attr.editTextStyle);
        this.Mycontext = context;

        android.content.res.Configuration configuration = this.Mycontext.getResources().getConfiguration();
        int directionality = configuration.getLayoutDirection();//this.getLayoutDirection();//
        isRTL = directionality == Character.DIRECTIONALITY_RIGHT_TO_LEFT ||
                directionality == Character.DIRECTIONALITY_RIGHT_TO_LEFT_ARABIC;

        loc = isRTL ? Location.LEFT : Location.RIGHT;

        if (this.getTag() != null)
        {
            if (tf == null)
                tf = Typeface.createFromAsset(context.getAssets(), this.getTag().toString());
            setTypeface(tf, getTypeface().getStyle());
        }

        this.setOnEditorActionListener(new EditorActionListener());

        if (this.isEnabled()) init();
    }


    //region Clear
    public static enum Location
    {
        LEFT(0), RIGHT(2);

        final int idx;

        private Location(int idx)
        {
            this.idx = idx;
        }
    }

    public interface Listener {void didClearText();}

    @Override
    public void setOnTouchListener(OnTouchListener l) {this.l = l;}

    @Override
    public void setOnFocusChangeListener(OnFocusChangeListener f) {this.f = f;}

    private Location loc = isRTL ? Location.LEFT : Location.RIGHT;

    private Drawable xD;
    private Listener listener;

    private OnTouchListener l;
    private OnFocusChangeListener f;

    @Override
    public boolean onTouch(View v, MotionEvent event)
    {
        if (getDisplayedDrawable() != null)
        {
            int x = (int) event.getX();
            int y = (int) event.getY();
            int left = (loc == Location.LEFT) ? 0 : getWidth() - getPaddingRight() - xD.getIntrinsicWidth();
            int right = (loc == Location.LEFT) ? getPaddingLeft() + xD.getIntrinsicWidth() : getWidth();
            boolean tappedX = x >= left && x <= right && y >= 0 && y <= (getBottom() - getTop());
            if (tappedX)
            {
                if (event.getAction() == MotionEvent.ACTION_UP)
                {
                    setText("");
                    if (listener != null) listener.didClearText();
                }
                return true;
            }
        }
        if (l != null) return l.onTouch(v, event);
        return false;
    }

    @Override
    public void onFocusChange(View v, boolean hasFocus)
    {
        if (hasFocus)
            setClearIconVisible(isNotEmpty(getText()));
        else
            setClearIconVisible(false);

        if (f != null)
            f.onFocusChange(v, hasFocus);
    }

    @Override
    public void onTextChanged(EditText view, String text) {if (isFocused()) {setClearIconVisible(isNotEmpty(text));}}

    @Override
    public void setCompoundDrawables(Drawable left, Drawable top, Drawable right, Drawable bottom) {super.setCompoundDrawables(left, top, right, bottom);initIcon();}

    private void init()
    {
        super.setOnTouchListener(this);
        super.setOnFocusChangeListener(this);
        addTextChangedListener(new TextWatcherAdapter(this, this));
        initIcon();
        setClearIconVisible(false);
    }

    private void initIcon()
    {
        xD = null;
        if (loc != null)
        {
            xD = getCompoundDrawables()[loc.idx];
        }
        if (xD == null)
        {
            xD = getResources().getDrawable(R.mipmap.clear);
        }
        xD.setBounds(0, 0, xD.getIntrinsicWidth(), xD.getIntrinsicHeight());
        int min = getPaddingTop() + xD.getIntrinsicHeight() + getPaddingBottom();
        if (getSuggestedMinimumHeight() < min)
        {
            setMinimumHeight(min);
        }
    }

    private Drawable getDisplayedDrawable() {return (loc != null) ? getCompoundDrawables()[loc.idx] : null;}

    protected void setClearIconVisible(boolean visible)
    {
        Drawable[] cd = getCompoundDrawables();
        Drawable displayed = getDisplayedDrawable();
        boolean wasVisible = (displayed != null);
        if (visible != wasVisible)
        {
            Drawable x = visible ? xD : null;
            super.setCompoundDrawables((loc == Location.LEFT) ? x : cd[0], cd[1], (loc == Location.RIGHT) ? x : cd[2], cd[3]);
        }
    }

    //endregion

    //region Editor Action
    private class EditorActionListener implements OnEditorActionListener
    {
        @Override
        public boolean onEditorAction(TextView v, int actionId, KeyEvent event)
        {
            if(actionId== EditorInfo.IME_ACTION_DONE)
            {
                v.clearFocus();
                InputMethodManager imm = (InputMethodManager) Mycontext.getSystemService(Context.INPUT_METHOD_SERVICE);
                imm.hideSoftInputFromWindow(v.getWindowToken(), 0);
                View helper = ((LinearLayout)v.getParent()).findViewById(R.id.FocusHelper);
                if (helper != null) helper.requestFocus();
            }
            return false;
        }
    }
    //endregion
}
