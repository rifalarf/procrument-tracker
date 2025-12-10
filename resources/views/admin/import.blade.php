@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-800">Admin: Import Data</h1>
    
    <div class="bg-white p-6 rounded shadow max-w-lg">
        <form action="{{ route('admin.import.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Choose Excel File (.xlsx, .xls)</label>
                <input type="file" name="file" required class="mt-1 block w-full text-sm text-gray-500
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-full file:border-0
                  file:text-sm file:font-semibold
                  file:bg-blue-50 file:text-blue-700
                  hover:file:bg-blue-100
                ">
            </div>

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700">Import</button>
        </form>
        
        <div class="mt-6 text-sm text-gray-500">
            <p>Supported columns: mat_code, nama_barang, qty, pg, user, nilai, bagian, status, buyer.</p>
        </div>
    </div>
</div>
@endsection
