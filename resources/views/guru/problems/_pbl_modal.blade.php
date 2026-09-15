<div id="pbl-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-3xl w-full">
            <div class="px-4 py-3 border-b dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detail PBL</h3>
                <button id="pbl-modal-close" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>

            <div id="pbl-modal-body" class="p-4 max-h-[60vh] overflow-y-auto">
                <!-- loaded via AJAX -->
                <div class="text-center text-sm text-gray-500">Memuat...</div>
            </div>
        </div>
    </div>
</div>
