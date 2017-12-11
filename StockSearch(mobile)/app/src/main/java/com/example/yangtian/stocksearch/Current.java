package com.example.yangtian.stocksearch;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.graphics.PorterDuff;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.text.Spannable;
import android.text.SpannableStringBuilder;
import android.text.style.ImageSpan;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.ConsoleMessage;
import android.webkit.WebChromeClient;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.ProgressBar;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

//import com.facebook.share.model.ShareLinkContent;

import com.facebook.CallbackManager;
import com.facebook.FacebookCallback;
import com.facebook.FacebookException;
import com.facebook.FacebookSdk;
import com.facebook.share.Sharer;
import com.facebook.share.model.ShareLinkContent;
import com.facebook.share.model.ShareOpenGraphAction;
import com.facebook.share.model.ShareOpenGraphContent;
import com.facebook.share.model.ShareOpenGraphObject;
import com.facebook.share.widget.ShareDialog;
import com.google.gson.Gson;
//import com.facebook.FacebookSdk;
import java.util.ArrayList;
import java.util.Comparator;

import static com.facebook.FacebookSdk.getApplicationContext;

/**
 * Created by yangtian on 11/16/17.
 */

public class Current extends Fragment {

    private WebView mview;
    private String input;
    private StockTable stockTableA;
    private Button love;
    private CallbackManager callbackManager;
    private ProgressBar webProgress;
    private TextView error;
    private String facebookUrl;

    @Override
    public View onCreateView(LayoutInflater inflater, final ViewGroup container,
                             Bundle savedInstanceState) {

        FacebookSdk.sdkInitialize(getApplicationContext());
//        facebookSDKInitialize();

        Log.d("Current","I am in current I am in current I am in current I am in current I am in current         Log.d(\"Current\",\"I am in current I am in current I am in current I am in current I am in current\");\n");
        final View rootView = inflater.inflate(R.layout.current_fragment, container, false);
//        facebookSDKInitialize();
        webProgress = (ProgressBar) rootView.findViewById(R.id.web_progress);
        webProgress.getIndeterminateDrawable().setColorFilter(Color.BLACK, PorterDuff.Mode.MULTIPLY);
        error=(TextView) rootView.findViewById(R.id.error_web);
        love = (Button) rootView.findViewById(R.id.love);
        stockTableA = new StockTable();
        stockTableA = (StockTable) getActivity().getIntent().getSerializableExtra("stockTable");
        input=(String) getActivity().getIntent().getSerializableExtra("symbol");

        //if belong to love
        SharedPreferences sharedPreferences = getActivity().getSharedPreferences(getString(R.string.preference_file_key),Context.MODE_PRIVATE);
        if(sharedPreferences.contains(input))
            love.setBackgroundResource(R.drawable.filled);


        //try web view
        mview = (WebView) rootView.findViewById(R.id.webview);
        mview.setWebViewClient(new WebViewClient());

        WebSettings webSettings = mview.getSettings();
        webSettings.setJavaScriptEnabled(true);
        mview.setWebChromeClient(new WebChromeClient());

        mview.addJavascriptInterface(new webInterface(getActivity(),10,input),"Android");
        String url="file:///android_asset/demo.html";
        mview.loadUrl(url);

        mview.setWebChromeClient(new WebChromeClient(){
            @Override
            public boolean onConsoleMessage(ConsoleMessage consoleMessage) {
                if(consoleMessage.message().equals("success"))
                {
                    webProgress.setVisibility(View.GONE);
                }
                else
                {
                    Log.d("indicatorerror",consoleMessage.message());
                    webProgress.setVisibility(View.GONE);
                    error.setVisibility(View.VISIBLE);

                }
                return true;
            }
        });


        //url for fb
        WebView dummy = (WebView) rootView.findViewById(R.id.dummy_content);
        dummy.setWebViewClient(new WebViewClient());

        WebSettings webSettingfb = dummy.getSettings();
        webSettingfb.setJavaScriptEnabled(true);
        dummy.setWebChromeClient(new WebChromeClient());

        dummy.addJavascriptInterface(new webInterface(getActivity(),8,input),"Android");
        String urlfb="file:///android_asset/demo.html";
        dummy.loadUrl(urlfb);

        dummy.setWebChromeClient(new WebChromeClient(){
            @Override
            public boolean onConsoleMessage(ConsoleMessage consoleMessage) {
                Log.d("fbURL",consoleMessage.message());
                if(consoleMessage.message().length()>5&&consoleMessage.message().substring(0,7).equals("urlOfFb"))
                {

                    String temp=consoleMessage.message();
                    facebookUrl=temp.substring(7,temp.length());
                }
                else
                {
                    facebookUrl="http://export.highcharts.com/";

                }
                return true;
            }
        });

        //spinener dropdown
        final Spinner spinner=(Spinner) rootView.findViewById(R.id.spinner3);
        ArrayAdapter<CharSequence> adapter= ArrayAdapter.createFromResource(getActivity(),R.array.indicator_names,android.R.layout.simple_spinner_item);
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spinner.setAdapter(adapter);

        //set event for spinner
        final Button changeIn = (Button) rootView.findViewById(R.id.changeIn);
        spinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                changeIn.setEnabled(true);
            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });

        //set Event for changeButton
        changeIn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                error.setVisibility(View.GONE);
                int pos = spinner.getSelectedItemPosition();

                mview.addJavascriptInterface(new webInterface(getActivity(),pos,input),"Android");
                String url="file:///android_asset/demo.html";

                webProgress.setVisibility(View.VISIBLE);


                mview.loadUrl(url);
                changeIn.setEnabled(false);
            }
        });


        //set event for love button
        love = (Button) rootView.findViewById(R.id.love);
        love.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Context context = getActivity();
                SharedPreferences sharedPreferences = context.getSharedPreferences(getString(R.string.preference_file_key),Context.MODE_PRIVATE);
                SharedPreferences.Editor editor =sharedPreferences.edit();
                String symbol=(String)getActivity().getIntent().getSerializableExtra("symbol");
                if(sharedPreferences.contains(symbol))
                {
                    love.setBackgroundResource(R.drawable.empty);
                    editor.remove(symbol);
                    editor.apply();
                    return;
                }
                Gson gson = new Gson();
                String json = gson.toJson(stockTableA);
                editor.putString(stockTableA.getStockSymbol(),json);
                editor.commit();
                love.setBackgroundResource(R.drawable.filled);
            }
        });


        final ShareDialog shareDialog = new ShareDialog(this);
        callbackManager = CallbackManager.Factory.create();
        shareDialog.registerCallback(callbackManager, new FacebookCallback<Sharer.Result>() {
            @Override
            public void onSuccess(Sharer.Result result) {

//                Toast.makeText(getContext(),"Post Successfull",Toast.LENGTH_SHORT).show();
                Log.d("fbsuccess",result.toString());

            }

            @Override
            public void onCancel() {
//                Toast.makeText(getContext(),"Cancel Post",Toast.LENGTH_SHORT).show();
            }

            @Override
            public void onError(FacebookException error) {
//                Toast.makeText(getContext(),"Error happened, you can post again",Toast.LENGTH_SHORT).show();
            }
        });
        //set event for facebook button
        final Button facebook = (Button) rootView.findViewById(R.id.facebook);
        facebook.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                Log.d("faceBook","I am here in fafebook");
                if (ShareDialog.canShow(ShareLinkContent.class)) {
                    if(facebookUrl==null)
                        facebookUrl="http://export.highcharts.com/";
                    ShareLinkContent content = new ShareLinkContent.Builder()
                            .setContentUrl(Uri.parse(facebookUrl))
                            .setRef("export.highcharts.com")
                            .build();
                    Log.d("faceBook","I am here after after after  fafebook");
                    shareDialog.show(content);
                }


            }
        });







        //show information in table or not
        if((Boolean) getActivity().getIntent().getSerializableExtra("ifSuccess"))
        {
           /* ArrayList<StockTable> list = new ArrayList<>();
            list.add(stockTableA);
            Log.d("contenty",stockTableA.getStockSymbol());
            ListView stockView = (ListView) rootView.findViewById(R.id.stock_information);
            StockTableAdapter stockTableAdapter =new StockTableAdapter(getActivity(),R.layout.stock_table_view,list);
            stockView.setAdapter(stockTableAdapter);*/



            ArrayList<Item> res =new ArrayList<>();
            Item symbol = new Item("Stock Symbol",stockTableA.getStockSymbol());
            res.add(symbol);

            Item price= new Item("Last Price",stockTableA.getLastPrice());
            res.add(price);


            Item change = new Item("Change", Double.toString(stockTableA.getChange())+"/"+stockTableA.getChangePercent());
            res.add(change);

            Item time = new Item("Timestamp", stockTableA.getTimestamp());
            res.add(time);

            Item open = new Item("Open", stockTableA.getOpen());
            res.add(open);

            Item close = new Item("Close", stockTableA.getClose());
            res.add(close);

            Item range = new Item("Day's Range", stockTableA.getDayRange());
            res.add(range);

            Item volume = new Item("Volume", stockTableA.getVolume());
            res.add(volume);

            ListView stockView = (ListView) rootView.findViewById(R.id.stock_information);
            ItemAdapter itemAdapter = new ItemAdapter(getActivity(),R.layout.stock_table_view2,res);
            stockView.setAdapter(itemAdapter);


        }
        else
        {
            ArrayList<String> fail = new ArrayList<>();
            fail.add("Failed to load data");
            ListView stockView = (ListView) rootView.findViewById(R.id.stock_information);
//            ArrayAdapter<String> arrayAdapter = new ArrayAdapter<String>(getActivity(),R.layout.fail2,fail);
            failAdapter failAdapter = new failAdapter(getActivity(),R.layout.fail2,fail);
            stockView.setAdapter(failAdapter);
            stockView.setDivider(null);
            stockView.setDividerHeight(0);
        }



        return rootView;
    }



    @Override
    public void onActivityResult(final int requestCode, final int resultCode, final Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        callbackManager.onActivityResult(requestCode, resultCode, data);
    }



}





