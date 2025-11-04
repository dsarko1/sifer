package com.example.siferactivitymain;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.TextView;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;


public class MainActivity extends AppCompatActivity {
    TextView txtregistro;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_main);
        txtregistro = findViewById(R.id.txtregistro);

        txtregistro.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                view.animate()
                        .scaleX(0.95f)  // Se encoge un poco en X
                        .scaleY(0.95f)  // Se encoge un poco en Y
                        .setDuration(100)
                        .withEndAction(new Runnable() {
                            @Override
                            public void run() {
                                // Vuelve al tamaño normal
                                view.animate()
                                        .scaleX(1f)
                                        .scaleY(1f)
                                        .setDuration(100)
                                        .start();

                                Intent intent = new Intent(MainActivity.this, MainActivity2.class);
                                startActivity(intent);
                                finish();
                            }
                        })
                        .start();
            }
        });

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });
    }
}
