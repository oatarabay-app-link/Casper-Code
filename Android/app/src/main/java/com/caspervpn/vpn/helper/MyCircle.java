package com.caspervpn.vpn.helper;

import android.content.Context;
import android.graphics.Canvas;
import android.graphics.Paint;
import android.graphics.RectF;
import androidx.core.content.ContextCompat;
import android.util.AttributeSet;
import android.util.DisplayMetrics;
import android.util.Property;
import android.view.View;

import com.caspervpn.vpn.R;


public class MyCircle extends View {

    final protected Paint bgPaint = new Paint(Paint.ANTI_ALIAS_FLAG);
    final protected Paint fgPaint = new Paint(Paint.ANTI_ALIAS_FLAG);
    final protected Paint textPaint = new Paint(Paint.ANTI_ALIAS_FLAG);
    private RectF mRect = new RectF();
    private float sweepAngle;
    private float radiusInDPI = 100;
    private float radiusInPixels;
    private float strokeWidthInDPI = 4;
    private float stokeWidthInPixels;
    private float dpi;
    private int heightByTwo;
    private int widthByTwo;

    public MyCircle(Context context) {
        super(context);
        init();
    }

    public MyCircle(Context context, AttributeSet attrs) {
        super(context, attrs);
        init();
    }

    public MyCircle(Context context, AttributeSet attrs, int defStyleAttr) {
        super(context, attrs, defStyleAttr);
        init();
    }

    @Override
    public void onSizeChanged(int w, int h, int oldw, int oldh) {
        super.onSizeChanged(w, h, oldw, oldh);
        heightByTwo = h / 2;
        widthByTwo = w / 2;
        mRect = new RectF(w / 2 - radiusInPixels, h / 2 - radiusInPixels, w / 2 + radiusInPixels, h / 2 + radiusInPixels);
    }

    private void init() {
        DisplayMetrics metrics = getResources().getDisplayMetrics();
        dpi = metrics.density;
        radiusInPixels = dpi * radiusInDPI;
        stokeWidthInPixels = dpi * strokeWidthInDPI;
        bgPaint.setStrokeWidth(stokeWidthInPixels);
        bgPaint.setStyle(Paint.Style.STROKE);
        bgPaint.setColor(ContextCompat.getColor(getContext(), R.color.HalfTransparent));

        fgPaint.setStrokeWidth(stokeWidthInPixels);
        fgPaint.setStyle(Paint.Style.STROKE);
        fgPaint.setColor(ContextCompat.getColor(getContext(), R.color.White));

        textPaint.setTextSize(24 * 3);
        textPaint.setStyle(Paint.Style.STROKE);
        textPaint.setColor(ContextCompat.getColor(getContext(), R.color.HalfTransparent));


    }

    @Override
    protected void onDraw(Canvas canvas) {
        super.onDraw(canvas);
        // canvas.drawCircle(widthByTwo, heightByTwo, radiusInPixels, bgPaint);
        canvas.drawArc(mRect, 270, sweepAngle, false, fgPaint);
    }


    public static final Property<MyCircle, Float> SET_SWEEPWANGLE =
            new Property<MyCircle, Float>(Float.class, "outerCircleRadiusProgress") {
                @Override
                public Float get(MyCircle object) {
                    return object.getSweepAngle();
                }

                @Override
                public void set(MyCircle object, Float value) {
                    object.setSweepAngle(value);
                }
            };

    public float getSweepAngle() {
        return sweepAngle;
    }

    public void setSweepAngle(float sweepAngle)
    {
        this.sweepAngle = sweepAngle;
        postInvalidate();
    }
}