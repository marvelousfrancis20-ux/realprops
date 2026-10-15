@if (session('toast'))
    <div x-data="{ show: true }"
         x-init="setTimeout(() => show = false, 4000)"
         x-show="show"
         x-transition.opacity
         role="status"
         class="pointer-events-none fixed inset-x-0 top-20 z-50 flex justify-center px-4">
        <div class="pointer-events-auto rounded-full bg-gray-900 px-5 py-2.5 text-sm font-medium text-white shadow-lg dark:bg-white dark:text-gray-900">
            {{ session('toast') }}
        </div>
    </div>
@endif
