@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Create New Procurement Item</h1>

    <form action="{{ route('procurement.store') }}" method="POST" class="space-y-4">
        @csrf

        <!-- Row 1: Identification -->
        <h3 class="text-lg font-semibold text-gray-700 border-b pb-1">Identification</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">External ID</label>
                <input type="text" name="external_id" class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Material Code *</label>
                <input type="text" name="mat_code" required class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Nama Barang *</label>
                <input type="text" name="nama_barang" required class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
            </div>
        </div>

        <!-- Row 2: Specs & Value -->
        <h3 class="text-lg font-semibold text-gray-700 border-b pb-1 mt-4">Specs & Value</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Qty *</label>
                <input type="number" step="any" name="qty" required class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">UoM *</label>
                <input type="text" name="um" required class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Nilai (Budget/Value)</label>
                <input type="number" step="0.01" name="nilai" class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
            </div>
        </div>

        <!-- Row 3: Request Info -->
        <h3 class="text-lg font-semibold text-gray-700 border-b pb-1 mt-4">Requester Info</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">PG</label>
                <input type="text" name="pg" class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Bagian</label>
                <input type="text" name="bagian" class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
            </div>
             <div>
                <label class="block text-sm font-medium text-gray-700">User Requester</label>
                <input type="text" name="user_requester" class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
            </div>
             <div>
                <label class="block text-sm font-medium text-gray-700">Tgl Terima Dok.</label>
                <input type="date" name="tanggal_terima_dokumen" class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
            </div>
        </div>

        <!-- Row 4: Procurement Info -->
        <h3 class="text-lg font-semibold text-gray-700 border-b pb-1 mt-4">Procurement Status</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Proc Type</label>
                <input type="text" name="proc_type" class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Buyer</label>
                 <select name="buyer" class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
                    <option value="">Select Buyer</option>
                    @foreach(\App\Enums\BuyerEnum::cases() as $buyer)
                        <option value="{{ $buyer->value }}">{{ $buyer->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                 <label class="block text-sm font-medium text-gray-700">Status *</label>
                 <select name="status" class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
                    @foreach(\App\Enums\ProcurementStatusEnum::cases() as $s)
                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tgl Status</label>
                <input type="date" name="tanggal_status" class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Emergency Note</label>
                <input type="text" name="emergency" class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
            </div>
        </div>

        <!-- Row 5: Vendor & PO -->
        <h3 class="text-lg font-semibold text-gray-700 border-b pb-1 mt-4">Vendor & PO</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
             <div>
                <label class="block text-sm font-medium text-gray-700">Vendor Name</label>
                <input type="text" name="nama_vendor" class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">No PO</label>
                <input type="text" name="no_po" class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tgl PO</label>
                <input type="date" name="tanggal_po" class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
            </div>
             <div>
                <label class="block text-sm font-medium text-gray-700">Tgl Datang</label>
                <input type="date" name="tanggal_datang" class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2">
            </div>
        </div>

        <!-- Row 6: Remarks -->
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">Keterangan</label>
            <textarea name="keterangan" rows="3" class="mt-1 block w-full rounded border-gray-300 shadow-sm border p-2"></textarea>
        </div>

        <div class="flex justify-end pt-4">
            <a href="{{ route('dashboard') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded mr-2 hover:bg-gray-300">Cancel</a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">Create Item</button>
        </div>
    </form>
</div>
@endsection
