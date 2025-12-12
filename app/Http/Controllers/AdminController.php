<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = \App\Models\User::all();
        $bagians = \App\Enums\BagianEnum::cases();
        return view('admin.users.index', compact('users', 'bagians'));
    }

    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email|unique:users']);
        
        \App\Models\User::create([
            'email' => $request->email,
            'name' => 'New User', // Placeholder until login
            'password' => bcrypt('password'), // Dummy password
            'role' => $request->role ?? 'user',
            'bagian_access' => $request->bagian_access ? (is_array($request->bagian_access) ? $request->bagian_access : [$request->bagian_access]) : null,
        ]);

        return back()->with('success', 'User added to whitelist.');
    }

    public function edit($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $bagians = \App\Enums\BagianEnum::cases();
        return view('admin.users.edit', compact('user', 'bagians'));
    }

    public function update(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);
        $request->validate([
            'role' => 'required|in:admin,user',
            'bagian_access' => 'nullable|array',
            'password' => 'nullable|min:6', // Optional password update
        ]);

        $data = [
            'role' => $request->role,
            'bagian_access' => $request->bagian_access,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }



    public function destroy($id)
    {
        \App\Models\User::findOrFail($id)->delete();
        return back()->with('success', 'User removed.');
    }
}
