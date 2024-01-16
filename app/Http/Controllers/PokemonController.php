<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pokemon;
use App\Http\Requests\StorePokemonsRequest;
use Illuminate\Support\Facades\Storage;

class PokemonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pokemones = Pokemon::all();
        return response()->json([
            'status' => true,
            'pokemones' => $pokemones
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePokemonsRequest $request)
    {

        $data = $request->validate([
            'nombre' => 'string',
            'hp' => 'numeric',
            'primera_edicion' => 'string',
            'expansion' => 'string',
            'tipo' => 'string',
            'rareza' => 'string',
            'precio' => 'numeric',
            'imagen' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            $imagenPath = $request->file('imagen')->store('imagenes_pokemones', 'public');
            #dd($imagenPath);
            $data['imagen'] = $imagenPath;
        }

        $pokemon = Pokemon::create($data);

        return response()->json([
            'status' => true,
            'message' => "Pokemon creado exitosamente!",
            'pokemon' => $pokemon
        ], 200);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $pokemon = Pokemon::find($id);

            if ($pokemon) {
                return response()->json($pokemon);
            } else {
                return response()->json(['error' => 'Carta Pokemon no encontrada'], 404);
            }
        } catch (\Exception $e) {
            // En caso de error inesperado, capturar la excepción y devolver una respuesta de error con un código de estado 500 o el código de estado apropiado.
            return response()->json(['error' => 'Error interno del servidor. Ha ocurrido un error.'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        $data = $request->validate([
            'nombre' => 'string',
            'hp' => 'numeric',
            'primera_edicion' => 'string',
            'expansion' => 'string',
            'tipo' => 'string',
            'rareza' => 'string',
            'precio' => 'numeric',
            'imagen' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $pokemon=Pokemon::find($id);

        if ($request->hasFile('imagen')) {
            // Elimina la imagen anterior si existe
            if ($pokemon->imagen) {
                Storage::delete('public/' . $pokemon->imagen);
            }

            $imagenPath = $request->file('imagen')->store('imagenes_pokemones', 'public');
            $data['imagen'] = $imagenPath;
        }

        $pokemon->update($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Pokemon actualizado exitosamente!',
            'pokemon' => $pokemon
        ], 200);

    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pokemon = Pokemon::destroy($id);
        return response()->json([
            'status' => true,
            'message' => 'Pokemon borrado exitosamente!',
            'pokemon' => $pokemon
        ], 200);

    }
}
