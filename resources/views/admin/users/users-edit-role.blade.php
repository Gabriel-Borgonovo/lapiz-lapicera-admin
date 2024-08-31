<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Actualizar rol') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div>
                        <span class="py-2 text-gray-700">Usuario:</span>
                        <h1 class="py-2 px-4 rounded text-white bg-gradient-to-r from-blue-600 via-green-500 to-indigo-400 bg-transparent text-4xl font-bold">{{$user->name}}</h1>
                    </div>

                    <form action="{{ route('users-update-role', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4 mt-4">
                            <label class="block text-gray-700">Roles</label>
                            @foreach ($roles as $role)
                                <div class="flex items-center">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                        {{ $user->roles->contains($role->id) ? 'checked' : '' }} class="mr-2">
                                    <label>{{ $role->name }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update
                                Roles
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>