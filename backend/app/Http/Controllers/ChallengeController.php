<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\Phrase;
use Illuminate\Http\Request;
use Throwable;

class ChallengeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return response()->json(Challenge::all());
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'type_encryption_id' => 'required|integer| exists:type_encryption,id',
            'phrase' => 'required|string|max:255',
            'key' => 'sometimes|string|max:255',
            'xp' => 'required|integer',
        ]);
        $challenge = Challenge::create($data);
        return response()->json($challenge, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Challenge $challenge)
    {
        //
        return response()->json($challenge);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Challenge $challenge)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Challenge $challenge)
    {
        //
        $data =$request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:255',
            'type_encryption_id' => 'sometimes|integer| exists:type_encryption,id',
            'phrase' => 'sometimes|string|max:255',
            'key' => 'sometimes|string|max:255',
            'xp' => 'sometimes|integer',
        ]);
        $challenge->update($data);
        return response()->json($challenge);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Challenge $challenge)
    {
        //
        $challenge->delete();
        return response()->json(null, 204);
    }


}
