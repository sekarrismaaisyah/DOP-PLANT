@extends('layouts.master')

@section('title', 'Fueling Evaluasi - Tableau')

@section('css')
<style>
    .tableau-wrapper {
        width: 100%;
        min-height: calc(100vh - 260px);
        border: 1px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
        background-color: #fff;
    }

    .tableau-iframe {
        width: 100%;
        height: calc(100vh - 260px);
        min-height: 620px;
        border: 0;
    }
</style>
@endsection

@section('content')
    <x-page-title title="Fueling Evaluasi" pagetitle="Tableau Embed Dashboard" />

    <div class="row">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">HSE Division Dashboard</h5>
                    <a
                        href="https://idashboard.beraucoal.co.id/#/site/hsedivision/redirect_to_view/25998"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn btn-sm btn-outline-primary"
                    >
                        Buka di Tab Baru
                    </a>
                </div>
                <div class="card-body">
                    <div class="tableau-wrapper">
                        <iframe
                            class="tableau-iframe"
                            src="https://idashboard.beraucoal.co.id/#/site/hsedivision/redirect_to_view/25998"
                            title="Tableau HSE Division Dashboard"
                            loading="lazy"
                            allowfullscreen
                        ></iframe>
                    </div>
                    <p class="text-muted mt-3 mb-0 small">
                        Jika embed tidak muncul karena kebijakan browser/server (X-Frame-Options/CSP),
                        gunakan tombol <strong>Buka di Tab Baru</strong>.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
