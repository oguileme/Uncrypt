<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Http\Request;


class AchievementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return response()->json(Achievement::all());
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
            'xp_reward' => 'required|integer',
            'required_count' => 'required|integer',
        ]);

        $achievement = Achievement::create($data);

        return response()->json($achievement, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Achievement $achievement)
    {
        //
        return response()->json($achievement);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Achievement $achievement)
    {
        //
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Achievement $achievement)
    {
        //
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:255',
            'xp_reward' => 'sometimes|integer',
            'required_count' => 'sometimes|integer',
        ]);

        $achievement->update($data);
        return response()->json($achievement);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Achievement $achievement)
    {
        //
        $achievement->delete();
        return response()->json(null, 204);
    }
}
