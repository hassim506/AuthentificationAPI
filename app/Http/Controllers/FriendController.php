<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    public function sendRequest($friendId)
    {
        $user = Auth::user();

        // Vérifie si une demande d'amitié existe déjà
        if (Friend::where('user_id', $user->id)->where('friend_id', $friendId)->exists()) {
            return response()->json(['message' => 'Request already sent']);
        }

        // Crée la relation d'amitié avec le statut "pending"
        Friend::create([
            'user_id' => $user->id,
            'friend_id' => $friendId,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Friend request sent']);
    }

    public function acceptRequest($friendId)
    {
    $user = Auth::user();

    // Trouver la demande d'amitié en attente
    $friendRequest = Friend::where('friend_id', $user->id)
            ->where('user_id', $friendId)
               ->where('status', 'pending')
               ->first();

        if ($friendRequest) {
            // Mettre à jour le statut en "accepted"
            $friendRequest->status = 'accepted';
            $friendRequest->save();

            return response()->json(['message' => 'Friend request accepted']);
        }

        return response()->json(['message' => 'Friend request not found']);
    }
}
