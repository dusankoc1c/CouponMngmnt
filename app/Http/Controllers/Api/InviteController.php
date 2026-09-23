<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InviteRequest;
use App\Mail\InviteMail;
use App\Models\Invite;
use Illuminate\Support\Facades\Mail;

class InviteController extends Controller
{
    public function store(InviteRequest $request)
    {
        $data = $request->validated();

        $invite = Invite::create([
            'email' => $data['email'],
            'value_limit' => $data['value_limit'] ?? null,
        ]);

        $registeredUrl = \URL::temporarySignedRoute(
            'register',
            now()->addDays(7),
            ['invite' => $invite->id]
        );

        Mail::to($invite->email)->send(new InviteMail($registeredUrl));

        return response()->json([
            'message' => 'Invite mail uspesno poslat',
        ]);
    }
}
