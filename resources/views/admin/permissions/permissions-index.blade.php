<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Permissions') }}
        </h2>
    </x-slot>

   
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 overflow-scroll">

                    <div class="flex min-600 justify-between mb-4 items-center shadow-lg p-2 rounded">
                        <a href="{{ route('permissions.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Create Permission</a>
                        <h3 class="text-lg font-semibold">{{ __('Permissions') }}</h3>
                    </div>
                    <table class="w-full bg-white min-600">
                        <thead>
                            <tr>
                                <th class="p-2 text-start">Name</th>
                                <th class="p-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $permission)
                                <tr>
                                    <td class="p-2 font-black text-blue-900">{{ $permission->name }}</td>
                                    <td class="p-2 text-center">
                                        <a href="{{ route('permissions.edit', $permission->id) }}" class="inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded">Edit</a>
                                        
                                        <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded" onclick="return confirm('Estas seguro?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>