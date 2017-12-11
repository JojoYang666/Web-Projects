package com.example.yangtian.stocksearch;

import android.text.TextPaint;
import android.text.style.URLSpan;

/**
 * Created by yangtian on 11/19/17.
 */

public class urlNoUnderline extends URLSpan{

    public urlNoUnderline(String url) {
        super(url);
    }

    @Override
    public void updateDrawState(TextPaint ds) {
        super.updateDrawState(ds);
        ds.setUnderlineText(false);
    }
}
