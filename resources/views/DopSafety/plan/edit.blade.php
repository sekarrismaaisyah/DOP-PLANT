@extends('DopSafety.layouts.app')

@section('title', 'Edit DOP — DOP Safety')

@push('head')
@include('DopSafety.partials.styles')
@endpush

@section('content')
@include('DopSafety.partials.page-header', [
   'title' => 'Edit Daily Operation Plan',
   'subtitle' => $plan->site . ' · ' . $plan->plan_date->format('d M Y') . ' · ' . $plan->shiftLabel(),
   'breadcrumb' => 'Edit DOP',
])

@if($errors->any())
<div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900 mb-4">
   <ul class="list-disc pl-4 space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
</div>
@endif

@include('DopSafety.plan.partials.form', [
   'defaults' => $defaults,
   'sectionOptions' => $sectionOptions,
   'unitCategories' => $unitCategories,
   'shiftOptions' => $shiftOptions,
   'statusOptions' => $statusOptions,
   'formAction' => route('dop-safety.plan.update', $plan),
   'formMethod' => 'PUT',
   'submitLabel' => 'Perbarui DOP',
])
@endsection
