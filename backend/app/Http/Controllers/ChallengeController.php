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
            'phrase_id' => 'required|integer| exists:phrases,id',
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'type_encryption_id' => 'required|integer| exists:type_encryption,id',
            'phrase_id' => 'required|integer| exists:phrases,id',
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

    //esse metodo esta errado, altere quando puder
    public function attemptChallenge(Challenge $challenge, Request $request){
        $attempt = $request->validate([
            'attempt' => 'required|string'
        ]);

        try{
            $phrase = Phrase::find($challenge->phrase_id);

            $pivot = $challenge->users()
                ->where('user_id', auth()->id())
                ->firstOrFail()
                ->pivot;

            if($phrase->text == $attempt['attempt']){
                $pivot->increment('attempts');
                $pivot->update(['is_complete' => true]);
                return response()->json(['message' => 'certa resposta'], 200);
            }else{
                $pivot->increment('attempts');
                return response()->json(['message' => 'resposta errada'], 200);
            }
        }catch(\Throwable $th){
            return response()->json(['erro' => $th->getMessage()], 500);
        }
    }
}
