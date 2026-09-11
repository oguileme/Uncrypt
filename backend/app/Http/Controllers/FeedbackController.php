<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Support\AdminAudit;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Listagem admin do feedback: paginada, com filtros por status e tipo.
     */
    public function index(Request $request)
    {
        $perPage = max(5, min((int) $request->integer('per_page', 15), 50));

        $feedbacks = Feedback::query()
            ->with('user:id,name,username,email')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('feedback_type'), fn ($q) => $q->where('feedback_type', $request->string('feedback_type')))
            ->latest()
            ->paginate($perPage);

        return response()->json($feedbacks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'context_url' => 'required|string',
            'feedback_text' => 'required|string',
            'feedback_type' => 'required|in:bug,feature_request,general',
        ]);

        // IDOR: o autor e sempre o usuario autenticado — nunca confia em user_id vindo do client
        $feedback = Feedback::create([
            'user_id' => auth()->id(),
            'context_url' => $data['context_url'],
            'feedback_text' => $data['feedback_text'],
            'feedback_type' => $data['feedback_type'],
        ]);

        return response()->json($feedback, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Feedback $feedback)
    {
        return response()->json($feedback->load('user:id,name,username,email'));
    }

    /**
     * Admin troca o status do feedback; nunca reescreve o conteudo reportado.
     */
    public function update(Request $request, Feedback $feedback)
    {
        $data = $request->validate([
            'status' => 'required|in:new,in_progress,resolved',
        ]);

        $feedback->update($data);

        AdminAudit::record([
            'action' => 'feedback.status_changed',
            'target_type' => 'feedback',
            'target_id' => $feedback->id,
            'changes' => ['status' => $data['status']],
        ]);

        return response()->json($feedback);
    }

    /**
     * Remove the specified resource from storage (spam/bots).
     */
    public function destroy(Feedback $feedback)
    {
        AdminAudit::record([
            'action' => 'feedback.deleted',
            'target_type' => 'feedback',
            'target_id' => $feedback->id,
            'changes' => ['id' => $feedback->id, 'feedback_type' => $feedback->feedback_type],
        ]);

        $feedback->delete();

        return response()->json(null, 204);
    }
}
