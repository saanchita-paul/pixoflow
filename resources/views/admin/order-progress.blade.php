<x-app-layout>
    <div class="max-w-7xl mx-auto p-6">
        <h2 class="text-xl font-bold mb-6">📝 Progress for Order: {{ $order->title }}</h2>

        @foreach($claims as $userId => $userClaims)
            <div class="mb-8 border border-gray-200 rounded p-4 bg-white shadow">
                <h3 class="text-lg font-semibold text-blue-700 mb-3">
                    👤 {{ $userClaims->first()->user->name }}
                </h3>

                <table class="w-full table-auto border-collapse text-sm">
                    <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="p-2">📄 File</th>
                        <th class="p-2">Order Name</th>
                        <th class="p-2">Path</th>
                        <th class="p-2">🟢 Status</th>
                        <th class="p-2">⏰ Claimed At</th>
                        <th class="p-2">🙋‍♂️ Claimed By</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($userClaims as $claim)
                        @php
                            $file = $claim->file;
                            $fileName = $file->name;
                            $isImage = Str::endsWith($fileName, ['.jpg', '.jpeg', '.png', '.gif']);
                            $filePath = 'orders/' . Str::slug($order->title) . '-' . $order->id . '/' . ($file->path ?? '') . ($file->path ? '/' : '') . $fileName;
                        @endphp

                        <tr class="border-t align-top">
                            <td class="p-2 flex items-center gap-3">
                                @if($isImage)
                                    <img src="{{ asset('storage/' . $filePath) }}" alt="{{ $fileName }}"
                                         class="w-12 h-12 object-cover rounded">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 text-gray-500 flex items-center justify-center rounded">📄</div>
                                @endif

                                <span class="text-sm font-medium text-gray-800 truncate max-w-[300px]">{{ $fileName }}</span>
                            </td>
                            <td class="p-2 text-gray-600">{{ $order->title }}</td>
                            <td class="p-2 text-gray-600 truncate max-w-xs">{{ $file->path ?? 'N/A' }}</td>

                            <td class="p-2">
                                    <span class="px-2 py-1 rounded text-black
                                        {{ $claim->status === 'Completed' ? 'bg-green-600' :
                                           ($claim->status === 'In Progress' ? 'bg-yellow-500' : 'bg-gray-400') }}">
                                        {{ $claim->status }}
                                    </span>
                            </td>
                            <td class="p-2 text-gray-600">{{ $claim->created_at->format('M d, Y H:i') }}</td>
                            <td class="p-2 text-gray-800">{{ $claim->user->name }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</x-app-layout>
