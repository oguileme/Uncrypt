<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChallengeUser;
use App\Helpers\CipherHelper;

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
            'challenge_id' => 'required|integer|exists:challenge,id',
        ]);

        // o usuario vem sempre do token; firstOrCreate evita duplicar o registro ao reiniciar um desafio
        $challengeUser = ChallengeUser::firstOrCreate(
            ['user_id' => auth()->id(), 'challenge_id' => $data['challenge_id']],
            ['completed' => false, 'attempts' => 0]
        );

        // garante que os valores default do banco estejam presentes na resposta
        if ($challengeUser->wasRecentlyCreated) {
            $challengeUser = $challengeUser->fresh();
        }

        return response()->json($this->withCiphertext($challengeUser), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ChallengeUser $challengeUser)
    {
        //
        return response()->json($this->withCiphertext($challengeUser), 200);
    }

    private function withCiphertext(ChallengeUser $challengeUser): ChallengeUser
    {
        $challenge = $challengeUser->challenge->load('typeEncryption');
        $challenge->ciphertext = CipherHelper::encryptByTypeName(
            $challenge->typeEncryption->name,
            $challenge->phrase,
            $challenge->key
        );

        return $challengeUser->setRelation('challenge', $challenge->makeHidden('phrase'));
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
            'hint_used' => 'sometimes|boolean',
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

    public function attempt(Request $request, ChallengeUser $challengeUser){
        if(auth()->id() !== $challengeUser->user_id){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'attempt' => 'required|string|max:255',
        ]);

        if($challengeUser->completed){
            return response()->json(['message' => 'Challenge already completed.', 'completed' => true], 200);
        }

        $challengeUser->update(['attempts' => $challengeUser->attempts + 1]);

        // comparacao normalizada: ignora espacos extras e diferenca de maiusculas/minisculas
        $normalizedAttempt = mb_strtolower(trim($data['attempt']));
        $expected = mb_strtolower(trim($challengeUser->challenge->phrase));

        if($normalizedAttempt === $expected){
            // abs + cast: diffInSeconds pode vir negativo/decimal e a coluna e integer
            $timeTaken = (int) abs(now()->diffInSeconds($challengeUser->created_at));
            $challengeUser->update(['completed' => true, 'time_taken' => $timeTaken]);
            $xpGained = $this->awardXp($challengeUser->challenge->xp);

            return response()->json([
                'message' => 'Challenge completed!',
                'completed' => true,
                'xp_gained' => $xpGained,
                'time_taken' => $timeTaken,
                'challenge_user' => $this->withCiphertext($challengeUser->fresh()),
            ], 200);
        }

        return response()->json(['message' => 'Incorrect attempt.', 'completed' => false], 400);
    }

    // soma o xp no usuario com level up automatico ao atingir o limite
    private function awardXp(int $xp): int{
        $user = auth()->user();
        $user->xp_progress += $xp;

        while($user->xp_progress >= $user->xp_levelup){
            $user->xp_progress -= $user->xp_levelup;
            $user->level += 1;
        }

        $user->save();
        return $xp;
    }


}
