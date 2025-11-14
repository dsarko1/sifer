package com.example.siferactivitymain;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import java.util.List;

public class ProductoAdapter extends RecyclerView.Adapter<ProductoAdapter.ProductoViewHolder> {

    // Lista que contiene TODOS los productos a mostrar
    private List<Producto> listaProductos;

    // CONSTRUCTOR - recibe la lista de productos
    public ProductoAdapter(List<Producto> listaProductos) {
        this.listaProductos = listaProductos;  // Guarda la lista recibida
    }

    //MÉTODO 1: Crear nuevas vistas (se llama para crear cada "cuadradito")
    @NonNull
    @Override
    public ProductoViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        // Convierte el XML item_producto.xml en una View real de Android
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.items_recyclerview, parent, false);
        // Crea un nuevo ViewHolder con esa vista
        return new ProductoViewHolder(view);
    }

    // ✅ MÉTODO 2: Vincular datos a las vistas (se llama para CADA item)
    @Override
    public void onBindViewHolder(@NonNull ProductoViewHolder holder, int position) {
        Producto producto = listaProductos.get(position);

        // ✅ USAR LOS NUEVOS NOMBRES (de tu BD real)
        holder.txtNombre.setText(producto.getNombreProducto());  // ← nombreProducto (no nombre)
        holder.txtStock.setText("Stock: " + producto.getCantidad()); // ← cantidad (no stock)

        // Si tienes descripción, puedes agregar:
        // holder.txtDescripcion.setText(producto.getDescripcion());

        holder.itemView.setOnClickListener(v -> {
            Toast.makeText(v.getContext(),
                    "Clickeaste: " + producto.getNombreProducto(),
                    Toast.LENGTH_SHORT).show();
        });
    }

    // ✅ MÉTODO 3: Decir cuántos items hay en total
    @Override
    public int getItemCount() {
        return listaProductos.size();  // Devuelve el tamaño de la lista
    }

    // ✅ CLASE INTERNA: ViewHolder - representa CADA "cuadradito" de producto
    public static class ProductoViewHolder extends RecyclerView.ViewHolder {
        // Variables para cada elemento del layout item_producto.xml
        ImageView imgProducto;
        TextView txtNombre, txtStock;

        // CONSTRUCTOR del ViewHolder
        public ProductoViewHolder(@NonNull View itemView) {
            super(itemView);
            // Conecta las variables Java con los elementos XML
            imgProducto = itemView.findViewById(R.id.imgProducto);
            txtNombre = itemView.findViewById(R.id.txtNombre);
            txtStock = itemView.findViewById(R.id.txtStock);
        }
    }
}