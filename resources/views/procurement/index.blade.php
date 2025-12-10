@extends('layouts.app')

@section('content')
<div x-data="{
    selected: [],
    allSelected: false,
    resizing: false,

    startResize(event, colId) {
        this.resizing = true;
        let th = event.target.closest('th');
        let startX = event.pageX;
        let startWidth = th.offsetWidth;
        
        // Prevent text selection
        document.body.style.userSelect = 'none';
        document.body.style.cursor = 'col-resize';
        
        let onMouseMove = (e) => {
            let newWidth = Math.max(80, startWidth + (e.pageX - startX));
            this.colWidths[colId] = newWidth + 'px';
        };
        
        let onMouseUp = () => {
             this.resizing = false;
             document.body.style.userSelect = '';
             document.body.style.cursor = '';
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        };
        
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    },
    toggleAll() {
        if (this.selected.length === {{ $items->count() }}) {
            this.selected = [];
        } else {
            this.selected = [{{ $items->pluck('id')->implode(',') }}];
        }
    },
    deleteSelected() {
        if (this.selected.length === 0) return;

        window.dispatchEvent(new CustomEvent('open-confirm-modal', {
            detail: {
                title: 'Delete Selected Items',
                message: 'Are you sure you want to delete ' + this.selected.length + ' items?',
                confirmText: 'Delete',
                cancelText: 'Cancel',
                onConfirm: () => {
                    fetch('{{ route('admin.procurement.bulk-delete') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ ids: this.selected })
                    }).then(res => {
                        if(res.ok) {
                            window.location.reload();
                        } else {
                            window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Failed to delete items', type: 'error' } }));
                        }
                    });
                }
            }
        }));
    },
    deleteAll() {
        window.dispatchEvent(new CustomEvent('open-confirm-modal', {
            detail: {
                title: 'DELETE ALL DATA',
                message: 'WARNING: This will delete ALL data in the database. Are you absolutely sure?',
                confirmText: 'Yes, Delete Everything',
                cancelText: 'Cancel',
                onConfirm: () => {
                    // Double check by opening another modal or just proceeding? 
                    // User logic had a double confirm.
                    // For simplicity, let's just do one strong confirm or re-dispatch.
                    // Let's emulate the double check by dispatching another one.
                    setTimeout(() => {
                         window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                            detail: {
                                title: 'FINAL WARNING',
                                message: 'This action cannot be undone. double check: Are you absolutely really sure?',
                                confirmText: 'I Understand, DELETE',
                                cancelText: 'Cancel',
                                onConfirm: () => {
                                    let form = document.createElement('form');
                                    form.method = 'POST';
                                    form.action = '{{ route('admin.procurement.delete-all') }}';
                                    let csrf = document.createElement('input');
                                    csrf.type = 'hidden';
                                    csrf.name = '_token';
                                    csrf.value = '{{ csrf_token() }}';
                                    form.appendChild(csrf);
                                    document.body.appendChild(form);
                                    form.submit();
                                }
                            }
                        }));
                    }, 200);
                }
            }
        }));
    }
}" x-init="initResize();" class="space-y-6">


    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard Pengadaan</h1>
        <div class="flex items-center space-x-2">
            @if(auth()->user()->isAdmin())
                <div x-show="selected.length > 0" x-cloak class="flex items-center space-x-2 mr-4">
                    <button @click="deleteSelected()" class="bg-red-600 text-white px-3 py-1 rounded text-sm font-semibold hover:bg-red-700 shadow-sm transition">
                        Delete Selected (<span x-text="selected.length"></span>)
                    </button>
                </div>

                <a href="{{ route('admin.import.form') }}" class="bg-teal-600 text-white px-3 py-1 rounded text-sm font-semibold hover:bg-teal-700 shadow-sm transition">Import Excel</a>
                <a href="{{ route('admin.columns.index') }}" class="bg-gray-600 text-white px-3 py-1 rounded text-sm font-semibold hover:bg-gray-700 shadow-sm transition">Columns</a>
                <a href="{{ route('procurement.create') }}" class="bg-indigo-600 text-white px-3 py-1 rounded text-sm font-semibold hover:bg-indigo-700 shadow-sm transition">+ New Item</a>

            @endif
            <!-- Export Button -->
            <a href="{{ route('procurement.export') }}" class="bg-green-600 text-white px-3 py-1 rounded text-sm font-semibold hover:bg-green-700 shadow-sm transition">Export XLSX</a>
        </div>
    </div>

    <!-- Filters & Search -->
    <!-- Filters & Search -->
    <form method="GET" action="{{ route('dashboard') }}" class="bg-white p-4 rounded shadow space-y-4">
        <!-- Row 1: Search -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Mat Code, ID Procurement, Name, PO..." class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
        </div>

        <!-- Row 2: Filters -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Buyer</label>
                <select name="buyer" class="mt-1 block w-full rounded border-gray-300 shadow-sm sm:text-sm p-2 border">
                    <option value="">All Buyers</option>
                    @foreach($buyers as $buyer)
                        <option value="{{ $buyer->value }}" {{ request('buyer') == $buyer->value ? 'selected' : '' }}>{{ $buyer->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 block w-full rounded border-gray-300 shadow-sm sm:text-sm p-2 border">
                    <option value="">All Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Bagian</label>
                <select name="bagian" class="mt-1 block w-full rounded border-gray-300 shadow-sm sm:text-sm p-2 border">
                    <option value="">All Bagian</option>
                    @foreach($bagians as $bagian)
                        <option value="{{ $bagian->value }}" {{ request('bagian') == $bagian->value ? 'selected' : '' }}>{{ $bagian->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">User</label>
                <select name="user" class="mt-1 block w-full rounded border-gray-300 shadow-sm sm:text-sm p-2 border">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user }}" {{ request('user') == $user ? 'selected' : '' }}>{{ $user }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 w-full">Filter</button>
            </div>
        </div>
    </form>

    <!-- Desktop Table View -->
    <div class="hidden md:block bg-white shadow rounded overflow-hidden overflow-x-auto" :class="{'cursor-col-resize select-none': resizing}">
        <table class="min-w-full table-fixed divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr id="table-headers" class="border-b border-gray-200">
                    @if(auth()->user()->isAdmin())
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10 border-r border-gray-200">
                            <input type="checkbox" @click="toggleAll()" :checked="selected.length === {{ $items->count() }} && {{ $items->count() }} > 0" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </th>
                    @endif
                    @foreach($columns as $col)
                        <th 
                            data-id="{{ $col->id }}" 
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider group relative border-r border-gray-200" 

                            :style="'width: ' + (colWidths['{{ $col->id }}'] || 'auto') + '; min-width: ' + ('{{ $col->key }}' === 'status' ? '350px' : ('{{ $col->key }}' === 'bagian' ? '250px' : '100px')) + '; position: relative;'"
                        >
                            <div class="flex items-center justify-between h-full w-full">
                                <span class="truncate pr-4">{{ $col->label }}</span>
                                <!-- Robust Resize Handle -->
                                <div 
                                    class="absolute inset-y-0 right-0 w-4 cursor-col-resize z-50 flex items-center justify-center hover:bg-blue-50 transition-colors"
                                    @mousedown.stop.prevent="startResize($event, '{{ $col->id }}')"
                                    title="Drag to resize"
                                >
                                    <!-- Permanent visible line -->
                                    <div class="h-6 w-0.5 bg-gray-400"></div> 
                                </div>
                            </div>

                        </th>
                    @endforeach
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($items as $item)
                    <tr>
                        @if(auth()->user()->isAdmin())
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                <input type="checkbox" value="{{ $item->id }}" x-model="selected" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </td>
                        @endif
                        @foreach($columns as $col)
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                @if($col->key == 'status')
                                    <div x-data="{ 
                                        current: '{{ $item->status instanceof \UnitEnum ? $item->status->value : $item->status }}',
                                        update(val) {
                                            const oldVal = this.current;
                                            this.current = val;
                                            
                                            fetch('/procurement/{{ $item->id }}/quick-update', {
                                                method: 'POST',
                                                headers: { 
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({ field: 'status', value: val })
                                            })
                                            .then(r => r.json())
                                            .then(data => {
                                                if(!data.success) {
                                                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Failed: ' + (data.message || 'Unknown'), type: 'error' } }));
                                                    this.current = oldVal; 
                                                } else {
                                                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Status updated', type: 'success' } }));
                                                }
                                            });
                                        }
                                    }">
                                        <select x-model="current" @change="update($event.target.value)" 
                                            class="text-sm font-semibold rounded px-2 py-1 min-w-[200px] border-gray-300 focus:ring-2 focus:ring-indigo-500 cursor-pointer bg-white text-gray-900"
                                        >
                                            @foreach($statuses as $status)
                                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @elseif($col->key == 'bagian')
                                    <div x-data="{ 
                                        current: '{{ $item->bagian }}',
                                        update(val) {
                                            const oldVal = this.current;
                                            this.current = val;
                                            
                                            fetch('/procurement/{{ $item->id }}/quick-update', {
                                                method: 'POST',
                                                headers: { 
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({ field: 'bagian', value: val })
                                            })
                                            .then(r => r.json())
                                            .then(data => {
                                                if(!data.success) {
                                                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Failed: ' + (data.message || 'Unknown'), type: 'error' } }));
                                                    this.current = oldVal; // Revert
                                                } else {
                                                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Bagian updated', type: 'success' } }));
                                                }
                                            });
                                        }
                                     }">
                                        <select x-model="current" @change="update($event.target.value)" 
                                            class="text-sm border-gray-300 rounded p-1 min-w-[150px] bg-transparent hover:bg-gray-50 focus:bg-white transition-colors cursor-pointer"
                                        >
                                            <option value="">-</option>
                                            @foreach($bagians as $case)
                                                <option value="{{ $case->value }}">{{ $case->label() }}</option>
                                            @endforeach
                                        </select>
                                     </div>
                                @elseif($col->key == 'pg')
                                     <div x-data="{ 
                                        val: '{{ $item->pg }}',
                                        update() {
                                             fetch('/procurement/{{ $item->id }}/quick-update', {
                                                method: 'POST',
                                                headers: { 
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({ field: 'pg', value: this.val })
                                            }).then(r => r.json()).then(d => { 
                                                if(!d.success) {
                                                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: d.message, type: 'error' } }));
                                                } else {
                                                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'PG updated', type: 'success' } }));
                                                }
                                            });
                                        }
                                     }">
                                        <input type="text" x-model="val" @blur="update()" @keydown.enter="update()" class="text-sm border-gray-300 rounded p-1 w-full bg-transparent hover:bg-gray-50 focus:bg-white transition-colors">
                                     </div>
                                @elseif($col->key == 'buyer')
                                    @php
                                        $buyerEnum = $item->buyer; // Since it's cast, this is an Enum instance or null
                                        $color = $buyerEnum?->color() ?? '#f3f4f6';
                                        $isDark = in_array($color, ['#3d3d3d', '#b10202', '#753800', '#473822', '#11734b', '#0a53a8', '#215a6c', '#5a3286']);
                                        $textColor = $isDark ? 'text-white' : 'text-gray-800';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $textColor }}" style="background-color: {{ $color }}">
                                        {{ $buyerEnum?->label() ?? '-' }}
                                    </span>
                                @elseif((str_starts_with($col->key, 'tanggal_') || in_array($col->key, ['tanggal_po', 'tanggal_datang', 'tanggal_status'])) && $item->{$col->key})
                                     {{ \Carbon\Carbon::parse($item->{$col->key})->format('d M Y') }}
                                @elseif(str_starts_with($col->key, 'extra_'))
                                    {{ $item->extra_attributes[$col->key] ?? '-' }}
                                @elseif($col->key == 'nilai')
                                    {{ number_format($item->nilai, 0, ',', '.') }}
                                @else
                                    {{ $item->{$col->key} }}
                                @endif
                            </td>
                        @endforeach
                         <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('procurement.show', $item->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                         @if(auth()->user()->isAdmin())
                            <td></td>
                         @endif
                        <td colspan="{{ $columns->count() + 1 }}" class="px-6 py-4 text-center text-gray-500">No items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-4 mt-4">
        @foreach($items as $item)
            <a href="{{ route('procurement.show', $item->id) }}" class="block bg-white rounded-2xl border border-gray-200 shadow-sm p-4 relative hover:bg-gray-50 transition-colors z-10 cursor-pointer">
                <div class="pr-4">
                    <!-- Title -->
                    <h3 class="text-lg font-bold text-gray-900 line-clamp-2 break-words">
                        {{ $item->nama_barang ?? 'No Name' }}
                    </h3>
                    
                    <!-- ID Dokumen -->
                    <p class="text-sm text-gray-500 mt-1">
                        ID Procurement: <span class="text-gray-700">{{ $item->external_id ?? '-' }}</span>
                    </p>

                    <!-- Status -->
                    <div class="mt-3">
                        Status: <span class="text-sm font-medium text-gray-900">
                             {{ $item->status instanceof \UnitEnum ? $item->status->label() : $item->status }}
                        </span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $items->withQueryString()->links() }}
    </div>




</div>


@push('scripts')


@endpush
@endsection
