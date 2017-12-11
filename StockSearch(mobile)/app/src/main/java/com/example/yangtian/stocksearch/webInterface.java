package com.example.yangtian.stocksearch;

import android.content.Context;
import android.util.Log;
import android.view.View;
import android.webkit.JavascriptInterface;
import android.widget.ListView;
import android.widget.Toast;

import java.util.ArrayList;

/**
 * Created by yangtian on 11/18/17.
 */

public class webInterface {
    private Context context;
    private int position;
    private String symbol;
    private View rootView;



    @JavascriptInterface
    public String getSymbol() {
        return symbol;
    }

    public void setSymbol(String symbol) {
        this.symbol = symbol;
    }

    public webInterface(View rootView,Context context, int position, String symbol) {
        this.context = context;
        this.position = position;
        this.symbol = symbol;
        this.rootView=rootView;
    }

    public webInterface(Context context, int position, String symbol) {
        this.context = context;
        this.position = position;
        this.symbol = symbol;
    }

    //    public webInterface(Context context, int position) {
//        this.context = context;
//        this.position = position;
//    }

    @JavascriptInterface
    public int getPosition() {
        return position;
    }

    @JavascriptInterface
    public void error()
    {

//        Log.d("errorIndicator","there is wrong in indicator there is wrong in indicator there is wrong in indicator there is wrong in indicator");
//        ListView articles = (ListView) rootView.findViewById(R.id.error_historical);
//        ArrayList<String> fail = new ArrayList<>();
//        fail.add("Failed to load data");
//        failAdapter failAdapter = new failAdapter(context,R.layout.fail_news,fail);
//        articles.setAdapter(failAdapter);
//        articles.setDivider(null);
//        articles.setDividerHeight(0);
        Log.d("fail in news","fail in news");
    }

    @JavascriptInterface
    public void success()
    {
        Log.d("success","success success v success vsuccess success success");
    }
}
