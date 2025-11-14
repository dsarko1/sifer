package com.example.siferactivitymain;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

// ✅ CLASE ApiClient - será nuestro "centro de control" de API
public class ApiClient {

    // =============================================
    // ✅ CONSTANTE: URL BASE de tu servidor
    // =============================================

    // ⚠️ IMPORTANTE: Esta URL es TEMPORAL para pruebas locales
    // Para desarrollo local con XAMPP, necesitarás:
    // 1. Encontrar tu IP local: ipconfig (Windows) o ifconfig (Mac/Linux)
    // 2. Cambiar "192.168.1.100" por tu IP real
    // 3. Asegurarte de que "sifer" es el nombre de tu carpeta en XAMPP/htdocs/
    private static final String BASE_URL = "http://192.168.0.17/sifer-main/";
    // ↑ Esta URL apunta a: http://tu-ip/sifer/productos.php

    // =============================================
    // ✅ VARIABLE: Instancia única de Retrofit
    // =============================================
    private static Retrofit retrofit = null;

    // =============================================
    // ✅ MÉTODO: Obtener la instancia de Retrofit
    // =============================================
    public static Retrofit getClient() {

        // Si retrofit es null (primera vez que se llama)
        if (retrofit == null) {

            // CONSTRUIR Retrofit paso a paso:
            retrofit = new Retrofit.Builder()
                    .baseUrl(BASE_URL)   // ← Configurar la URL base

                    // ✅ AGREGAR CONVERTIDOR GSON:
                    // Esto le dice a Retrofit: "Usa GSON para convertir JSON → Java"
                    .addConverterFactory(GsonConverterFactory.create())

                    .build();  // ← Terminar la construcción
        }

        // Devolver la instancia de Retrofit (nueva o existente)
        return retrofit;
    }
}