<?php

namespace App\Http\Controllers;

use App\Models\Phrase;
use Illuminate\Http\Request;

class PhraseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $phrases = Phrase::all();
        return response()->json($phrases);
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
        $data  =$request->validate([
            'phrase' => 'required|string|max:255',
        ]);
        $phrase = Phrase::create($data);
        return response()->json($phrase, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Phrase $phrase)
    {
        //
        return response()->json($phrase);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Phrase $phrase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Phrase $phrase)
    {
        //
        $data  =$request->validate([
            'phrase' => 'required|string|max:255',
        ]);
        $phrase->update($data);
        return response()->json($phrase);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Phrase $phrase)
    {
        //
        $phrase->delete();
        return response()->json(null, 204);
    }
}
