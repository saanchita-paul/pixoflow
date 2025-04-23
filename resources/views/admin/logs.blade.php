<x-app-layout>
    <div class="max-w-7xl mx-auto p-6">
        <h2 class="text-xl font-bold mb-6">📜 Employee Action Logs</h2>

        <table class="w-full table-auto border-collapse text-sm bg-white shadow rounded">
            <thead>
            <tr class="bg-gray-100 text-left">
                <th class="p-2">👤 User</th>
                <th class="p-2">📁 File</th>
                <th class="p-2">📝 Order</th>
                <th class="p-2">📌 Status</th>
                <th class="p-2">⏰ Time</th>
            </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr class="border-t">
                    <td class="p-2 text-gray-800">{{ $log->user->name ?? 'N/A' }}</td>
                    <td class="p-2 text-blue-700">{{ $log->file->name ?? 'N/A' }}</td>
                    <td class="p-2">{{ $log->order->title ?? 'N/A' }}</td>
                    <td class="p-2">
                            <span class="px-2 py-1 rounded text-white
                                {{ $log->action === 'Completed' ? 'bg-green-600' :
                                   ($log->action === 'In Progress' ? 'bg-yellow-500' : 'bg-gray-400') }}">
                                {{ $log->action }}
                            </span>
                    </td>
                    <td class="p-2 text-gray-500">{{ $log->updated_at->format('M d, Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-gray-500 py-4">No logs found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</x-app-layout>
