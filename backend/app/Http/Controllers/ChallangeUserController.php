<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChallengeUser;

class ChallangeUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return response()->json(ChallengeUser::all(), 200);
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
            'challenge_id' => 'required|integer|exists:challenges,id',
            'user_id' => 'required|integer|exists:users,id',    
        ]);

        return response()->json(ChallengeUser::create($data), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ChallengeUser $challengeUser)
    {
        //
        return response()->json($challengeUser, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChallengeUser $challengeUser)
    {
        //
        $data = $request->validate([
            'challenge_id' => 'sometimes|integer|exists:challenges,id',
            'user_id' => 'sometimes|integer|exists:users,id',    
        ]);

        $challengeUser->update($data);
        return response()->json($challengeUser, 200);
    }
 

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChallengeUser $challengeUser)
    {
        //
        $challengeUser->delete();
        return response()->json(null, 204);
    }

    public function attemptChallenge(Request $request, ChallengeUser $challengeUser){
        if(auth()->id() !== $challengeUser->user_id){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'attempt' => 'required|string|max:255', 
        ]);

        if($data['attempt'] === $challengeUser->challenge->phrase){
            $challengeUser->update(['completed' => true, 'time_taken' => now()->diffInSeconds($challengeUser->created_at), 'attenpts' => $challengeUser->attempts + 1]);
            return response()->json(['message' => 'Challenge completed!'], 200);
        }

        $challengeUser->update(['attempts' => $challengeUser->attempts + 1]);
        return response()->json(['message' => 'Incorrect attempt.'], 400);

    }


}
