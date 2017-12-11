package com.example.yangtian.stocksearch;

/**
 * Created by yangtian on 11/29/17.
 */

public class Item {
    private String symbol;
    private String value;

    public Item(String symbol, String value) {
        this.symbol = symbol;
        this.value = value;
    }

    public String getSymbol() {
        return symbol;
    }

    public void setSymbol(String symbol) {
        this.symbol = symbol;
    }

    public String getValue() {
        return value;
    }

    public void setValue(String value) {
        this.value = value;
    }
}
