<x-app-layout>
    <div class="max-w-7xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-6">📊 Admin Dashboard</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Orders Overview --}}
            <div>
                <h3 class="text-xl font-semibold mb-3">🧾 Orders Progress</h3>
                <div class="space-y-4">
                    @foreach($orderStats as $stat)
                        <div class="p-4 bg-white shadow rounded border">
                            <h4 class="text-lg font-semibold">{{ $stat['order']->title }}</h4>
                            <p>Total Files: {{ $stat['total'] }}</p>
                            <p>✅ Completed: {{ $stat['completed'] }}</p>
                            <p>🕓 In Progress: {{ $stat['inProgress'] }}</p>
                            <p>🆓 Remaining: {{ $stat['remaining'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Employee Overview --}}
            <div>
                <h3 class="text-xl font-semibold mb-3">👷 Employee Workload</h3>
                <div class="space-y-4">
                    @foreach($userStats as $stat)
                        <div class="p-4 bg-white shadow rounded border">
                            <h4 class="text-lg font-semibold">{{ $stat['name'] }}</h4>
                            <p>Total Assigned Files: {{ $stat['total'] }}</p>
                            <p>✅ Completed: {{ $stat['completed'] }}</p>
                            <p>🕓 In Progress: {{ $stat['inProgress'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
