package com.example.yangtian.stocksearch;

import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.ConsoleMessage;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceError;
import android.webkit.WebResourceRequest;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.ListView;
import android.widget.ProgressBar;

import java.util.ArrayList;

/**
 * Created by yangtian on 11/16/17.
 */

public class Historical  extends Fragment {

    private WebView mview;
    private String input;
    private  View rootView;
    private ProgressBar historicalProgress;
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        rootView = inflater.inflate(R.layout.historical_fragment, container, false);
        input=(String) getActivity().getIntent().getSerializableExtra("symbol");
        historicalProgress=(ProgressBar) rootView.findViewById(R.id.historical_progress);
        //historical webview
        mview = (WebView) rootView.findViewById(R.id.historical);
        mview.setWebViewClient(new WebViewClient());

        WebSettings webSettings = mview.getSettings();
        webSettings.setJavaScriptEnabled(true);



        mview.addJavascriptInterface(new webInterface(rootView,getActivity(),7,input),"Android");
        String url="file:///android_asset/demo.html";
        mview.loadUrl(url);

//        mview.setWebViewClient(new WebViewClient(){
//            @Override
//            public void onReceivedError(WebView view, WebResourceRequest request, WebResourceError error) {
//                super.onReceivedError(view, request, error);
//                Log.d("HISTORICAL","I AM IN HISTORICAL I AM IN HISTORICAL I AM IN HISTORICAL I AM IN HISTORICAL I AM IN HISTORICAL I AM IN HISTORICAL");
//            }
//        });

        mview.setWebChromeClient(new WebChromeClient(){
            @Override
            public boolean onConsoleMessage(ConsoleMessage consoleMessage) {
                Log.d("errorIndicator",consoleMessage.message());
                if(consoleMessage.message().contains("success"))
                {
                    historicalProgress.setVisibility(View.GONE);
                    return true;
                }
                historicalProgress.setVisibility(View.GONE);
                ListView articles = (ListView) rootView.findViewById(R.id.error_historical);
                ArrayList<String> fail = new ArrayList<>();
                fail.add("Failed to load data");
                failAdapter failAdapter = new failAdapter(getActivity(),R.layout.fail_news,fail);
                articles.setAdapter(failAdapter);
                articles.setDivider(null);
                articles.setDividerHeight(0);
                return true;
            }
        });

        return rootView;
    }

//    public void onReceivedError(WebView view, int errorCode,
//                                String description, String failingUrl) {
//
//    }
}
