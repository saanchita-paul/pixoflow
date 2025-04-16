<x-app-layout>
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800">
                📁 {{ $order->title }}
                @if($currentFolder)
                    <span class="text-gray-500 text-base ml-2">
                        ›
                        @php
                            $parts = explode('/', $currentFolder);
                            $accumulated = '';
                        @endphp
                        @foreach($parts as $index => $part)
                            @php $accumulated .= ($index > 0 ? '/' : '') . $part; @endphp
                            <a href="{{ route('orders.show', ['order' => $order->id, 'folder' => $accumulated]) }}"
                               class="text-blue-600 hover:underline">
                                {{ $part }}
                            </a>
                            @if(!$loop->last)
                                <span class="mx-1 text-gray-400">›</span>
                            @endif
                        @endforeach
                    </span>
                @endif
            </h2>
        </div>

        <form method="POST" action="{{ route('orders.claim-files', $order->id) }}" id="claimForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Folders --}}
                @foreach($subFolders as $folder)
                    @php
                        $fullPath = $currentFolder ? "$currentFolder/$folder" : $folder;
                        $folderFiles = $order->files()->where('path', $fullPath)->get();
                        $total = $folderFiles->count();
                        $claimed = $folderFiles->filter(fn($file) => $file->userClaims->isNotEmpty())->count();
                        $isFullyClaimed = $total > 0 && $total === $claimed;
                    @endphp

                    <div class="relative border p-4 rounded bg-gray-100 hover:bg-gray-200 transition">
                        <div class="flex items-center">
                            <input type="checkbox"
                                   name="folder_paths[]"
                                   value="{{ $fullPath }}"
                                   class="mr-3 folder-checkbox"
                                {{ $isFullyClaimed ? 'disabled checked' : '' }}>
                            <a href="{{ route('orders.show', ['order' => $order->id, 'folder' => $fullPath]) }}"
                               class="text-blue-700 font-medium truncate">
                                📁 {{ $folder }}
                            </a>
                            @if($isFullyClaimed)
                                <span class="ml-2 text-xs text-red-600">🔒 Claimed</span>
                            @endif

                            <!-- 3-dot dropdown -->
                            <div class="ml-auto relative">
                                <button type="button" class="text-gray-600 hover:text-gray-800 font-weight-bold" onclick="toggleDropdown('{{ Str::slug($fullPath) }}')">
                                    ⋮
                                </button>
                                <div id="dropdown-{{ Str::slug($fullPath) }}"
                                     class="hidden absolute right-0 mt-2 w-40 bg-white border rounded shadow-md z-10">
                                    <a href="{{ route('admin.order-progress', ['order' => $order->id, 'folder' => $fullPath]) }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">See Progress</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Files --}}
                @foreach($files as $file)
                    @php $isClaimed = $file->userClaims->isNotEmpty(); @endphp
                    <div class="flex items-center border p-3 rounded bg-white hover:bg-gray-50 transition">
                        @if(Str::endsWith($file->name, ['.jpg', '.jpeg', '.png', '.gif']))
                            <img src="{{ asset('storage/orders/' . Str::slug($order->title) . '-' . $order->id . '/' . ($currentFolder ? "$currentFolder/" : '') . $file->name) }}"
                                 alt="{{ $file->name }}"
                                 class="w-12 h-12 object-cover rounded mr-4" />
                        @else
                            <div class="w-12 h-12 bg-gray-200 text-gray-500 flex items-center justify-center rounded mr-4">📄</div>
                        @endif
                        <div class="flex flex-col flex-1">
                            <span class="text-sm font-medium text-gray-800 truncate max-w-xs">{{ $file->name }}</span>
                            <span class="text-xs text-gray-500">
                                {{ number_format(Storage::disk('public')->size('orders/' . Str::slug($order->title) . '-' . $order->id . ($currentFolder ? "/$currentFolder" : '') . '/' . $file->name) / 1024, 2) }} KB ·
                                {{ \Carbon\Carbon::parse($file->created_at)->format('M d, Y') }}
                            </span>
                        </div>
                        @if($isClaimed)
                            <span class="ml-4 text-xs text-red-600">🔒 Claimed</span>
                        @else
                            <input type="checkbox" name="file_ids[]" value="{{ $file->id }}" class="ml-4 file-checkbox">
                        @endif
                        <!-- 3-dot dropdown for file status -->
                        <div class="ml-auto relative">
                            <button type="button" class="text-gray-600 hover:text-gray-800 font-weight-bold" onclick="toggleDropdown('{{ Str::slug($file->id) }}')">
                                ⋮
                            </button>
                            <div id="dropdown-{{ Str::slug($file->id) }}"
                                 class="hidden absolute right-0 mt-2 w-40 bg-white border rounded shadow-md z-10">
                                <a href="{{ route('admin.order-progress', ['order' => $order->id, 'file' => $file->id]) }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">See Progress</a>
                                <button type="button"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        onclick="openStatusModal('{{ $order->id }}', '{{ $file->id }}', '{{ optional($file->userClaims->first())->id }}')">
                                    Change Status
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 hidden" id="claimButtonContainer">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Claim Selected Files
                </button>
            </div>
        </form>
    </div>

    <!-- Status Update Modal -->
    <div class="fixed z-50 inset-0 overflow-y-auto hidden" id="statusModal">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white border rounded-lg shadow-xl w-full max-w-md">
                <form method="POST" id="statusForm" action="">
                    @csrf
                    <input type="hidden" name="order_id" id="modalOrderId">
                    <input type="hidden" name="file_id" id="modalFileId">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-lg font-medium text-gray-800">Update File Status</h3>
                    </div>
                    <div class="px-6 py-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Select Status</label>
                        <select name="status" id="status" class="w-full border-gray-300 rounded">
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="px-6 py-4 border-t flex justify-end">
                        <button type="button" onclick="closeModal()" class="mr-2 px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function toggleDropdown(id) {
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => el.classList.add('hidden'));
            document.getElementById('dropdown-' + id).classList.toggle('hidden');
        }

        function openStatusModal(orderId, fileId, claimId) {
            document.getElementById('modalOrderId').value = orderId;
            document.getElementById('modalFileId').value = fileId;
            document.getElementById('statusForm').action = `/claims/${claimId}/status`;
            document.getElementById('statusModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]:not(:disabled)');
            const buttonContainer = document.getElementById('claimButtonContainer');
            function toggleButton() {
                const checked = Array.from(checkboxes).some(cb => cb.checked);
                buttonContainer.classList.toggle('hidden', !checked);
            }
            checkboxes.forEach(cb => cb.addEventListener('change', toggleButton));
        });
    </script>
</x-app-layout>
