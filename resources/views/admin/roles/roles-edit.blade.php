<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar rol') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('roles-update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700">Role Name</label>
                        <input type="text" name="name" id="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500" value="{{ $role->name }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Permissions</label>
                        @forelse($permissions as $permission)
                            <div class="flex items-center">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="mr-2"
                                {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                <label>{{ $permission->name }}</label>
                            </div>
                        @empty
                            <p>No permissions loaded</p>
                        @endforelse
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update</button>
                    </div>
                </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>