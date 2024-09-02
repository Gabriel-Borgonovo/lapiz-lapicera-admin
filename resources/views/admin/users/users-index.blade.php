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
                    </table>

                </div>
            </div>
        </div>






        <div>
            <!-- Campo para capturar el código de barras -->
            <input type="text" id="barcodeInput" class="absolute top-[-9999px] left-[-9999px]" />
        
            <!-- Modal para mostrar el código de barras -->
            <div id="barcodeModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
                <div class="absolute inset-0 bg-gray-900 bg-opacity-50"></div>
                <div class="bg-white rounded-lg shadow-lg p-6 z-10">
                    <h2 class="text-xl font-bold mb-4">Código de Barras Escaneado</h2>
                    <p id="barcodeText" class="mb-4"></p>
                    <div class="flex justify-end space-x-4">
                        <button id="closeModal"
                            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Cerrar</button>
                    </div>
                </div>
            </div>
        
        </div>
        <!-- Custom JavaScript -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const barcodeInput = document.getElementById('barcodeInput');
                const barcodeModal = document.getElementById('barcodeModal');
                const barcodeText = document.getElementById('barcodeText');
                const closeModal = document.getElementById('closeModal');
                
                // Make the barcode input field temporarily visible and focused
                function showBarcodeInput() {
                    barcodeInput.classList.remove('hidden');
                    barcodeInput.style.position = 'absolute';
                    barcodeInput.style.top = '0';
                    barcodeInput.style.left = '0';
                    barcodeInput.style.opacity = '0';
                    barcodeInput.focus();
                }
        
                if (barcodeInput) {
                    showBarcodeInput();
        
                    barcodeInput.addEventListener('input', function(event) {
                        setTimeout(() => {
                            const barcode = event.target.value.trim();
        
                            if (barcode) {
                                // Mostrar el modal
                                barcodeText.textContent = barcode;
                                barcodeModal.classList.remove('hidden');
        
                                event.target.value = ''; // Limpiar el campo después de enviar
                            }
                        }, 100); // Ajustar el retraso según sea necesario
                    });
        
                    // Cerrar el modal al hacer clic en el botón
                    closeModal.addEventListener('click', function() {
                        barcodeModal.classList.add('hidden');
                    });
                } else {
                    console.error('Campo de código de barras no encontrado.');
                }
            });
        </script>
</x-app-layout>
