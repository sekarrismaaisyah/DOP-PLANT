@extends('pilot-project-validation.layout.peer-app')

@section('title', 'Pilot Project Validation - Edit Proyek')

@section('content')
<div class="bg-white rounded-2xl anchored-card overflow-hidden max-w-4xl">
   <div class="p-6 border-b border-outline-variant/20">
      <h2 class="font-headline font-bold text-xl text-on-surface">Edit Proyek Pilot Validation</h2>
      <p class="mt-1 text-xs text-on-surface-variant font-medium">Perubahan pada master proyek akan memengaruhi tampilan dashboard utama Pilot Project Validation.</p>
   </div>

   <div class="p-6">
      <form action="{{ route('pilot-project-validation.projects.update', $project) }}" method="post">
         @csrf
         @method('PUT')
         @include('pilot-project-validation.projects._form', ['project' => $project, 'submitLabel' => 'Perbarui proyek'])
      </form>
   </div>
</div>
@endsection
