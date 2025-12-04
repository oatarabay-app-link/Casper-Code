package com.caspervpn.vpn.helper;


import android.app.Activity;
import android.graphics.drawable.Drawable;
import android.os.Handler;
import android.view.MotionEvent;
import android.view.View;
import android.view.View.OnTouchListener;

import com.caspervpn.vpn.R;
import com.google.android.gms.maps.model.Marker;

public abstract class OnInfoWindowElemTouchListener implements OnTouchListener {
    private final MyButton view;
    private final Drawable bgDrawableNormal;
    private final Drawable bgDrawablePressed;
    private final int bgColorNormal;
    private final int bgColorPressed;
    private final Handler handler = new Handler();

    private Marker marker;
    private boolean pressed = false;

    public OnInfoWindowElemTouchListener(Activity MyActivity, MyButton btn)
    {
        this.view = btn;
        bgDrawableNormal = MyActivity.getResources().getDrawable(R.drawable.map_popup_btn_enabled);
        bgDrawablePressed = MyActivity.getResources().getDrawable(R.drawable.map_popup_btn_clicked);
        bgColorNormal = MyActivity.getResources().getColor(R.color.PopupColor);
        bgColorPressed = MyActivity.getResources().getColor(R.color.White);
    }

    public void setMarker(Marker marker) {
        this.marker = marker;
    }

    @Override
    public boolean onTouch(View vv, MotionEvent event) {
        if (0 <= event.getX() && event.getX() <= view.getWidth() && 0 <= event.getY() && event.getY() <= view.getHeight()) {
            switch (event.getActionMasked()) {
                case MotionEvent.ACTION_DOWN:
                    startPress();
                    break;

                // We need to delay releasing of the view a little so it shows the
                // pressed state on the screen
                case MotionEvent.ACTION_UP:
                    handler.postDelayed(confirmClickRunnable, 150);
                    break;

                case MotionEvent.ACTION_CANCEL:
                    endPress();
                    break;
                default:
                    break;
            }
        } else {
            // If the touch goes outside of the view's area
            // (like when moving finger out of the pressed button)
            // just release the press
            endPress();
        }
        return false;
    }

    private void startPress() {
        if (!pressed) {
            pressed = true;
            handler.removeCallbacks(confirmClickRunnable);
            view.setBackgroundDrawable(bgDrawablePressed);
            view.setTextColor(bgColorPressed);
            if (marker != null)
                marker.showInfoWindow();
        }
    }

    private boolean endPress() {
        if (pressed) {
            this.pressed = false;
            handler.removeCallbacks(confirmClickRunnable);
            view.setBackgroundDrawable(bgDrawableNormal);
            view.setTextColor(bgColorNormal);
            if (marker != null)
                marker.showInfoWindow();
            return true;
        } else
            return false;
    }

    private final Runnable confirmClickRunnable = new Runnable() {
        public void run() {
            if (endPress()) {
                onClickConfirmed(view, marker);
            }
        }
    };

    /**
     * This is called after a successful click
     */
    protected abstract void onClickConfirmed(View v, Marker marker);
}