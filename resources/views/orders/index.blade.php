<x-app-layout>
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">📁 All Orders</h2>
            @if(auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('orders.create') }}" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200">
                    ➕ Create Order
                </a>
            @endif
        </div>
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($orders->isEmpty())
            <div class="text-center text-gray-500 py-12">
                No orders found. Click “Create Order” to get started.
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($orders as $order)
                    <a href="{{ route('orders.show', $order) }}"
                       class="border rounded-xl p-5 shadow-sm hover:shadow-lg transition duration-200 bg-white flex flex-col hover:bg-gray-50">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xl font-semibold text-gray-800">📁 {{ $order->title }}</span>

                            @if($order->status === 'processing')
                                <div class="flex items-center text-yellow-600 text-sm gap-2">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                    <span>Processing...</span>
                                </div>
                            @endif
                        </div>

                        <span class="text-sm text-gray-600">{{ $order->files_count }} {{ Str::plural('file', $order->files_count) }}</span>
                        <span class="text-xs text-gray-400 mt-auto">Created {{ $order->created_at->diffForHumans() }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
