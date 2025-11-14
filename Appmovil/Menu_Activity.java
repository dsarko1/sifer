package com.example.siferactivitymain;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.Toast;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class Menu_Activity extends AppCompatActivity {

    private RecyclerView rvstock;
    private ProductoAdapter adapter;
    private SiferApi apiService;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_menu);

        rvstock = findViewById(R.id.rvstock);

        GridLayoutManager layoutManager = new GridLayoutManager(this, 2);
        rvstock.setLayoutManager(layoutManager);

        apiService = ApiClient.getClient().create(SiferApi.class);

        cargarProductosDesdeAPI();
    }

    private void cargarProductosDesdeAPI() {
        Call<ApiResponse> call = apiService.getProductos();

        call.enqueue(new Callback<ApiResponse>() {
            @Override
            public void onResponse(Call<ApiResponse> call, Response<ApiResponse> response) {
                if (response.isSuccessful() && response.body() != null) {
                    ApiResponse apiResponse = response.body();

                    if (apiResponse.isSuccess()) {
                        List<Producto> productosDeLaWeb = apiResponse.getData();

                        if (productosDeLaWeb.isEmpty()) {
                            Toast.makeText(Menu_Activity.this,
                                    "✅ Conexión exitosa - No hay productos",
                                    Toast.LENGTH_SHORT).show();
                        } else {
                            adapter = new ProductoAdapter(productosDeLaWeb);
                            rvstock.setAdapter(adapter);
                        }
                    }
                }
            }

            @Override
            public void onFailure(Call<ApiResponse> call, Throwable t) {
                Toast.makeText(Menu_Activity.this,
                        "Error de conexión: " + t.getMessage(),
                        Toast.LENGTH_SHORT).show();
            }
        });
    }
}