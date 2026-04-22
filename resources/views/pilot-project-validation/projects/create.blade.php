@extends('pilot-project-validation.layout.peer-app')

@section('title', 'Pilot Project Validation - Tambah Proyek')

@section('content')
<div class="bg-white rounded-2xl anchored-card overflow-hidden max-w-4xl">
   <div class="p-6 border-b border-outline-variant/20">
      <h2 class="font-headline font-bold text-xl text-on-surface">Tambah Proyek Pilot Validation</h2>
      <p class="mt-1 text-xs text-on-surface-variant font-medium">Proyek baru akan dibuat dengan struktur default: gate, metrik, dan timeline awal.</p>
   </div>

   <div class="p-6">
      <form action="{{ route('pilot-project-validation.projects.store') }}" method="post">
         @csrf
         @include('pilot-project-validation.projects._form', ['submitLabel' => 'Simpan proyek'])
      </form>
   </div>
</div>
@endsection
