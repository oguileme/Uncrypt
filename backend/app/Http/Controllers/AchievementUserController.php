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
        return response()->json(AchievementUser::where('user_id', auth()->id())->get());
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
            'achievement_id' => 'required|integer',
        ]);

        $achievementUser = AchievementUser::create([
            'user_id' => auth()->id(),
            'achievement_id' => $data['achievement_id'],
        ]);
        return response()->json($achievementUser,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AchievementUser $achievementUser)
    {
        //
        if (auth()->id() !== $achievementUser->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

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
        if (auth()->id() !== $achievementUser->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'progress' => 'sometimes|nullable|integer|min:0',
            'is_completed' => 'sometimes|boolean',
        ]);

        $achievementUser->update($data);

        return response()->json($achievementUser);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AchievementUser $achievementUser)
    {
        //
        if (auth()->id() !== $achievementUser->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $achievementUser->delete();
        return response()->json(null,204);
    }
}
