package com.example.siferactivitymain;

public class Producto {
    // =============================================
    // ✅ VARIABLES DE INSTANCIA (atributos)
    // =============================================
    private int idProducto;          // ← IGUAL que en tu BD: idProducto
    private String nombreProducto;   // ← IGUAL que en tu BD: nombreProducto
    private String descripcion;      // ← IGUAL que en tu BD: descripcion
    private int cantidad;            // ← IGUAL que en tu BD: cantidad

    // =============================================
    // ✅ CONSTRUCTOR - se ejecuta al crear "new Producto()"
    // =============================================
    public Producto(int idProducto, String nombreProducto, String descripcion, int cantidad) {
        // "this" se refiere a las variables DE ESTA CLASE (las de arriba ↑)
        this.idProducto = idProducto;        // Asigna el parámetro idProducto al atributo idProducto
        this.nombreProducto = nombreProducto; // Asigna el parámetro nombreProducto al atributo nombreProducto
        this.descripcion = descripcion;      // Asigna el parámetro descripcion al atributo descripcion
        this.cantidad = cantidad;            // Asigna el parámetro cantidad al atributo cantidad
    }

    // =============================================
    // ✅ MÉTODOS GETTER - permiten LEER las variables privadas
    // =============================================

    // Devuelve el id del producto
    public int getIdProducto() {
        return idProducto;   // Retorna el valor de idProducto
    }

    // Devuelve el nombre del producto
    public String getNombreProducto() {
        return nombreProducto; // Retorna el valor de nombreProducto
    }

    // Devuelve la descripción del producto
    public String getDescripcion() {
        return descripcion;   // Retorna el valor de descripcion
    }

    // Devuelve la cantidad en stock
    public int getCantidad() {
        return cantidad;      // Retorna el valor de cantidad
    }
}
