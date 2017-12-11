package com.example.yangtian.stocksearch;

import android.content.Context;
import android.graphics.Color;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import java.util.List;

/**
 * Created by yangtian on 11/20/17.
 */

public class favoriteAdaptor extends ArrayAdapter<StockTable> {

    private static final String TAG = "favoriteAdapter";
    private Context mContext;
    int mResource;


    public favoriteAdaptor(@NonNull Context context, int resource, @NonNull List<StockTable> objects) {
        super(context, resource, objects);
        mContext=context;
        mResource=resource;
    }

    @NonNull
    @Override
    public View getView(int position, @Nullable View convertView, @NonNull ViewGroup parent) {
        String  symbol = getItem(position).getStockSymbol();
        String  price = getItem(position).getLastPrice();
        String changePercent = getItem(position).getChangePercent();
        double change = getItem(position).getChange();

        StockTable stockTable = new StockTable(symbol, price,change, changePercent);


        LayoutInflater inflater = LayoutInflater.from(mContext);
        convertView = inflater.inflate(mResource,parent,false);

        TextView symbolT = convertView.findViewById(R.id.love_symbol);
        symbolT.setText(symbol);

        TextView priceT = convertView.findViewById(R.id.love_price);
        priceT.setText(price);

        TextView changePerT = convertView.findViewById(R.id.love_change);
        String changeLove = String.valueOf(change)+"("+changePercent+")";
        changePerT.setText(changeLove);
        if(change>=0)
            changePerT.setTextColor(Color.parseColor("#ff99cc00"));
        else
            changePerT.setTextColor(Color.parseColor("#ffff4444"));


        return convertView;
    }
}
