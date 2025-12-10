<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = \App\Models\User::all();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email|unique:users']);
        
        \App\Models\User::create([
            'email' => $request->email,
            'name' => 'New User', // Placeholder until login
            'password' => bcrypt('password'), // Dummy password
            'role' => $request->role ?? 'user',
        ]);

        return back()->with('success', 'User added to whitelist.');
    }

    public function showImportForm()
    {
        return view('admin.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ProcurementImport, $request->file('file'));

        return back()->with('success', 'Data imported successfully.');
    }

    public function destroy($id)
    {
        \App\Models\User::findOrFail($id)->delete();
        return back()->with('success', 'User removed.');
    }
}
