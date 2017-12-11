package com.example.yangtian.stocksearch;

import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ListView;

import java.util.ArrayList;

/**
 * Created by yangtian on 11/16/17.
 */

public class News  extends Fragment {

    private  ArrayList<NewsModel> articalList;
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        View rootView = inflater.inflate(R.layout.news_fragment, container, false);

        articalList = new ArrayList<NewsModel>();
        articalList = (ArrayList<NewsModel>) getActivity().getIntent().getSerializableExtra("articalList");
//        Log.d("receiveNews", articalList.get(0).getTitle());
        if(!(Boolean)getActivity().getIntent().getSerializableExtra("articalSuccess"))
        {
            ListView articles = (ListView) rootView.findViewById(R.id.news);
            ArrayList<String> fail = new ArrayList<>();
            fail.add("Failed to load data");
            failAdapter failAdapter = new failAdapter(getActivity(),R.layout.fail_news,fail);
            articles.setAdapter(failAdapter);
            articles.setDivider(null);
            articles.setDividerHeight(0);
            Log.d("fail in news","fail in news");
            return rootView;
        }
        ListView articles = (ListView) rootView.findViewById(R.id.news);
        NewModelAdapter adapter = new NewModelAdapter(getActivity(),R.layout.list_adapter_view,articalList);
        articles.setAdapter(adapter);

        articles.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                Log.d("inNews","news here  news here  news here  news here  news here");
                String url = articalList.get(i).getLink();
                Log.d("urlgo",url);
                Log.d("position",String.valueOf(i));
                Uri uri = Uri.parse(url);
                Intent intent = new Intent(Intent.ACTION_VIEW, uri);
                startActivity(intent);
            }
        });
        return rootView;
    }
}
