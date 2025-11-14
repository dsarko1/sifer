package com.example.siferactivitymain;

import java.util.List;

public class ApiResponse {
    private boolean success;
    private List<Producto> data;
    private int count;
    private String message;

    // Getters
    public boolean isSuccess() { return success; }
    public List<Producto> getData() { return data; }
    public int getCount() { return count; }
    public String getMessage() { return message; }
}