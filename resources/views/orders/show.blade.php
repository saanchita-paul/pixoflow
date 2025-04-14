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

            {{-- Files --}}
            @foreach($files as $file)
                <div class="border p-4 rounded bg-white">
                    <p class="font-semibold">{{ $file->name }}</p>
                    @if(Str::endsWith($file->name, ['.jpg', '.jpeg', '.png', '.gif']))
                        <img src="{{ asset('storage/orders/' . ($currentFolder ? "$currentFolder/" : '') . $file->name) }}" class="mt-2 max-w-full h-auto"/>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
