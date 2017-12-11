package com.example.yangtian.stocksearch;

import android.content.Context;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import java.util.ArrayList;

/**
 * Created by yangtian on 11/21/17.
 */

public class failAdapter extends ArrayAdapter<String> {
    private Context mContext;
    int mResource;

    public failAdapter(@NonNull Context context, int resource, @NonNull ArrayList<String> objects) {
        super(context, resource, objects);
        mContext=context;
        mResource=resource;
    }

    @NonNull
    @Override
    public View getView(int position, @Nullable View convertView, @NonNull ViewGroup parent) {
        LayoutInflater inflater = LayoutInflater.from(mContext);
        convertView = inflater.inflate(mResource,parent,false);

        String title = getItem(position).toString();
        TextView titleView = (TextView) convertView.findViewById(R.id.failtry);
        titleView.setText(title);
        return convertView;
    }


}
