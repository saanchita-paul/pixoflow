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
                            @php
                                $accumulated .= ($index > 0 ? '/' : '') . $part;
                            @endphp

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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {{-- Folders --}}
            @foreach($subFolders as $folder)
                <a href="{{ route('orders.show', ['order' => $order->id, 'folder' => $currentFolder ? "$currentFolder/$folder" : $folder]) }}" class="border p-4 rounded bg-gray-100 hover:bg-gray-200">
                    📁 {{ $folder }}
                </a>
            @endforeach

            @foreach($files as $file)
                <div class="flex items-center border p-3 rounded bg-white hover:bg-gray-50 transition">
                    @if(Str::endsWith($file->name, ['.jpg', '.jpeg', '.png', '.gif']))
                        <img src="{{ asset('storage/orders/' . Str::slug($order->title) . '-' . $order->id . '/' . ($currentFolder ? "$currentFolder/" : '') . $file->name) }}"
                             alt="{{ $file->name }}"
                             class="w-12 h-12 object-cover rounded mr-4" />
                    @else
                        <div class="w-12 h-12 bg-gray-200 text-gray-500 flex items-center justify-center rounded mr-4">
                            📄
                        </div>
                    @endif

                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-800 truncate max-w-xs">{{ $file->name }}</span>
                        <span class="text-xs text-gray-500">
                            {{ Storage::disk('public')->size('orders/' . Str::slug($order->title) . '-' . $order->id . ($currentFolder ? "/$currentFolder" : '') . '/' . $file->name) / 1024 | number_format(2) }} KB ·
                            {{ \Carbon\Carbon::parse($file->created_at)->format('M d, Y') }}
                        </span>
                    </div>
                </div>
            @endforeach


        </div>
    </div>
</x-app-layout>
