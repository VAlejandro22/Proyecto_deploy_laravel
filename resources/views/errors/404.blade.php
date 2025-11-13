@extends('layouts.app')

@section('content')
<div class="text-center py-20">
    <h1 class="text-6xl font-bold text-cyan-400">404</h1>
    <p class="text-xl text-blue-100 mt-4">Oops, página no encontrada.</p>
    <a href="{{ route('dashboard') }}"
       class="mt-6 inline-block bg-cyan-600 hover:bg-cyan-500 text-white px-6 py-2 rounded-lg shadow">
       Ir al inicio
    </a>
</div>
@endsection
