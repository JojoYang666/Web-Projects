package com.example.yangtian.stocksearch;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.text.Spannable;
import android.text.SpannableStringBuilder;
import android.text.style.ImageSpan;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by yangtian on 11/17/17.
 */

public class StockTableAdapter extends ArrayAdapter<StockTable> {

    private static final String TAG = "StockTableAdapter";
    private Context mContext;
    int mResource;

    public StockTableAdapter(@NonNull Context context, int resource, @NonNull ArrayList<StockTable> objects) {
        super(context, resource, objects);
        mContext=context;
        mResource=resource;
    }



    @NonNull
    @Override
    public View getView(int position, @Nullable View convertView, @NonNull ViewGroup parent) {
        String symbole = getItem(position).getStockSymbol();
        String price = getItem(position).getLastPrice();
        double change = getItem(position).getChange();
        String timestamp = getItem(position).getTimestamp();
        String open = getItem(position).getOpen();
        String close = getItem(position).getClose();
        String range = getItem(position).getDayRange();
        String volume = getItem(position).getVolume();
//        StockTable stockTable = new StockTable(symbole, price, change, timestamp,  open,  close, range,  volume);

        LayoutInflater inflater = LayoutInflater.from(mContext);
        convertView = inflater.inflate(mResource,parent,false);

        TextView symbolT =(TextView) convertView.findViewById(R.id.symbol);
        TextView symbol_content =(TextView) convertView.findViewById(R.id.symbol_content);
        symbolT.setText("Stock Symbol");
        symbol_content.setText(symbole);

        TextView priceT =(TextView) convertView.findViewById(R.id.price);
        TextView price_content =(TextView) convertView.findViewById(R.id.price_content);
        priceT.setText("Last Price");
        price_content.setText(price);

        TextView changeT =(TextView) convertView.findViewById(R.id.change);
        TextView changeT_content =(TextView) convertView.findViewById(R.id.change_content);
        changeT.setText("Change");

        String changeString = String.valueOf(change);
        String changePercent = getItem(position).getChangePercent();
        changeString=changeString+"("+changePercent+")";
        String temp = changeString+"  ";

        if(change>=0)
        {
            SpannableStringBuilder spannableStringBuilder = new SpannableStringBuilder(temp);
//            Bitmap inDe = BitmapFactory.decodeResource(getResources(),R.drawable.up);
            Bitmap inDe = BitmapFactory.decodeResource(mContext.getResources(),R.drawable.up);
            inDe=Bitmap.createScaledBitmap(inDe,100,100,false);
            spannableStringBuilder.setSpan(new ImageSpan(inDe),changeString.length(),temp.length(), Spannable.SPAN_INCLUSIVE_INCLUSIVE);
            changeT_content.setText(spannableStringBuilder, TextView.BufferType.SPANNABLE);
        }
        else
        {
            SpannableStringBuilder spannableStringBuilder = new SpannableStringBuilder(temp);
            Bitmap inDe = BitmapFactory.decodeResource(mContext.getResources(),R.drawable.downn);
            inDe=Bitmap.createScaledBitmap(inDe,100,100,false);
            spannableStringBuilder.setSpan(new ImageSpan(inDe),changeString.length(),temp.length(),Spannable.SPAN_INCLUSIVE_INCLUSIVE);
            changeT_content.setText(spannableStringBuilder, TextView.BufferType.SPANNABLE);
        }

//        changeT_content.setText(String.valueOf(change));

        TextView timestampT =(TextView) convertView.findViewById(R.id.timestamp);
        TextView timestampT_content =(TextView) convertView.findViewById(R.id.timestamp_content);
        timestampT.setText("Timestamp");
        timestampT_content.setText(timestamp);

        TextView openT =(TextView) convertView.findViewById(R.id.open);
        TextView openT_content =(TextView) convertView.findViewById(R.id.open_content);
        openT.setText("Open");
        openT_content.setText(open);

        TextView closeT =(TextView) convertView.findViewById(R.id.close);
        TextView closeT_content =(TextView) convertView.findViewById(R.id.close_content);
        closeT.setText("Close");
        closeT_content.setText(close);

        TextView rangeT =(TextView) convertView.findViewById(R.id.range);
        TextView rangeT_content =(TextView) convertView.findViewById(R.id.range_content);
        rangeT.setText("Day's Range");
        rangeT_content.setText(range);

        TextView volumeT =(TextView) convertView.findViewById(R.id.volume);
        TextView volumeT_content =(TextView) convertView.findViewById(R.id.volume_content);
        volumeT.setText("Volume");
        volumeT_content.setText(volume);
        return convertView;

    }

}
