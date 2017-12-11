package com.example.yangtian.stocksearch;

import java.io.Serializable;

/**
 * Created by yangtian on 11/17/17.
 */

public class StockTable implements Serializable {
    private static final long serialVersionUID = 1L;
    private String StockSymbol;
    private String LastPrice;
    private double Change;
    private String Timestamp;
    private String Open;
    private String Close;
    private String DayRange;
    private String Volume;
    private String changePercent;
    private boolean ifGet;

    public boolean isIfGet() {
        return ifGet;
    }

    public void setIfGet(boolean ifGet) {
        this.ifGet = ifGet;
    }

    public String getChangePercent() {
        return changePercent;
    }

    public void setChangePercent(String changePercent) {
        this.changePercent = changePercent;
    }

    public StockTable() {
    }

    public StockTable(String stockSymbol, String lastPrice, double change, String timestamp, String open, String close, String dayRange, String volume) {
        StockSymbol = stockSymbol;
        LastPrice = lastPrice;
        Change = change;
        Timestamp = timestamp;
        Open = open;
        Close = close;
        DayRange = dayRange;
        Volume = volume;
    }

    public StockTable(String stockSymbol, String lastPrice, double change, String changePercent) {
        StockSymbol = stockSymbol;
        LastPrice = lastPrice;
        Change = change;
        this.changePercent = changePercent;
    }

    public String getStockSymbol() {
        return StockSymbol;
    }

    public void setStockSymbol(String stockSymbol) {
        StockSymbol = stockSymbol;
    }

    public String getLastPrice() {
        return LastPrice;
    }

    public void setLastPrice(String lastPrice) {
        LastPrice = lastPrice;
    }

    public double getChange() {
        return Change;
    }

    public void setChange(double change) {
        Change = change;
    }

    public String getTimestamp() {
        return Timestamp;
    }

    public void setTimestamp(String timestamp) {
        Timestamp = timestamp;
    }

    public String getOpen() {
        return Open;
    }

    public void setOpen(String open) {
        Open = open;
    }

    public String getClose() {
        return Close;
    }

    public void setClose(String close) {
        Close = close;
    }

    public String getDayRange() {
        return DayRange;
    }

    public void setDayRange(String dayRange) {
        DayRange = dayRange;
    }

    public String getVolume() {
        return Volume;
    }

    public void setVolume(String volume) {
        Volume = volume;
    }
}
