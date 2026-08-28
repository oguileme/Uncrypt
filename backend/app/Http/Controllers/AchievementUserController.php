<?php

namespace App\Http\Controllers;

use App\Models\AchievementUser;
use Illuminate\Http\Request;

class AchievementUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return response()->json(AchievementUser::all());
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
            'user_id' => 'required|integer',
            'achievement_id' => 'required|integer',
        ]);

        $achievementUser = AchievementUser::create($data);
        return response()->json($achievementUser,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AchievementUser $achievementUser)
    {
        //
        return response()->json($achievementUser);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AchievementUser $achievementUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AchievementUser $achievementUser)
    {
        //
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AchievementUser $achievementUser)
    {
        //
        $achievementUser->delete();
        return response()->json(null,204);
    }
}
