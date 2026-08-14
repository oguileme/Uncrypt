<?php

namespace App\Http\Controllers;

use App\Models\Key;
use Illuminate\Http\Request;

class KeyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return response()->json(Key::all());
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
        $data =$request->validate([
            'key' => 'required|string|max:255',
            'difficulty' => 'required|integer',
        ]);
        $key = Key::create($data);
        return response()->json($key, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Key $key)
    {
        //
        return response()->json($key);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Key $key)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Key $key)
    {
        //
        $data =$request->validate([
            'key' => 'required|string|max:255',
            'difficulty' => 'required|integer',
        ]);
        $key->update($data);
        return response()->json($key);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Key $key)
    {
        //
        $key->delete();
        return response()->json(null, 204);
    }
}
