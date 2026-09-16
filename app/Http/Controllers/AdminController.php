<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(){
        $admins = User::where('role', 'admin')->get();

        return view('admins.index', ['admins' => $admins]);
    }

    public function destroy(User $user){
        if($user->role === 'superadmin'){
            abort(403, 'Nije moguce obrisati superadmin nalog');
        }

        $user->delete();

        return redirect('/superadmin/admins')->with('success', 'Admin has been deleted');
    }

}
