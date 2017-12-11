package com.example.yangtian.stocksearch;

import java.io.Serializable;

/**
 * Created by yangtian on 11/19/17.
 */

public class NewsModel implements Serializable {
    private static final long serialVersionUID = 2L;
    private String title;
    private String author;
    private String time;
    private String link;

    public String getLink() {
        return link;
    }

    public void setLink(String link) {
        this.link = link;
    }

    public  NewsModel(){}

    public NewsModel(String title, String author, String time, String link) {
        this.title = title;
        this.author = author;
        this.time = time;
        this.link = link;
    }

    public static long getSerialVersionUID() {
        return serialVersionUID;
    }

    public String getTitle() {
        return title;
    }

    public void setTitle(String title) {
        this.title = title;
    }

    public String getAuthor() {
        return author;
    }

    public void setAuthor(String author) {
        this.author = author;
    }

    public String getTime() {
        return time;
    }

    public void setTime(String time) {
        this.time = time;
    }
}
