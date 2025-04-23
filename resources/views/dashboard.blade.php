
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                    <a href="{{ route('orders.index') }}" class="inline-block px-4 py-2 bg-blue-600 float-right text-white font-semibold rounded hover:bg-blue-700 transition duration-200">
                        Order List
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
