package com.caspervpn.vpn.helper;

import android.content.Context;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.RectF;
import android.util.AttributeSet;
import android.view.View;

import com.caspervpn.vpn.R;
import com.caspervpn.vpn.common.Commun;


public class DemiCircle extends View {

    private static final int START_ANGLE_POINT = 45;

    private final Paint paint;
    //private final RectF rect;

    private float angle;
    private final float v;

    public DemiCircle(Context context, AttributeSet attrs) {

        super(context, attrs);

        float scaleRatio = getResources().getDisplayMetrics().density;
        float CircleRotateScaleDimen = getResources().getDimensionPixelSize(R.dimen.CircleRotateScaleValue) / scaleRatio;
        Commun c = new Commun(context);
        v = c.dipToPixels(CircleRotateScaleDimen);

        paint = new Paint();
        paint.setAntiAlias(true);
        paint.setStyle(Paint.Style.STROKE);
        paint.setStrokeWidth(c.dipToPixels(1));
        paint.setColor(Color.WHITE);

        //Initial Angle (optional, it can be zero)
        angle = 180;
    }

    @Override
    protected void onDraw(Canvas canvas)
    {
        super.onDraw(canvas);

        float x = canvas.getWidth();
        float y = canvas.getHeight() ;
        RectF rect = new RectF((x  - v) / 2,(y - v) / 2 , (x  - v) / 2 + v , (y - v) / 2 + v );

        canvas.drawArc(rect, START_ANGLE_POINT, angle, false, paint);
    }

    public float getAngle() {
        return angle;
    }

    public void setAngle(float angle) {
        this.angle = angle;
    }

    @Override
    public void clearAnimation()
    {
        super.clearAnimation();
        this.angle = 1;
        requestLayout();
    }
}