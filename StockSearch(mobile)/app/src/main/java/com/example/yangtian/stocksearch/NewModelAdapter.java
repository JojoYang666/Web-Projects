package com.example.yangtian.stocksearch;

import android.content.Context;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.text.Html;
import android.text.Spannable;
import android.text.method.LinkMovementMethod;
import android.text.style.URLSpan;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by yangtian on 11/19/17.
 */

public class NewModelAdapter extends ArrayAdapter<NewsModel> {

    private static final String TAG = "NewModelAdapter";
    private Context mContext;
    int mResource;

    public NewModelAdapter(@NonNull Context context, int resource, @NonNull ArrayList<NewsModel> objects) {
        super(context, resource, objects);
        mContext=context;
        mResource=resource;
    }

    @NonNull
    @Override
    public View getView(int position, @Nullable View convertView, @NonNull ViewGroup parent) {
        String title = getItem(position).getTitle();
        String author = getItem(position).getAuthor();
        String date = getItem(position).getTime();
        String link = getItem(position).getLink();

        NewsModel newsModel = new NewsModel(title,author,date,link);

        LayoutInflater inflater = LayoutInflater.from(mContext);
        convertView = inflater.inflate(mResource,parent,false);

        TextView titleView = (TextView) convertView.findViewById(R.id.titile);
//        titleView.setClickable(true);
//        titleView.setMovementMethod(LinkMovementMethod.getInstance());
//
//        String text ="<a href='"+link+"'>"+title+"</a>";
//        Spannable spannedText = Spannable.Factory.getInstance().newSpannable(Html.fromHtml(text));
//        Spannable processedText = removeUnderlines(spannedText);
//
//        Log.d("linkStle",text);
//        titleView.setText(processedText);
        titleView.setText(title);

        TextView authorView = (TextView) convertView.findViewById(R.id.author);
        authorView.setText("Author: "+author);

        TextView DateView = (TextView) convertView.findViewById(R.id.time);
        DateView.setText("Date: "+date);

        return  convertView;


    }

    private Spannable removeUnderlines(Spannable spannedText) {
        URLSpan[] spans = spannedText.getSpans(0,spannedText.length(),URLSpan.class);
        for(URLSpan span:spans){
            int start = spannedText.getSpanStart(span);
            int end = spannedText.getSpanEnd(span);
            spannedText.removeSpan(span);
            span= new urlNoUnderline(span.getURL());
            spannedText.setSpan(span,start,end,0);
        }
       return spannedText;
    }
}
