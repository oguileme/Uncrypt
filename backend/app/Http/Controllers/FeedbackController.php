<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Feedback::all();
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
            'user_id' => 'required|exists:users,id',
            'context_url' => 'required|string',
            'feedback_text' => 'required|string',
            'feedback_type' => 'required|in:bug,feature_request,general',
        ]);

        $feedback = Feedback::create($data);

        return response()->json($feedback, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Feedback $feedback)
    {
        //
        return response()->json($feedback);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Feedback $feedback)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Feedback $feedback)
    {
        //
        $data = $request->validate([
            'context_url' => 'sometimes|string',
            'feedback_text' => 'sometimes|string',
            'feedback_type' => 'sometimes|in:bug,feature_request,general',
            'status' => 'sometimes|in:new,in_progress,resolved',
        ]);

        $feedback->update($data);
        return response()->json($feedback);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feedback $feedback)
    {
        //
        $feedback->delete();
        return response()->json(null, 204);
    }
}
