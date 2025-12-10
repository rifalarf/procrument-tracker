<div 
    x-data="{ 
        show: false, 
        message: '', 
        type: 'success', // success, error, info
        timeout: null 
    }" 
    @notify.window="
        message = $event.detail.message; 
        type = $event.detail.type || 'success'; 
        show = true; 
        clearTimeout(timeout);
        timeout = setTimeout(() => show = false, 3000);
    "
    x-show="show" 
    x-transition:enter="transform ease-out duration-300 transition"
    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-cloak
    class="fixed top-4 right-4 z-50 flex w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-md border-l-4"
    :class="{
        'border-green-500': type === 'success',
        'border-red-500': type === 'error',
        'border-blue-500': type === 'info'
    }"
    role="alert"
>
    <div class="flex items-center justify-center w-12 bg-gray-50" 
         :class="{
            'text-green-500': type === 'success',
            'text-red-500': type === 'error',
            'text-blue-500': type === 'info'
         }">
        <!-- Icon based on type -->
        <template x-if="type === 'success'">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </template>
        <template x-if="type === 'error'">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </template>
        <template x-if="type === 'info'">
             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </template>
    </div>

    <div class="px-4 py-2 -mx-3">
        <div class="mx-3">
            <span class="font-semibold" :class="{
                'text-green-500': type === 'success',
                'text-red-500': type === 'error',
                'text-blue-500': type === 'info'
            }" x-text="type.charAt(0).toUpperCase() + type.slice(1)"></span>
            <p class="text-sm text-gray-600" x-text="message"></p>
        </div>
    </div>
</div>
