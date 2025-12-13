<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TableColumn;
use App\Models\ImportProgress;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Facades\Storage;

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
        $originalName = $file->getClientOriginalName();
        $path = $file->storeAs('temp', 'import_' . now()->timestamp . '.' . $file->getClientOriginalExtension());

        $absolutePath = Storage::path($path);

        // Read Headers
        $headings = (new HeadingRowImport)->toArray($absolutePath);
        $fileHeaders = $headings[0][0] ?? [];

        // Count total rows (excluding header)
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($absolutePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $totalRows = $worksheet->getHighestRow() - 1; // Minus header row

        // Get DB Columns
        $dbColumns = TableColumn::ordered()->get();

        return view('admin.import.mapping', [
            'file_path' => $path,
            'file_name' => $originalName,
            'file_headers' => $fileHeaders,
            'db_columns' => $dbColumns,
            'total_rows' => $totalRows,
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'file_path' => 'required',
            'mapping' => 'required|array',
            'strategy' => 'required|in:skip,update',
            'total_rows' => 'required|integer',
        ]);

        $mapping = $request->input('mapping');
        $strategy = $request->input('strategy');
        $relativePath = $request->input('file_path');
        $totalRows = $request->input('total_rows');
        $fileName = $request->input('file_name', 'Unknown');

        // Create progress record
        $progress = ImportProgress::create([
            'user_email' => auth()->user()->email,
            'file_name' => $fileName,
            'total_rows' => $totalRows,
            'processed_rows' => 0,
            'success_count' => 0,
            'failed_count' => 0,
            'status' => 'pending',
        ]);
        
        // Dispatch to queue for background processing
        \App\Jobs\ProcessLargeImport::dispatch(
            $relativePath,
            $mapping,
            $strategy,
            auth()->user()->email,
            $progress->id
        );

        // Redirect to progress page
        return redirect()->route('admin.import.progress', $progress->id);
    }

    /**
     * Show import progress page
     */
    public function progress($id)
    {
        $progress = ImportProgress::findOrFail($id);
        
        // Only allow owner to view progress
        if ($progress->user_email !== auth()->user()->email && auth()->user()->role !== 'admin') {
            abort(403);
        }

        return view('admin.import.progress', [
            'progress' => $progress,
        ]);
    }

    /**
     * API endpoint to get progress status
     */
    public function progressStatus($id)
    {
        $progress = ImportProgress::findOrFail($id);
        
        // Only allow owner to view progress
        if ($progress->user_email !== auth()->user()->email && auth()->user()->role !== 'admin') {
            abort(403);
        }

        return response()->json([
            'id' => $progress->id,
            'status' => $progress->status,
            'total_rows' => $progress->total_rows,
            'processed_rows' => $progress->processed_rows,
            'success_count' => $progress->success_count,
            'failed_count' => $progress->failed_count,
            'progress_percentage' => $progress->progress_percentage,
            'error_message' => $progress->error_message,
        ]);
    }
}
