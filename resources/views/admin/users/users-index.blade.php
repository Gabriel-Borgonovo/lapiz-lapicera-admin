<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    {{-- <a href="{{ route('users-create')}}"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">Add
                        New User</a> --}}

                    <table class="w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2">ID</th>
                                <th class="py-2">image</th>
                                <th class="py-2">Name</th>
                                <th class="py-2">Email</th>
                                <th class="py-2">Roles</th>
                                <th class="py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="py-2">{{ $user->id }}</td>
                                    <td class="py-2">

                                        @if (env('APP_ENV') === 'production')
                                            @if ($user->profile_image)
                                                <img src="../{{ $user->profile_image }}"
                                                    class="w-12 h-12 rounded-full mr-4 object-cover"
                                                    alt="imagen de perfil" />
                                            @endif
                                        @else
                                            @if ($user->profile_image)
                                                <img src="/storage/{{ $user->profile_image }}"
                                                    class="w-12 h-12 rounded-full mr-4 object-cover"
                                                    alt="imagen de perfil" />
                                            @endif
                                        @endif
                                    </td>
                                    <td class="py-2">{{ $user->name }}</td>
                                    <td class="py-2">{{ $user->email }}</td>
                                    <td class="py-2">
                                        @forelse($user->roles as $role)
                                            <span
                                                class="bg-gray-200 text-gray-700 py-1 px-3 rounded-full text-sm">{{ $role->name }}</span>
                                        @empty
                                            <span>No posee rol</span>
                                        @endforelse
                                    </td>
                                    <td class="py-2">
                                        <a href="{{ route('users-edit-role', $user->id) }}"
                                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded">Edit
                                            Roles</a>
                                        <form action="{{ route('users-destroy', $user->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
