<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TableColumn;
use Illuminate\Http\Request;

class ColumnSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all columns ordered by 'order'
        $columns = TableColumn::orderBy('order')->get();
        return view('admin.columns.index', compact('columns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.columns.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:table_columns,key|regex:/^[a-z0-9_]+$/',
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,date,number,select',
            'order' => 'required|integer',
            'is_visible' => 'boolean',
        ]);

        $validated['is_dynamic'] = true; // Created columns are dynamic "extra" columns
        $validated['is_visible'] = $request->has('is_visible'); // checkbox handling

        TableColumn::create($validated);

        return redirect()->route('admin.columns.index')->with('success', 'Column created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TableColumn $column)
    {
        return view('admin.columns.edit', compact('column'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TableColumn $column)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'order' => 'required|integer',
            'is_visible' => 'boolean',
            // Key and Type are generally immutable for simplicity to avoid data loss/mapping issues for now
        ]);

        $validated['is_visible'] = $request->has('is_visible');

        $column->update($validated);

        return redirect()->route('admin.columns.index')->with('success', 'Column updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TableColumn $column)
    {
        // Prevent deleting core system columns if necessary, but table schema has no flag.
        // We'll rely on user discretion or check is_dynamic if we want to protect core fields.
        
        $column->delete();

        return redirect()->route('admin.columns.index')->with('success', 'Column deleted successfully.');
    }
}
