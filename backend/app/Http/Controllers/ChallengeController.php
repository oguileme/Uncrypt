<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ChallengeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $challenges = Cache::remember('challenges.index', 1800, function () {
            return Challenge::with('typeEncryption')
                ->get()
                ->map(fn (Challenge $c) => $c->withCiphertext())
                ->makeHidden('phrase')
                ->values()
                ->toArray();
        });

        return response()->json($challenges);
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'type_encryption_id' => 'required|integer| exists:type_encryption,id',
            'phrase' => 'required|string|max:255',
            'key' => 'sometimes|string|max:255',
            'xp' => 'required|integer',
            'hint' => 'required|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);
        $challenge = Challenge::create($data);

        AdminAudit::record([
            'action' => 'challenge.created',
            'target_type' => 'challenge',
            'target_id' => $challenge->id,
            'changes' => array_diff_key($data, array_flip(['phrase', 'key'])),
        ]);

        Cache::forget('challenges.index');

        return response()->json($challenge, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Challenge $challenge)
    {
        //
        $data = Cache::remember("challenges.{$challenge->id}", 1800, function () use ($challenge) {
            return $challenge->withCiphertext()->makeHidden('phrase')->toArray();
        });

        return response()->json($data);
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
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:255',
            'type_encryption_id' => 'sometimes|integer| exists:type_encryption,id',
            'phrase' => 'sometimes|string|max:255',
            'key' => 'sometimes|string|max:255',
            'xp' => 'sometimes|integer',
            'hint' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);
        $challenge->update($data);

        // nunca registra frase/chave no log de auditoria (material sensivel)
        AdminAudit::record([
            'action' => 'challenge.updated',
            'target_type' => 'challenge',
            'target_id' => $challenge->id,
            'changes' => array_diff_key($challenge->getChanges(), array_flip(['phrase', 'key'])),
        ]);

        Cache::forget('challenges.index');
        Cache::forget("challenges.{$challenge->id}");

        return response()->json($challenge);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Challenge $challenge)
    {
        //
        AdminAudit::record([
            'action' => 'challenge.deleted',
            'target_type' => 'challenge',
            'target_id' => $challenge->id,
            'changes' => ['title' => $challenge->title],
        ]);

        $challenge->delete();

        Cache::forget('challenges.index');
        Cache::forget("challenges.{$challenge->id}");

        return response()->json(null, 204);
    }

    public function getChallengeRecommendations()
    {
        $user = auth()->user();

        $completedChallenges = $user->challenges()
            ->wherePivot('completed', true)
            ->pluck('challenge.id')
            ->toArray();

        $allChallenges = Cache::remember('challenges.index', 1800, function () {
            return Challenge::with('typeEncryption')
                ->get()
                ->map(fn (Challenge $c) => $c->withCiphertext())
                ->makeHidden('phrase')
                ->values()
                ->toArray();
        });

        $recommended = collect($allChallenges)
            ->filter(fn (array $challenge) => ! in_array($challenge['id'], $completedChallenges))
            ->values();

        return response()->json($recommended);
    }
}
