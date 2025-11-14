package com.example.siferactivitymain;

import retrofit2.Call;           // Para Call<T>
import retrofit2.http.GET;       // Para @GET
import java.util.List;

public interface SiferApi {
    @GET("api_productos.php")
    Call<ApiResponse> getProductos();
}