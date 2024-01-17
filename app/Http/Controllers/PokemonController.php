<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pokemon;
use App\Http\Requests\StorePokemonsRequest;
use Illuminate\Support\Facades\Storage;
    /**
    * @OA\Info(title="API Cartas de Pokemon", version="1.0")
    *
    * @OA\Server(url="http://localhost:8000/api/")
    */

class PokemonController extends Controller
{
    /**
    * @OA\Get(
    *     path="/pokemons",
    *     summary="Mostrar todas las cartas de Pokemon",
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todas las cartas de Pokemon."
    *     ),
    *     @OA\Response(
    *         response="default",
    *         description="Ha ocurrido un error."
    *     )
    * )
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
 * @OA\Post(
 *     path="/api/pokemones",
 *     summary="Crear una nueva carta Pokémon",
 *     description="Crea una nueva carta Pokémon con los detalles proporcionados.",
 *     operationId="createPokemon",
 *     @OA\RequestBody(
 *         required=true,
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Carta Pokémon creada exitosamente",
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Error de validación",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(property="errors", type="object", example={"nombre": {"El campo nombre es obligatorio."}})
 *         )
 *     )
 * )
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
 * @OA\Get(
 *     path="/pokemones/{id}",
 *     summary="Obtener detalles de un Pokémon",
 *     description="Devuelve los detalles de un Pokémon específico.",
 *     operationId="getPokemonById",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID del Pokémon",
 *         required=true,
 *         @OA\Schema(type="integer", format="int64")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Detalles del Pokémon"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Pokémon no encontrado",
 *     )
 * )
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
 * @OA\Post(
 *     path="/pokemones/{id}",
 *     summary="Actualizar una carta",
 *     description="Mediante el ID se busca y cargan los datos pasados en el body en formato json.",
 *     operationId="getPokemonById",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID del Pokémon",
 *         required=true,
 *         @OA\Schema(type="integer", format="int64")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Detalles del Pokémon"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Pokémon no encontrado",
 *     )
 * )
 */
    public function update(Request $request,$id)
    {
        $data = $request->validate([
            'nombre' => 'string',
            'hp' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($value % 10 !== 0) {
                        $fail($attribute.' debe ser un múltiplo de 10.');
                    }
                },
            ],
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

        $pokemon->update($data);
        return response()->json([
            'status' => true,
            'message' => 'Pokemon actualizado exitosamente!',
            'pokemon' => $pokemon
        ], 200);

    }
    /**
    * @OA\Del(
    *     path="/pokemons/{id}",
    *     summary="Borrar una carta en particular",
    *     @OA\Response(
    *         response=200,
    *         description="Borrar una carta en particular mediante un ID pasado en la URL
    *                      Se utiliza método DEL, se envía en la url el ID de la carta deseada y la API devuelve mensaje como el siguiente: 'Pokemon borrado exitosamente!'"
    *     ),
    *     @OA\Response(
    *         response="default",
    *         description="Ha ocurrido un error."
    *     )
    * )
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
