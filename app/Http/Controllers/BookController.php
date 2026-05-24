<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Book;

class BookController extends Controller
{
    /**
     * Listar todos los libros con su categoría asociada.
     */
    public function index()
    {
        $books = Book::with('category')->latest()->get();
        return response()->json($books);
    }

    /**
     * Registrar un nuevo libro con su imagen de portada.
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada con mensajes en español
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'editorial' => 'required|string|max:255',
            'edicion' => 'required|string|max:255',
            'fecha_lanzamiento' => 'required|date',
            'categoria_id' => 'required|exists:categories,id',
            'imagen_portada' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'nombre.required' => 'El título del libro es obligatorio.',
            'autor.required' => 'El autor del libro es obligatorio.',
            'editorial.required' => 'La editorial es obligatoria.',
            'edicion.required' => 'La edición del libro es obligatoria.',
            'fecha_lanzamiento.required' => 'La fecha de lanzamiento es obligatoria.',
            'fecha_lanzamiento.date' => 'La fecha de lanzamiento no tiene un formato válido.',
            'categoria_id.required' => 'Debes seleccionar una categoría.',
            'categoria_id.exists' => 'La categoría seleccionada no es válida.',
            'imagen_portada.required' => 'La imagen de portada es obligatoria.',
            'imagen_portada.image' => 'El archivo debe ser una imagen.',
            'imagen_portada.mimes' => 'La imagen debe estar en formato: jpeg, png, jpg, gif o webp.',
            'imagen_portada.max' => 'La imagen no debe pesar más de 2MB.',
        ]);

        // Procesar y almacenar la imagen de portada
        if ($request->hasFile('imagen_portada')) {
            $path = $request->file('imagen_portada')->store('portadas', 'public');
            $validatedData['imagen_portada'] = $path;
        }

        // Crear el registro de libro
        $book = Book::create($validatedData);

        // Retornar respuesta exitosa con el libro creado y su relación
        return response()->json([
            'success' => true,
            'message' => '¡Libro registrado con éxito!',
            'book' => $book->load('category')
        ], 201); // 201 Created status
    }
}
