<?php

namespace App\Http\Controllers;

use App\Models\TypeEncrypton;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TypeEncryptonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $types = Cache::remember('type-encryption.index', 1800, function () {
            return TypeEncrypton::all();
        });

        return response()->json($types);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);
        $typeEncrypton = TypeEncrypton::create($data);

        Cache::forget('type-encryption.index');
        Cache::forget('challenges.index');

        return response()->json($typeEncrypton, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TypeEncrypton $typeEncrypton)
    {
        //
        return response()->json($typeEncrypton);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeEncrypton $typeEncrypton)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeEncrypton $typeEncrypton)
    {
        //
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:255',
            'difficulty' => 'sometimes|in:easy,medium,hard',
        ]);
        $typeEncrypton->update($data);

        // renomear o tipo invalida o ciphertext materializado dos desafios desse tipo
        if ($typeEncrypton->wasChanged('name')) {
            foreach ($typeEncrypton->challenges()->get() as $challenge) {
                $challenge->computeCiphertext();
                $challenge->saveQuietly();
            }
        }

        Cache::forget('type-encryption.index');
        Cache::forget('challenges.index');

        return response()->json($typeEncrypton);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeEncrypton $typeEncrypton)
    {
        //
        $typeEncrypton->delete();

        Cache::forget('type-encryption.index');
        Cache::forget('challenges.index');

        return response()->json(null, 204);
    }
}
