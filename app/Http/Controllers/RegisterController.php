<?php

namespace App\Http\Controllers;

use App\Models\Invite;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $inviteId = $request->query('invite');
        $invite = Invite::findOrFail($inviteId);

        if($invite->used_at != null){
            abort(403, 'Invite je iskoriscen');
        }

        return view('auth.register', ['invite'=>$invite]);
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
    public function store(RegisterRequest $request)
    {
        $invite = Invite::findOrFail($request->invite_id);

        $validatedData = $request->validated();

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $invite->email,
            'password' => Hash::make($validatedData['password']),
            'role' => 'admin'
        ]);

        $invite->used_at = now();
        $invite->save();

        Auth::login($user);

        return redirect('/dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
