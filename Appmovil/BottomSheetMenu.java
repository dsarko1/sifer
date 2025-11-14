package com.example.siferactivitymain;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.FrameLayout;
import android.widget.Toast;

import com.google.android.material.bottomsheet.BottomSheetBehavior;
import com.google.android.material.bottomsheet.BottomSheetDialog;

public class BottomSheetMenu {
    private final Context context;
    private BottomSheetDialog bottomSheetDialog;

    public BottomSheetMenu(Context context) {
        this.context = context;

    }
    public void showMenu(){
        bottomSheetDialog = new BottomSheetDialog(context);
        View view = LayoutInflater.from(context).inflate(R.layout.bottomsheet,null);
        bottomSheetDialog.setContentView(view);

        BottomSheetBehavior<FrameLayout> behavior = BottomSheetBehavior.from((FrameLayout)view.getParent());
        behavior.setState(BottomSheetBehavior.STATE_EXPANDED);


        view.findViewById(R.id.opusuario).setOnClickListener(v ->{
            Toast.makeText(context,"Perfil de usuario", Toast.LENGTH_SHORT).show();
            bottomSheetDialog.dismiss();
        } );
        view.findViewById(R.id.option_buscar).setOnClickListener(v ->{
            Toast.makeText(context,"Buscar productos", Toast.LENGTH_SHORT).show();
            bottomSheetDialog.dismiss();
        } );
        view.findViewById(R.id.option_stock).setOnClickListener(v ->{
            Toast.makeText(context,"Stock", Toast.LENGTH_SHORT).show();
            bottomSheetDialog.dismiss();
        } );
        view.findViewById(R.id.option_cerrar_sesion).setOnClickListener(v ->{
          cerrarSesion();
          bottomSheetDialog.dismiss();
        } );

        bottomSheetDialog.show();



    }
    private void cerrarSesion(){
        Intent intent = new Intent(context, MainActivity.class);

        intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP| Intent.FLAG_ACTIVITY_NEW_TASK);
        context.startActivity(intent);

        if(context instanceof Activity) {
            ((Activity) context). finish();
        }
        Toast.makeText(context, "Sesion cerrada", Toast.LENGTH_SHORT).show();
    }
}
