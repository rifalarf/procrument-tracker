<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TableColumn;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class AdminImportController extends Controller
{
    public function show()
    {
        return view('admin.import.index');
    }

    public function parse(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');
        // storeAs returns "temp/filename.xlsx" (relative to disk root)
        $path = $file->storeAs('temp', 'import_' . now()->timestamp . '.' . $file->getClientOriginalExtension());

        // Use Storage facade to get absolute path.
        // If file system is local, this works.
        $absolutePath = \Illuminate\Support\Facades\Storage::path($path);

        // Read Headers
        $headings = (new HeadingRowImport)->toArray($absolutePath);
        $fileHeaders = $headings[0][0] ?? [];

        // Get DB Columns
        $dbColumns = TableColumn::ordered()->get();

        return view('admin.import.mapping', [
            'file_path' => $path, // Pass relative path to view
            'file_headers' => $fileHeaders,
            'db_columns' => $dbColumns,
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'file_path' => 'required',
            'mapping' => 'required|array',
            'strategy' => 'required|in:skip,update',
        ]);

        $mapping = $request->input('mapping');
        $strategy = $request->input('strategy');
        
        // Pass the relative path given by the view
        $relativePath = $request->input('file_path');
        
        // Ensure we get the absolute path for Maatwebsite
        $absolutePath = \Illuminate\Support\Facades\Storage::path($relativePath);

        try {
            Excel::import(new \App\Imports\AdvancedProcurementImport($mapping, $strategy), $absolutePath);
            
            // Cleanup
            if (\Illuminate\Support\Facades\Storage::exists($relativePath)) {
                \Illuminate\Support\Facades\Storage::delete($relativePath);
            }

            return redirect()->route('dashboard')->with('success', 'Import completed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
