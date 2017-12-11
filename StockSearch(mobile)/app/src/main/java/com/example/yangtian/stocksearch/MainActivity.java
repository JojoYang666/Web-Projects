package com.example.yangtian.stocksearch;

import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.graphics.PorterDuff;
import android.os.Handler;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.util.Log;
import android.view.ContextMenu;
import android.view.Menu;
import android.view.MenuItem;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.InputMethodManager;
import android.widget.Adapter;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.view.View.OnClickListener;
import android.widget.CheckedTextView;
import android.widget.CompoundButton;
import android.widget.EditText;
import android.widget.ImageButton;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.PopupMenu;
import android.widget.PopupWindow;
import android.widget.ProgressBar;
import android.widget.Spinner;
import android.widget.Switch;
import android.widget.TextView;
import android.widget.Toast;


import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonArrayRequest;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;
import com.google.gson.Gson;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.text.DecimalFormat;
import java.text.NumberFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.TreeMap;


public class MainActivity extends AppCompatActivity implements OnClickListener {

    private AutoCompleteTextView textView;
    private List<String>  responseContent = new ArrayList<String>();
    private Context context=this;
    private RequestQueue requestQueue;
    private int symbolState=0;
    private int articalState=0;
    private StockTable stockTable;
    private ArrayList<NewsModel> res;
    private String input;
    private int ifCurrentAscending=1;
    private ArrayList<StockTable> love=new ArrayList<>();
    private SharedPreferences sharedPreferences;
    private ListView listView;
    private ProgressBar search;
    private boolean artical = true;
    private boolean stock =true;
    private int count;
    private  TreeMap<String,StockTable> resLove;
    private  Handler handler;
    private Runnable runnable;
    private Spinner spinnerSort;
    private  ArrayAdapter<String> adapter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_HIDDEN);



        EditText dummy = (EditText) findViewById(R.id.dummy);
        dummy.requestFocus();

        Button button=(Button) findViewById(R.id.button1);
        button.setOnClickListener(this);

//          search = (LinearLayout) this.findViewById(R.id.searchProgress);
         search =(ProgressBar) findViewById(R.id.progressBar2);
         search.getIndeterminateDrawable().setColorFilter(Color.BLACK, PorterDuff.Mode.MULTIPLY);


        final String url ="http://nodetry-env.us-east-2.elasticbeanstalk.com/auto";

//        requestWindowFeature(Window.FEATURE_INDETERMINATE_PROGRESS);

        //get data for autocomplete
        textView = (AutoCompleteTextView)findViewById(R.id.autoCompleteTextView);
        textView.setInputType(InputType.TYPE_TEXT_FLAG_NO_SUGGESTIONS);

        textView.clearFocus();
        textView.setThreshold(1);
        requestQueue = Volley.newRequestQueue(this);

        TextWatcher searchChanged = new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {

            }

            @Override
            public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {

                search.setVisibility(View.VISIBLE);
                input=textView.getText().toString();
                final String urlA = url+input;
                if(input.equals(""))
                {
                    search.setVisibility(View.GONE);
                    return;
                }

                Log.d("URLCONTENT", "I am herea");
//                adapter = new ArrayAdapter<String>(context,
//                        android.R.layout.simple_dropdown_item_1line);


                adapter = new ArrayAdapter<String>(context,
                        android.R.layout.simple_dropdown_item_1line, responseContent);

                JsonArrayRequest jsonArrayRequest = new JsonArrayRequest(Request.Method.GET, urlA, null, new Response.Listener<JSONArray>() {
                    @Override
                    public void onResponse(JSONArray response) {
                        Log.d("JSONFirst",response.toString());
                        responseContent.clear();
//                        adapter.clear();

                        for(int i=0; i<response.length();i++)
                        {
                            try {
                                if(i==5) break;
                                Log.d("current Index", Integer.toString(i));
                                JSONObject jresponse = response.getJSONObject(i);
//                                Log.d("JSON",jresponse.toString());
                                String name= jresponse.getString("Name");
                                String symbol=jresponse.getString("Symbol");
                                String exchange=jresponse.getString("Exchange");
                                String res=symbol+' '+'-'+' '+name+'('+exchange+')';
                                responseContent.add(res);
//                                adapter.add(res);
//                                adapter.notifyDataSetChanged();
//                                Log.d("res",res);
                            } catch (JSONException e) {
                                Toast.makeText(getApplicationContext(),"failed to get symbol",Toast.LENGTH_SHORT).show();
                                e.printStackTrace();
                            }
                        }


//                        textView.setThreshold(1);
                        Log.d("Respnsecontext",adapter.toString());

                        Log.d("number",String.valueOf(adapter.getCount()));

                        textView.setAdapter(adapter);
                        adapter.notifyDataSetChanged();
//                        Log.d("AFterSET",responseContent.toString());
                        search.setVisibility(View.GONE);
//                        setProgressBarIndeterminateVisibility(false);



                    }
                },
                        new Response.ErrorListener() {
                            @Override
                            public void onErrorResponse(VolleyError error) {
                                responseContent.clear();
                                Log.d("ERROR","there is some error");
                                Toast.makeText(getApplicationContext(),"failed to get symbol",Toast.LENGTH_SHORT).show();
                                search.setVisibility(View.GONE);
                            }
                        });
                requestQueue.add(jsonArrayRequest);
//                search.setVisibility(View.GONE);

            }



            @Override
            public void afterTextChanged(Editable editable) {

            }


        };
        textView.addTextChangedListener(searchChanged);


        //spinener dropdown for sort
        spinnerSort=(Spinner)findViewById(R.id.sort);
//        ArrayAdapter<CharSequence> adapterSort= ArrayAdapter.createFromResource(this,R.array.sort,android.R.layout.simple_spinner_item);
//        adapterSort.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
//        spinnerSort.setAdapter(adapterSort);


        final String[] sorts = new String[]{
                "Sort by",
                "Default",
                "Symbol",
                "Price",
                "Change"
        };

        final List<String> sortList = new ArrayList<>(Arrays.asList(sorts));
        final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<String>(
                this,android.R.layout.simple_spinner_item,sortList){
            @Override
            public boolean isEnabled(int position){
                if(position == 0)
                {
                    // Disable the second item from Spinner
                    return false;
                }
                else
                {
                    return true;
                }
            }

            @Override
            public View getDropDownView(int position, View convertView,
                                        ViewGroup parent) {
                View view = super.getDropDownView(position, convertView, parent);
                TextView tv = (TextView) view;
                if(position==0) {
                    // Set the disable item text color
                    tv.setTextColor(Color.GRAY);
                }
                else {
                    tv.setTextColor(Color.BLACK);
                }
                return view;
            }
        };

        spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spinnerSort.setAdapter(spinnerArrayAdapter);

//        spinnerArrayAdapter.isEnabled()





        //spinener dropdown for order
        final Spinner spinnerOrder=(Spinner)findViewById(R.id.order);
//        ArrayAdapter<CharSequence> adapterOrder= ArrayAdapter.createFromResource(this,R.array.order,android.R.layout.simple_spinner_item);
//        adapterOrder.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
//        spinnerOrder.setAdapter(adapterOrder);


        final String[] ordes = new String[]{
                "Order",
                "Ascending",
                "Descending"

        };

        final List<String> ordersList = new ArrayList<>(Arrays.asList(ordes));
        final ArrayAdapter<String> adapterOrder = new ArrayAdapter<String>(
                this,android.R.layout.simple_spinner_item,ordersList){
            @Override
            public boolean isEnabled(int position){
                if(position == 0)
                {
                    // Disable the second item from Spinner
                    return false;
                }
                else
                {
                    return true;
                }
            }

            @Override
            public View getDropDownView(int position, View convertView,
                                        ViewGroup parent) {
                View view = super.getDropDownView(position, convertView, parent);
                TextView tv = (TextView) view;
                if(position==0) {
                    // Set the disable item text color
                    tv.setTextColor(Color.GRAY);
                }
                else {
                    tv.setTextColor(Color.BLACK);
                }
                return view;
            }
        };

        adapterOrder.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spinnerOrder.setAdapter(adapterOrder);


        //get prefrenced list
        sharedPreferences = getSharedPreferences(getString(R.string.preference_file_key),Context.MODE_PRIVATE);

        Map<String,?> keys = sharedPreferences.getAll();
        love = new ArrayList<StockTable>();
        for(Map.Entry<String,?> entry:keys.entrySet())
        {
            String key = entry.getKey();
            Gson gson = new Gson();
            String json = entry.getValue().toString();
            StockTable stockTable = new StockTable();
            stockTable = gson.fromJson(json,StockTable.class);
//            Log.d("getLOve",stockTable.getStockSymbol());
            love.add(stockTable);
        }
        //set listView for love
        listView = (ListView)findViewById(R.id.favorite_list);
        favoriteAdaptor favoriteAdaptorptor = new favoriteAdaptor(this, R.layout.favorite_list_view,love);
        listView.setAdapter(favoriteAdaptorptor);
//        registerForContextMenu(listView);


        //delete item
        listView.setOnItemLongClickListener(new AdapterView.OnItemLongClickListener() {
            @Override
            public boolean onItemLongClick(AdapterView<?> adapterView, View view, int i, long l) {
                Log.d("long","i am in long");
                final String key = love.get(i).getStockSymbol();
                final int position =i;
                PopupMenu popupMenu = new PopupMenu(context,listView);
                popupMenu.getMenuInflater().inflate(R.menu.popup,popupMenu.getMenu());
                popupMenu.show();
                popupMenu.setOnMenuItemClickListener(new PopupMenu.OnMenuItemClickListener() {
                    @Override
                    public boolean onMenuItemClick(MenuItem menuItem) {
                        if(!menuItem.getTitle().toString().equals("Yes")) return false;
//                        Log.d("itemId", String.valueOf(menuItem.getTitle()));
                        sharedPreferences = getSharedPreferences(getString(R.string.preference_file_key),Context.MODE_PRIVATE);
                        SharedPreferences.Editor editor = sharedPreferences.edit();
                        editor.remove(key);
                        editor.apply();
                        love.remove(position);
                        listView = (ListView)findViewById(R.id.favorite_list);
                        favoriteAdaptor favoriteAdaptorptor = new favoriteAdaptor(context, R.layout.favorite_list_view,love);
                        listView.setAdapter(favoriteAdaptorptor);
                        return true;
                    }
                });
                return true;
        }
        });



        //search item in lidtview
        listView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                String key = love.get(i).getStockSymbol();
                search(key);
            }
        });

        //events for spinner of sort
        spinnerSort.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                if(i==2)
                {
                    Log.d("sort","symbol");
                    Collections.sort(love,new SortBySymbol());
                }
                if(i==3)
                {
                    Collections.sort(love,new SortByPrice());
                    Log.d("sort","price");
                }
                if(i==4)
                {
                    Collections.sort(love,new SortByChange());
                    Log.d("sort","change");
                }
                if(spinnerOrder.getSelectedItem().toString().equals("Descending"))
                {
                    Collections.reverse(love);
                    ifCurrentAscending=0;
                }
                ListView listView = (ListView)findViewById(R.id.favorite_list);
                favoriteAdaptor favoriteAdaptorptor = new favoriteAdaptor(context, R.layout.favorite_list_view,love);
                listView.setAdapter(favoriteAdaptorptor);
            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });



        //event for spinner of order
        spinnerOrder.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                if(spinnerOrder.getSelectedItem().toString().equals("Ascending")&&ifCurrentAscending==0)
                {
                    Collections.reverse(love);
                    ifCurrentAscending=1;
                }
                else if(spinnerOrder.getSelectedItem().toString().equals("Descending")&&ifCurrentAscending==1)
                {
                    Collections.reverse(love);
                    ifCurrentAscending=0;
                }
                ListView listView = (ListView)findViewById(R.id.favorite_list);
                favoriteAdaptor favoriteAdaptorptor = new favoriteAdaptor(context, R.layout.favorite_list_view,love);
                listView.setAdapter(favoriteAdaptorptor);
            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });


        //event for fresh once
        ImageButton freshOnce = (ImageButton) findViewById((R.id.freshOnce));
        freshOnce.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                search.setVisibility(View.VISIBLE);
                loveSearch(love);
                Log.d("loveonce","loveonce");
            }
        });



        //event for autorefresh
        Switch autorefresh = (Switch) findViewById(R.id.autorefresh);
        autorefresh.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton compoundButton, boolean b) {
                if(b)
                {
                    handler = new Handler();
                    runnable = new Runnable() {
                        @Override
                        public void run() {
                            search.setVisibility(View.VISIBLE);
                            loveSearch(love);
                            handler.postDelayed(this,2000);
                        }
                    };
                    handler.postDelayed(runnable,2000);//
                }
                else
                {
                    handler.removeCallbacks(runnable);
                    Log.d("removeSuccess","remove handler success");
                }
            }
        });




    }


    String msg="0";

    // event for search
    public void onClick(View v) {
//        Log.d("which one", String.valueOf(v.getId()));
        input= textView.getText().toString();;
        search(textView.getText().toString());



    }




    //search function
    public void search(String quote)
    {
//        quote=textView.getText().toString();
        search.setVisibility(View.VISIBLE);
        if(quote.isEmpty())
        {
            Log.d("validaeion", "validation");
            Toast.makeText(getApplicationContext(),"Please enter a stock name or symbol", Toast.LENGTH_LONG).show();
//            validation.show();
            search.setVisibility(View.GONE);
            return;
        }
        final String[] inputs=quote.split(" - ");
        quote=inputs[0];
        String urlStock="http://nodetry-env.us-east-2.elasticbeanstalk.com/symbol?";
        String requestStockUrl=urlStock+quote;
        requestQueue = Volley.newRequestQueue(this);

        final String finalQuote = quote;
        final String finalQuote1 = quote;
        JsonObjectRequest jsonArrayRequest = new JsonObjectRequest(Request.Method.GET, requestStockUrl, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject response) {
                Log.d("stockContent",response.toString());

                stockTable= new StockTable();
                stockTable.setStockSymbol(finalQuote);
//                List<StockTable> res = new ArrayList<StockTable>();
                Log.d("SockUrl", "I am in stock");
                try {
                    if(response.getJSONObject("Meta Data")==null)
                    {
                        if(articalState==1)
                        {
                            Intent nextPage = new Intent(MainActivity.this, Main2Activity.class);
                            nextPage.putExtra("stockTable", stockTable);
                            nextPage.putExtra("articalList",res);
                            nextPage.putExtra("symbol", finalQuote1);
                            nextPage.putExtra("ifSuccess",false);
                            if(artical)
                                nextPage.putExtra("articalSuccess",true);
                            else
                                nextPage.putExtra("articalSuccess",false);
                            symbolState=0;
                            articalState=0;
                            search.setVisibility(View.GONE);
                            startActivity(nextPage);
                        }
                        symbolState=1;
                        stock=false;
                        return;
                    }
                    Log.d("responsecontentstock",response.toString());
                    stockTable.setStockSymbol(response.getJSONObject("Meta Data").getString("2. Symbol"));
                    JSONObject stockContent = response.getJSONObject("Time Series (Daily)");
                    Iterator<String> key = stockContent.keys();
                    int i=0;
                    ArrayList<String> keyName= new ArrayList<String>();
                    while(key.hasNext())
                    {
                        keyName.add(key.next());
                        i++;
                        if(i==160) break;
                    }
                    DecimalFormat df = new DecimalFormat("#.00");
                    String FirstKey = keyName.get(0);
                    String SecondKey = keyName.get(1);
                    String changeToday = stockContent.getJSONObject(FirstKey).getString("4. close");
                    changeToday=df.format(Double.valueOf(changeToday));
                    String changePrevious = stockContent.getJSONObject(SecondKey).getString("4. close");
                    double change = Double.parseDouble(changeToday)-Double.parseDouble(changePrevious);
                    change=Math.round(change * 100.0) / 100.0;
                    double changePercent = change/Double.parseDouble(changePrevious);
                    NumberFormat defaultFormat = NumberFormat.getPercentInstance();
                    defaultFormat.setMinimumFractionDigits(2);
                    defaultFormat.setMaximumFractionDigits(2);
                    String changePercentString=defaultFormat.format(changePercent);
                    String timeStamp = response.getJSONObject("Meta Data").getString("3. Last Refreshed");
                    if(timeStamp.length()==10)
                    {
                        timeStamp=timeStamp+" 16:00:00 EDT";
                    }
                    else
                        timeStamp=timeStamp+" EDT";

                    stockTable.setChangePercent(changePercentString);
                    stockTable.setChange(change);
                    stockTable.setLastPrice(changeToday);
                    stockTable.setTimestamp(timeStamp);

                    String open=df.format(Double.valueOf(stockContent.getJSONObject(FirstKey).getString("1. open")));
                    stockTable.setOpen(open);


                    stockTable.setClose(changeToday);


                    stockTable.setVolume(stockContent.getJSONObject(FirstKey).getString("5. volume"));


                    String range=df.format(Double.valueOf(stockContent.getJSONObject(FirstKey).getString("3. low")))+" - "+df.format(Double.valueOf(stockContent.getJSONObject(FirstKey).getString("2. high")));
                    stockTable.setDayRange(range);
                    if(articalState==1)
                    {
                        Intent nextPage = new Intent(MainActivity.this, Main2Activity.class);
                        nextPage.putExtra("stockTable", stockTable);
                        nextPage.putExtra("articalList",res);
                        nextPage.putExtra("symbol",finalQuote1);
                        nextPage.putExtra("ifSuccess",true);
                        if(artical)
                            nextPage.putExtra("articalSuccess",true);
                        else
                            nextPage.putExtra("articalSuccess",false);
                        symbolState=0;
                        articalState=0;
                        search.setVisibility(View.GONE);
                        startActivity(nextPage);
                    }
                    symbolState=1;
                    stock=true;
//                    res.add(stockTable);
                    Log.d("StockRespoce",stockTable.getStockSymbol() );
                } catch (JSONException e) {
                    e.printStackTrace();
                    if(articalState==1)
                    {
                        Intent nextPage = new Intent(MainActivity.this, Main2Activity.class);
                        nextPage.putExtra("stockTable", stockTable);
                        nextPage.putExtra("articalList",res);
                        nextPage.putExtra("symbol",finalQuote1);
                        nextPage.putExtra("ifSuccess",false);
                        if(artical)
                            nextPage.putExtra("articalSuccess",true);
                        else
                            nextPage.putExtra("articalSuccess",false);
                        symbolState=0;
                        articalState=0;
                        search.setVisibility(View.GONE);
                        startActivity(nextPage);
                    }
                    symbolState=1;
                    stock=false;
                    return;
                }


            }
        },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        Log.d("ERROR", String.valueOf(error));
                        if(articalState==1)
                        {
                            Intent nextPage = new Intent(MainActivity.this, Main2Activity.class);
                            nextPage.putExtra("stockTable", stockTable);
                            nextPage.putExtra("articalList",res);
                            nextPage.putExtra("symbol",finalQuote1);
                            nextPage.putExtra("ifSuccess",false);
                            if(artical)
                                nextPage.putExtra("articalSuccess",true);
                            else
                                nextPage.putExtra("articalSuccess",false);
                            symbolState=0;
                            articalState=0;
                            search.setVisibility(View.GONE);
                            startActivity(nextPage);
                        }
                        symbolState=1;
                        stock=false;
                        return;
                    }
                });

        //request information about artical
        String artcalUrl="http://nodetry-env.us-east-2.elasticbeanstalk.com/artical?";
        String requestArticalUrl=artcalUrl+finalQuote1;

//        requestQueue = Volley.newRequestQueue(this);
        Log.d("Artical", requestArticalUrl);
        JsonObjectRequest jsonArrayRequestArtical = new JsonObjectRequest(Request.Method.GET, requestArticalUrl, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject response) {

                res = new ArrayList<NewsModel>();

                Log.d("try","sjhdksldhslnckldsvnnfdjn   skhjflsajcnsladncsa   sjfknlsanckldsnfve  sjfnklsd");

                try {
                    if(response.getJSONObject("rss")==null)
                    {
                        if(symbolState==1)
                        {
                            Intent nextPage = new Intent(MainActivity.this, Main2Activity.class);
                            nextPage.putExtra("stockTable", stockTable);
                            nextPage.putExtra("articalList",res);
                            nextPage.putExtra("symbol",finalQuote1);
                            nextPage.putExtra("articalSuccess",false);
                            if(stock)
                                nextPage.putExtra("ifSuccess",true);
                            else
                                nextPage.putExtra("ifSuccess",false);
                            symbolState=0;
                            articalState=0;
                            search.setVisibility(View.GONE);
                            startActivity(nextPage);
                        }
                        articalState=1;
                        artical=false;
                        return;
                    }
                    JSONArray channel = response.getJSONObject("rss").getJSONArray("channel");
                    Log.d("responseContent",channel.toString());
                    JSONArray item =channel.getJSONObject(0).getJSONArray("item");
                    Log.d("itemLength",String.valueOf(item.length()));
                    int k=0;
                    for(int i=0; i<item.length();i++)
                    {
                        JSONObject artical = item.getJSONObject(i);

                        String ifArtical=artical.getString("link");

                        if(ifArtical.contains("article")){
                            if(k>4) break;
                            NewsModel newsModel = new NewsModel();
                            newsModel.setLink(ifArtical);
                            JSONArray title = artical.getJSONArray("title");
                            JSONArray author = artical.getJSONArray("sa:author_name");
                            JSONArray time = artical.getJSONArray("pubDate");
                            JSONArray link = artical.getJSONArray("link");
                            String timeRes = time.getString(0);
                            int length = timeRes.length()-5;
                            timeRes = timeRes.substring(0,length)+"PDT";
                            newsModel.setTitle(title.getString(0));
                            newsModel.setAuthor(author.getString(0));
                            newsModel.setTime(timeRes);
                            newsModel.setLink(link.getString(0));
                            res.add(newsModel);
                            k++;
                        }
                    }
                    Log.d("responseContent",item.toString());
                    if(symbolState==1)
                    {
                        Intent nextPage = new Intent(MainActivity.this, Main2Activity.class);
                        nextPage.putExtra("stockTable", stockTable);
                        nextPage.putExtra("articalList",res);
                        nextPage.putExtra("symbol",finalQuote1);
                        nextPage.putExtra("articalSuccess",true);
                        if(stock)
                            nextPage.putExtra("ifSuccess",true);
                        else
                            nextPage.putExtra("ifSuccess",false);
                        symbolState=0;
                        articalState=0;
                        search.setVisibility(View.GONE);
                        startActivity(nextPage);
                    }
                    articalState=1;
                    artical=true;
                } catch (JSONException e) {
                    e.printStackTrace();
                    if(symbolState==1)
                    {
                        Intent nextPage = new Intent(MainActivity.this, Main2Activity.class);
                        nextPage.putExtra("stockTable", stockTable);
                        nextPage.putExtra("articalList",res);
                        nextPage.putExtra("symbol",finalQuote1);
                        nextPage.putExtra("articalSuccess",false);
                        if(stock)
                            nextPage.putExtra("ifSuccess",true);
                        else
                            nextPage.putExtra("ifSuccess",false);
                        symbolState=0;
                        articalState=0;
                        search.setVisibility(View.GONE);
                        startActivity(nextPage);
                    }
                    articalState=1;
                    artical=false;
                    return;
                }

            }
        },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        if(symbolState==1)
                        {
                            Intent nextPage = new Intent(MainActivity.this, Main2Activity.class);
                            nextPage.putExtra("stockTable", stockTable);
                            nextPage.putExtra("articalList",res);
                            nextPage.putExtra("symbol",finalQuote1);
                            nextPage.putExtra("articalSuccess",false);
                            if(stock)
                                nextPage.putExtra("ifSuccess",true);
                            else
                                nextPage.putExtra("ifSuccess",false);
                            symbolState=0;
                            articalState=0;
                            search.setVisibility(View.GONE);
                            startActivity(nextPage);
                        }
                        articalState=1;
                        artical=false;

                        return;

                    }
                }
        );
        requestQueue.add(jsonArrayRequest);
        requestQueue.add(jsonArrayRequestArtical);
    }

    // event for clear
    public void clearInput(View view) {
        Log.d("clear", "I am in clear");
        textView.setText("");
        Toast.makeText(getApplicationContext(),"Please enter a stock name or symbol", Toast.LENGTH_LONG).cancel();
//        validation.cancel();
    }



    //search information in love
    public  void loveSearch(ArrayList<StockTable> loveFresh)
    {
        resLove = new TreeMap<>();
        for(StockTable temp:loveFresh)
        {
            resLove.put(temp.getStockSymbol(),temp);
        }
        count = loveFresh.size();
        for(int i=0;i<loveFresh.size();i++)
        {
            String urlStock="http://nodetry-env.us-east-2.elasticbeanstalk.com/symbol?";
            final String symbol=loveFresh.get(i).getStockSymbol();
            String requestStockUrl=urlStock+symbol;
            requestQueue = Volley.newRequestQueue(this);

            JsonObjectRequest jsonArrayRequest = new JsonObjectRequest(Request.Method.GET, requestStockUrl, null, new Response.Listener<JSONObject>() {
                @Override
                public void onResponse(JSONObject response) {
                    StockTable lovestock = new StockTable();
                    try {
                        if(response.getJSONObject("Meta Data")==null)
                        {

                            count--;
                            if(count==0)
                            {
//                                ArrayList<StockTable> loveNew = new ArrayList<>();
                                love.clear();
                                for(Map.Entry<String,StockTable> entry:resLove.entrySet()){
                                    love.add(entry.getValue());
                                }
                                listView = (ListView)findViewById(R.id.favorite_list);
                                favoriteAdaptor favoriteAdaptorptor = new favoriteAdaptor(context, R.layout.favorite_list_view,love);
                                listView.setAdapter(favoriteAdaptorptor);
                                search.setVisibility(View.GONE);
                            }
//                            Toast.makeText(getApplicationContext(),"Please enter a stock name or symbol", Toast.LENGTH_LONG).show();
                            Log.d("refreshError","refreshError");
                            Toast.makeText(getApplicationContext(),"fail to update"+symbol,Toast.LENGTH_LONG).show();
                                return;
//                            res.put(symbol,)
                        }
                        else
                        {

                            String keyRes=response.getJSONObject("Meta Data").getString("2. Symbol");
                            lovestock.setStockSymbol(keyRes);
                            JSONObject stockContent = response.getJSONObject("Time Series (Daily)");
                            Iterator<String> key = stockContent.keys();
                            int i=0;
                            ArrayList<String> keyName= new ArrayList<String>();
                            while(key.hasNext())
                            {
                                keyName.add(key.next());
                                i++;
                                if(i==2) break;
                            }
                            Log.d("autocompelte",response.toString());
                            String FirstKey = keyName.get(0);
                            String SecondKey = keyName.get(1);
                            String changeToday = stockContent.getJSONObject(FirstKey).getString("4. close");

                            DecimalFormat df = new DecimalFormat("#.00");


                            String changePrevious = stockContent.getJSONObject(SecondKey).getString("4. close");
                            double change = Double.parseDouble(changeToday)-Double.parseDouble(changePrevious);
                            change=Math.round(change * 100.0) / 100.0;
                            double changePercent = change/Double.parseDouble(changePrevious);
                            NumberFormat defaultFormat = NumberFormat.getPercentInstance();
                            defaultFormat.setMinimumFractionDigits(2);
                            defaultFormat.setMaximumFractionDigits(2);
                            String changePercentString=defaultFormat.format(changePercent);
                            changeToday=df.format(Double.valueOf(changeToday));

                            lovestock.setChangePercent(changePercentString);
                            lovestock.setChange(change);
                            lovestock.setLastPrice(changeToday);
//                            lovestock.setTimestamp(response.getJSONObject("Meta Data").getString("3. Last Refreshed"));
//                            lovestock.setOpen(stockContent.getJSONObject(FirstKey).getString("1. open"));
//                            lovestock.setClose(stockContent.getJSONObject(FirstKey).getString("4. close"));
//                            lovestock.setVolume(stockContent.getJSONObject(FirstKey).getString("5. volume"));
//                            String range=stockContent.getJSONObject(FirstKey).getString("3. low")+" - "+stockContent.getJSONObject(FirstKey).getString("2. high");
//                            lovestock.setDayRange(range);


//                            resLove.remove(keyRes);
                            resLove.put(keyRes,lovestock);
                            count--;
                            if(count==0)
                            {
//                                ArrayList<StockTable> loveNew = new ArrayList<>();
                                love.clear();
                                for(Map.Entry<String,StockTable> entry:resLove.entrySet()){
                                    love.add(entry.getValue());
                                }
                                listView = (ListView)findViewById(R.id.favorite_list);
                                favoriteAdaptor favoriteAdaptorptor = new favoriteAdaptor(context, R.layout.favorite_list_view,love);
                                listView.setAdapter(favoriteAdaptorptor);
                                search.setVisibility(View.GONE);
                            }
                            return;


                        }

                    } catch (JSONException e) {
                        e.printStackTrace();

                        count--;

                            if(count==0)
                            {
//                                ArrayList<StockTable> loveNew = new ArrayList<>();
                                love.clear();
                                for(Map.Entry<String,StockTable> entry:resLove.entrySet()){
                                    love.add(entry.getValue());
                                }
                                listView = (ListView)findViewById(R.id.favorite_list);
                                favoriteAdaptor favoriteAdaptorptor = new favoriteAdaptor(context, R.layout.favorite_list_view,love);
                                listView.setAdapter(favoriteAdaptorptor);
                                search.setVisibility(View.GONE);
                            }
                        Log.d("refreshError","refreshError");
                        Toast.makeText(getApplicationContext(),"fail to update"+symbol,Toast.LENGTH_LONG).show();
                            return;
                    }

                }
            },
                    new Response.ErrorListener() {
                        @Override
                        public void onErrorResponse(VolleyError error) {

                            count--;
                            if(count==0)
                                if(count==0)
                                {
//                                    ArrayList<StockTable> loveNew = new ArrayList<>();
                                    love.clear();
                                    for(Map.Entry<String,StockTable> entry:resLove.entrySet()){
                                        love.add(entry.getValue());
                                    }

                                    listView = (ListView)findViewById(R.id.favorite_list);
                                    favoriteAdaptor favoriteAdaptorptor = new favoriteAdaptor(context, R.layout.favorite_list_view,love);
                                    listView.setAdapter(favoriteAdaptorptor);
                                    search.setVisibility(View.GONE);
                                }
                            Log.d("refreshError","refreshError");
                            Toast.makeText(getApplicationContext(),"fail to update"+symbol,Toast.LENGTH_LONG);
                                return;
                        }
                    });
            requestQueue.add(jsonArrayRequest);

        }






    }

    public void diableItem(View view) {
        Log.d("item",spinnerSort.getSelectedItem().toString());
//        spinnerSort.getSelectedItem().
//        int i = spinnerSort.getGravity();
//        Log.d("gravity",String.valueOf(i));

    }

    //hide keyboard

}


class SortBySymbol implements Comparator<StockTable> {

    @Override
    public int compare(StockTable stockTable, StockTable t1) {
        return stockTable.getStockSymbol().compareTo(t1.getStockSymbol());
    }
}

class SortByPrice implements Comparator<StockTable>{


    @Override
    public int compare(StockTable stockTable, StockTable t1) {
        if(Double.parseDouble(stockTable.getLastPrice())>Double.parseDouble(t1.getLastPrice()))
            return 1;
        else if(Double.parseDouble(stockTable.getLastPrice())==Double.parseDouble(t1.getLastPrice()))
            return 0;
        else
            return -1;
    }
}

class SortByChange implements Comparator<StockTable>{

    @Override
    public int compare(StockTable stockTable, StockTable t1) {
        if(stockTable.getChange()>t1.getChange())
            return 1;
        else if(stockTable.getChange()==t1.getChange())
            return 0;
        else
            return -1;
    }
}
