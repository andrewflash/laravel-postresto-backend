<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // function index
    public function index(Request $request)
    {
        // get all users with pagination from DB table users
        $users = DB::table('users')
            ->when($request->name, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->name}%")
                    ->orWhere('email', 'like', "%{$request->name}%");
            })
            ->paginate(10);
        return view('pages.users.index', compact('users'));
    }

    // function create
    public function create()
    {
        return view('pages.users.create');
    }

    // function store
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:USER,ADMIN,STAFF',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->save();

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    // function show
    public function show($id)
    {
        // $user = User::find($id);
        return view('pages.users.show', compact('user'));
    }

    // function edit
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('pages.users.edit', compact('user'));
    }

    // function update
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'role' => 'required|in:USER,ADMIN,STAFF',
        ]);

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();

        //if password is not empty
        if ($request->password) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    // function destroy
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
