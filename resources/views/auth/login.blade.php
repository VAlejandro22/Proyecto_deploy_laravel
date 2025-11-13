<x-guest-layout>
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-cyan-900 via-blue-900 to-blue-800
               px-4 py-12">

        <div
            class="w-full max-w-md bg-white/10 backdrop-blur-md rounded-3xl shadow-2xl border border-cyan-400
                   p-10 space-y-6 text-cyan-200">

            <h2 class="text-center text-3xl font-extrabold tracking-tight">
                Inicia Sesión
            </h2>

            @if (session('status'))
                <div
                    class="bg-green-800 bg-opacity-70 p-3 rounded-md text-center text-green-300 font-semibold">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                    <!-- Switch visual para modo oscuro -->
                

                <div>
                    <label for="email" class="block mb-2 font-semibold">Correo Electrónico</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}"
                         autofocus
                        class="w-full px-5 py-3 rounded-lg bg-transparent border border-cyan-400 placeholder-cyan-400
                               text-cyan-100 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition" />
                    @error('email')
                        <p class="mt-1 text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block mb-2 font-semibold">Contraseña</label>
                    <input id="password" name="password" type="password"  autocomplete="current-password"
                        class="w-full px-5 py-3 rounded-lg bg-transparent border border-cyan-400 placeholder-cyan-400
                               text-cyan-100 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition" />
                    @error('password')
                        <p class="mt-1 text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-cyan-200">
                    <label class="inline-flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="form-checkbox rounded border-cyan-400 text-cyan-500">
                        <span>Recordarme</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-cyan-300 hover:text-cyan-400 underline transition">¿Olvidaste tu contraseña?</a>
                    @endif
                </div>

                <button type="submit"
                    class="w-full py-3 bg-cyan-600 hover:bg-cyan-700 rounded-lg text-white font-bold text-lg
                           transition duration-300 shadow-md hover:shadow-lg">
                    Entrar
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
