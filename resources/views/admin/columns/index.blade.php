@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Manage Table Columns</h1>
        <a href="{{ route('admin.columns.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Add New Column</a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Label</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Key/Field</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visible</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="sortable-columns" class="bg-white divide-y divide-gray-200">
                @foreach($columns as $column)
                <tr data-id="{{ $column->id }}" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 cursor-move handle">
                        <div class="flex items-center space-x-2">
                             <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                             <!-- <span>{{ $column->order }}</span> -->
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $column->label }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $column->key }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $column->type }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($column->is_visible)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Yes</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">No</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex space-x-2">
                        <a href="{{ route('admin.columns.edit', $column->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                        @if($column->is_dynamic)
                            <form action="{{ route('admin.columns.destroy', $column->id) }}" method="POST" id="delete-form-{{ $column->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" 
                                    @click="$dispatch('open-confirm-modal', {
                                        title: 'Delete Column',
                                        message: 'Are you sure you want to delete this column?',
                                        confirmText: 'Delete',
                                        cancelText: 'Cancel',
                                        onConfirm: () => document.getElementById('delete-form-{{ $column->id }}').submit()
                                    })"
                                    class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var el = document.getElementById('sortable-columns');
        var sortable = Sortable.create(el, {
            handle: '.handle',
            animation: 150,
            onEnd: function (evt) {
                var order = [];
                el.querySelectorAll('tr[data-id]').forEach(function(row) {
                    order.push(row.getAttribute('data-id'));
                });

                // Since the ID is the primary key for columns table, we use a new route or the existing generic reorder route?
                // The existing 'admin.columns.reorder' expects 'order' array of keys (data-id was key in main table).
                // Wait, in main table data-id was the ID.
                // Let's check ColumnController@reorder implementation.
                
                fetch('{{ route('admin.columns.reorder') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order: order }) // Sends Array of IDs
                }).then(response => {
                    if(response.ok) {
                         // Optional: Reload to update the "Order" numbers visually, or just let it stay. 
                         // Reloading is safer to confirm state.
                         window.location.reload();
                    } else {
                        alert('Failed to save order');
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection
