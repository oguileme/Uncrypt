<?php

namespace App\Http\Controllers;

use App\Models\TypeEncrypton;
use Illuminate\Http\Request;

class TypeEncryptonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return response()->json(TypeEncrypton::all());
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
        return response()->json($typeEncrypton);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeEncrypton $typeEncrypton)
    {
        //
        $typeEncrypton->delete();
        return response()->json(null, 204);
    }
}
