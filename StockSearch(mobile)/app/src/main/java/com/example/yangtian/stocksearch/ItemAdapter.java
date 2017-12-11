package com.example.yangtian.stocksearch;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.support.annotation.NonNull;
import android.support.annotation.Nullable;
import android.text.Spannable;
import android.text.SpannableStringBuilder;
import android.text.style.ImageSpan;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import java.util.List;

/**
 * Created by yangtian on 11/29/17.
 */

public class ItemAdapter extends ArrayAdapter<Item> {


    private Context mContext;
    int mResource;


    public ItemAdapter(@NonNull Context context, int resource, @NonNull List<Item> objects) {
        super(context, resource, objects);
        mContext=context;
        mResource=resource;
    }

    @NonNull
    @Override
    public View getView(int position, @Nullable View convertView, @NonNull ViewGroup parent) {

        String first = getItem(position).getSymbol();
        String second = getItem(position).getValue();

        LayoutInflater inflater = LayoutInflater.from(mContext);
        convertView = inflater.inflate(mResource,parent,false);

        if(first.equals("Change"))
        {
//            String changeString = second;
            String[] res = second.split("/");
            String changeString = res[0];
            String changePercent= res[1];
            Log.d("originalINput",second);
            Log.d("result in adapter",changeString+"---"+changePercent);
            double change = Double.parseDouble(changeString);
            changeString=changeString+"("+changePercent+")";
            String temp = changeString+"  ";



            TextView symbolT =(TextView) convertView.findViewById(R.id.symbol_table);
            TextView symbol_content =(TextView) convertView.findViewById(R.id.value);
            symbolT.setText(first);
            if(change>=0)
            {

                SpannableStringBuilder spannableStringBuilder = new SpannableStringBuilder(temp);
//            Bitmap inDe = BitmapFactory.decodeResource(getResources(),R.drawable.up);
                Bitmap inDe = BitmapFactory.decodeResource(mContext.getResources(),R.drawable.up);
                inDe=Bitmap.createScaledBitmap(inDe,100,100,false);
                spannableStringBuilder.setSpan(new ImageSpan(inDe),changeString.length(),temp.length(), Spannable.SPAN_INCLUSIVE_INCLUSIVE);
                symbol_content.setText(spannableStringBuilder, TextView.BufferType.SPANNABLE);
            }
            else
            {
                SpannableStringBuilder spannableStringBuilder = new SpannableStringBuilder(temp);
                Bitmap inDe = BitmapFactory.decodeResource(mContext.getResources(),R.drawable.downn);
                inDe=Bitmap.createScaledBitmap(inDe,100,100,false);
                spannableStringBuilder.setSpan(new ImageSpan(inDe),changeString.length(),temp.length(),Spannable.SPAN_INCLUSIVE_INCLUSIVE);
                symbol_content.setText(spannableStringBuilder, TextView.BufferType.SPANNABLE);
            }


            return convertView;
        }
        TextView symbolT =(TextView) convertView.findViewById(R.id.symbol_table);
        TextView symbol_content =(TextView) convertView.findViewById(R.id.value);
        symbolT.setText(first);
        symbol_content.setText(second);


        return convertView;
    }
}
