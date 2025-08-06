<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;




use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class MessageController extends Controller
{
    protected MessageService $service;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    

    public function __construct(MessageService $service)
    {
        $this->service = $service;
    }

    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string'
        ]);

        $data['sender_id'] = Auth::id();

        $message = $this->service->sendMessage($data);

        return response()->json($message);
    }

    public function conversation($userId): JsonResponse
    {
        $authId = Auth::id();
        $messages = $this->service->getConversation($authId, $userId);

        return response()->json($messages);
    }
}
