@extends('DopSafety.layouts.app')

@section('title', 'Input DOP Baru — DOP Safety')

@push('head')
@include('DopSafety.partials.styles')
@endpush

@section('content')
@include('DopSafety.partials.page-header', [
   'title' => 'Input Daily Operation Plan',
   'subtitle' => 'PT. PAMA PERSADA — PLANT DEPT. · ' . $disclaimer,
   'breadcrumb' => 'Input DOP',
])

@if($errors->any())
<div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900 mb-4">
   <ul class="list-disc pl-4 space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
</div>
@endif

@include('DopSafety.ojii.partials.form', [
   'defaults' => $defaults,
   'sectionOptions' => $sectionOptions,
   'shiftOptions' => $shiftOptions,
   'statusOptions' => $statusOptions,
   'tableStructure' => $tableStructure ?? config('dop_safety.table_structure', []),
   'formAction' => route('dop-safety.oji.store'),
   'formMethod' => 'POST',
   'submitLabel' => 'Simpan OJI',
])
@endsection
