<x-guest-layout>
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-cyan-900 via-blue-900 to-blue-800
               px-4 py-12">

        <div
            class="w-full max-w-md bg-white/10 backdrop-blur-md rounded-3xl shadow-2xl border border-cyan-400
                   p-10 space-y-6 text-cyan-200">

            <h2 class="text-center text-3xl font-extrabold tracking-tight">
                Registrarse
            </h2>

            @if ($errors->any())
                <div
                    class="bg-red-900 bg-opacity-70 p-3 rounded-md text-center text-red-400 font-semibold">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block mb-2 font-semibold">Nombre</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                        class="w-full px-5 py-3 rounded-lg bg-transparent border border-cyan-400 placeholder-cyan-400
                               text-cyan-100 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition" />
                </div>

                <div>
                    <label for="email" class="block mb-2 font-semibold">Correo Electrónico</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                        class="w-full px-5 py-3 rounded-lg bg-transparent border border-cyan-400 placeholder-cyan-400
                               text-cyan-100 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition" />
                </div>

                <div>
                    <label for="password" class="block mb-2 font-semibold">Contraseña</label>
                    <input id="password" name="password" type="password" required
                        class="w-full px-5 py-3 rounded-lg bg-transparent border border-cyan-400 placeholder-cyan-400
                               text-cyan-100 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition" />
                </div>

                <div>
                    <label for="password_confirmation" class="block mb-2 font-semibold">Confirmar Contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        class="w-full px-5 py-3 rounded-lg bg-transparent border border-cyan-400 placeholder-cyan-400
                               text-cyan-100 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition" />
                </div>

                <button type="submit"
                    class="w-full py-3 bg-cyan-600 hover:bg-cyan-700 rounded-lg text-white font-bold text-lg
                           transition duration-300 shadow-md hover:shadow-lg">
                    Registrarse
                </button>
            </form>

            <p class="mt-4 text-sm text-center text-cyan-300">
                ¿Ya tienes cuenta?
                <a href="{{ route('login') }}" class="underline hover:text-cyan-400 transition">Inicia sesión</a>
            </p>
        </div>
    </div>
</x-guest-layout>
