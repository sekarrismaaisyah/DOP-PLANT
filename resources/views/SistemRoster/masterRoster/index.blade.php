@extends('layouts.masterRoster')

@section('title', 'Master Roster Weekly')

@section('css')
<style>
    :root {
        --kt-text: #071437;
        --kt-border: #eef0f4;
        --kt-card: #ffffff;
        --roster-radius: 16px;
        --roster-radius-sm: 10px;
        --roster-border: #e2e8f0;
        --roster-accent: #0d6efd;
    }

    /* Period toolbar */
    .roster-period-card {
        background: var(--kt-card);
        border: 1px solid var(--kt-border);
        border-radius: var(--roster-radius);
        padding: 1.35rem 1.75rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(16,24,40,.04), 0 1px 0 rgba(16,24,40,.02);
        transition: box-shadow .3s ease, border-color .2s ease;
    }
    .roster-period-card:hover { box-shadow: 0 8px 24px rgba(16,24,40,.06); border-color: #e2e8f0; }
    .roster-period-card .period-label {
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .12em;
        color: #94a3b8;
        font-weight: 700;
    }
    .roster-period-card .period-range {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--kt-text);
        letter-spacing: -.03em;
    }
    .roster-week-nav {
        display: inline-flex;
        align-items: center;
        background: #f8fafc;
        border: 1px solid var(--kt-border);
        border-radius: var(--roster-radius-sm);
        padding: 4px;
        gap: 2px;
        transition: all .2s ease;
    }
    .roster-week-nav .btn-week {
        padding: .5rem 1rem;
        font-size: .8rem;
        font-weight: 600;
        border-radius: 8px;
        color: #64748b;
        border: none;
        background: transparent;
        transition: all .25s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
    }
    .roster-week-nav .btn-week:hover {
        background: #fff;
        color: var(--roster-accent);
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
    }
    .roster-week-nav .btn-week.btn-current {
        background: var(--roster-accent);
        color: #fff;
        box-shadow: 0 2px 8px rgba(13, 110, 253, .3);
    }

    /* Tabs */
    .nav-tabs-roster {
        border: none;
        gap: 6px;
        flex-wrap: wrap;
        background: #f1f5f9;
        padding: 6px;
        border-radius: var(--roster-radius-sm);
        margin-bottom: 1.5rem;
    }
    .nav-tabs-roster .nav-link {
        border: none;
        font-weight: 600;
        color: #64748b;
        border-radius: 8px;
        padding: .55rem 1.1rem;
        font-size: .8rem;
        transition: all .2s ease;
    }
    .nav-tabs-roster .nav-link:hover { color: var(--roster-accent); background: #fff; }
    .nav-tabs-roster .nav-link.active {
        color: #fff;
        background: var(--roster-accent);
        box-shadow: 0 2px 8px rgba(13, 110, 253, .3);
    }

    /* ========== Sama dengan dashboard: lmo-perf calendar card & grid ========== */
    .lmo-perf .card-panel {
        background: var(--kt-card);
        border: 1px solid var(--kt-border);
        border-radius: 18px;
        box-shadow: 0 2px 8px rgba(16,24,40,.04), 0 1px 0 rgba(16,24,40,.02);
        transition: box-shadow .3s ease, border-color .2s ease;
    }
    .lmo-perf .card-panel:hover { box-shadow: 0 8px 24px rgba(16,24,40,.06); }
    .lmo-perf .calendar-card { overflow: hidden; }
    .lmo-perf .calendar-scroll { border-radius: 0 0 18px 18px; overflow-x: auto; -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }
    .lmo-perf .calendar-head {
        display: grid;
        grid-template-columns: 200px repeat(7, 1fr);
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 1px solid var(--kt-border);
    }
    .lmo-perf .calendar-head div,
    .lmo-perf .roster-calendar-table thead th {
        padding: .9rem .75rem;
        font-size: .75rem;
        font-weight: 700;
        letter-spacing: .1em;
        color: #64748b;
        text-transform: uppercase;
        text-align: center;
        border-right: 1px solid rgba(226,232,240,.8);
    }
    .lmo-perf .calendar-head div:first-child,
    .lmo-perf .roster-calendar-table thead th:first-child {
        text-align: left;
        padding-left: 1.25rem;
        border-right: 1px solid rgba(226,232,240,.9);
    }
    .lmo-perf .calendar-head div:last-child,
    .lmo-perf .roster-calendar-table thead th:last-child { border-right: 0; }
    .lmo-perf .roster-calendar-table thead th.weekend {
        background: linear-gradient(180deg, #f1f5f9 0%, #eef2f7 100%);
    }
    .lmo-perf .roster-calendar-table thead th.today-col {
        background: linear-gradient(180deg, #fff8e6 0%, #fff3cd 100%);
        color: #b45309;
    }
    .lmo-perf .roster-calendar-table thead th.today-col .day-name,
    .lmo-perf .roster-calendar-table thead th.today-col .day-date { color: #b45309; }
    .lmo-perf .current-pill {
        position: absolute;
        top: 10px;
        right: 10px;
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: #fff;
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .05em;
        padding: .25rem .5rem;
        border-radius: 8px;
        text-transform: uppercase;
        box-shadow: 0 2px 6px rgba(37,99,235,.35);
    }
    .lmo-perf .day-cell {
        min-height: 148px;
        border-right: 1px solid rgba(226,232,240,.9);
        border-bottom: 1px solid rgba(226,232,240,.9);
        padding: .75rem .85rem;
        position: relative;
        background: #fff;
        transition: background .25s ease, transform .2s ease, box-shadow .25s ease;
        vertical-align: top;
    }
    .lmo-perf .roster-calendar-table tbody td.day-cell:last-child { border-right: 0; }
    .lmo-perf .day-cell.state-good {
        background: linear-gradient(145deg, #f0fdf4 0%, #dcfce7 100%);
    }
    .lmo-perf .day-cell.state-good:hover {
        transform: scale(1.02);
        z-index: 2;
        box-shadow: 0 8px 20px rgba(0,0,0,.08);
    }
    .lmo-perf .day-cell.state-neutral { background: #f8fafc; }
    .lmo-perf .day-cell.selected {
        box-shadow: inset 0 0 0 3px #2563eb, 0 4px 16px rgba(37,99,235,.15);
        background: linear-gradient(145deg, #eff6ff 0%, #dbeafe 100%) !important;
        z-index: 3;
    }
    .lmo-perf .day-cell.selected.state-good {
        background: linear-gradient(145deg, #ecfdf5 0%, #d1fae5 100%) !important;
    }
    .lmo-perf .day-cell.selected:hover {
        box-shadow: inset 0 0 0 3px #2563eb, 0 6px 20px rgba(37,99,235,.2);
    }
    .lmo-perf .day-cell.today-cell {
        box-shadow: inset 0 0 0 3px #2563eb, 0 4px 16px rgba(37,99,235,.15);
        background: linear-gradient(145deg, #eff6ff 0%, #dbeafe 100%) !important;
    }
    .lmo-perf .day-cell.today-cell.state-good {
        background: linear-gradient(145deg, #ecfdf5 0%, #d1fae5 100%) !important;
    }
    .lmo-perf .roster-name-cell {
        position: sticky;
        left: 0;
        z-index: 5;
        background: #fff !important;
        font-weight: 600;
        color: var(--kt-text);
        padding: .85rem 1.25rem !important;
        border-right: 1px solid rgba(226,232,240,.9) !important;
        min-width: 200px;
        vertical-align: middle !important;
    }
    .lmo-perf .roster-calendar-table tbody tr:hover .roster-name-cell { background: #f8fafc !important; }
    .lmo-perf .roster-name-cell .site-tag { font-size: .7rem; color: #64748b; font-weight: 500; margin-top: 2px; }
    .lmo-perf .roster-calendar-table { width: 100%; border-collapse: collapse; margin: 0; min-width: 900px; }
    .lmo-perf .roster-calendar-table thead th { background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%); }
    /* Isi sel: list lokasi (sama konsep day-center) */
    .lmo-perf .day-cell .roster-day-inner {
        display: block;
        min-height: 100px;
        text-align: left;
    }
    .lmo-perf .day-cell .roster-day-inner.empty {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100px;
    }
    .lmo-perf .day-cell .lokasi-list { list-style: none; margin: 0; padding: 0; font-size: .7rem; line-height: 1.5; color: #334155; }
    .lmo-perf .day-cell .lokasi-list .lokasi-item {
        padding: .3rem 0;
        border-bottom: 1px solid rgba(226,232,240,.6);
    }
    .lmo-perf .day-cell .lokasi-list .lokasi-item:last-child { border-bottom: 0; }
    .lmo-perf .day-cell .lokasi-list .lokasi-nama {
        font-weight: 700;
        color: var(--kt-text);
        display: block;
        margin-bottom: 2px;
    }
    .lmo-perf .day-cell .lokasi-list .lokasi-detail {
        color: #64748b;
        font-size: .65rem;
    }
    .lmo-perf .day-cell .lokasi-list .lokasi-detail span { display: block; margin-top: 1px; }
    .lmo-perf .day-cell .lokasi-list .kode-badge {
        display: inline-block;
        font-size: .6rem;
        font-weight: 700;
        color: #475569;
        background: #f1f5f9;
        padding: .2rem .45rem;
        border-radius: 6px;
        margin-bottom: 4px;
    }
    .lmo-perf .day-cell .roster-empty-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #cbd5e1;
        opacity: .5;
    }
    .lmo-perf .card-header-roster {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .75rem;
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid var(--kt-border);
    }
    .lmo-perf .card-header-roster .title { font-size: 1.1rem; font-weight: 700; color: var(--kt-text); letter-spacing: -.02em; }
    .lmo-perf .card-header-roster .badge-count {
        font-size: .75rem;
        font-weight: 600;
        color: #64748b;
        background: #f8fafc;
        padding: .35rem .75rem;
        border-radius: 999px;
        border: 1px solid var(--kt-border);
    }
    .lmo-perf .roster-empty-row td {
        padding: 2.5rem 1rem !important;
        text-align: center;
        color: #64748b;
        font-size: .9rem;
    }
    .tab-pane { animation: fadeIn 0.35s ease; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @media (max-width: 768px) {
        .lmo-perf .roster-calendar-table { min-width: 840px; }
        .roster-period-card .period-range { font-size: 1rem; }
    }
</style>
@endsection

@section('content')
<x-page-title title="Master Roster Weekly" pagetitle="Mapping Roster per Site & Tabel" />

@php
    $todayStr = \Carbon\Carbon::today()->toDateString();
    $thisWeekStart = \Carbon\Carbon::today()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString();
    $isCurrentWeek = $startOfWeek->toDateString() === $thisWeekStart;
@endphp
<div class="row mb-2">
    <div class="col-12">
        <div class="roster-period-card d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
            <div>
                <div class="period-label">Periode Mingguan</div>
                <div class="period-range">{{ $startOfWeek->format('d M Y') }} — {{ $endOfWeek->format('d M Y') }}</div>
            </div>
            <div class="roster-week-nav">
                <a href="{{ route('sistem-roster.master-roster.index', ['start_date' => $startOfWeek->copy()->subWeek()->toDateString()]) }}" class="btn-week">
                    <i class="bx bx-chevron-left me-1"></i> Minggu lalu
                </a>
                <a href="{{ route('sistem-roster.master-roster.index') }}" class="btn-week {{ $isCurrentWeek ? 'btn-current' : '' }}">
                    Minggu ini
                </a>
                <a href="{{ route('sistem-roster.master-roster.index', ['start_date' => $startOfWeek->copy()->addWeek()->toDateString()]) }}" class="btn-week">
                    Minggu depan <i class="bx bx-chevron-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <ul class="nav nav-tabs nav-tabs-roster" id="rosterTab" role="tablist">
            @foreach($weeklyData as $key => $conf)
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if($loop->first) active @endif"
                            id="tab-{{ $key }}"
                            data-bs-toggle="tab"
                            data-bs-target="#pane-{{ $key }}"
                            type="button"
                            role="tab"
                            aria-controls="pane-{{ $key }}"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                        {{ $conf['label'] }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="tab-content" id="rosterTabContent">
            @foreach($weeklyData as $key => $conf)
                @php
                    /** @var \Illuminate\Support\Collection $byNama */
                    $byNama = $conf['data'];
                @endphp
                <div class="tab-pane fade @if($loop->first) show active @endif" id="pane-{{ $key }}" role="tabpanel" aria-labelledby="tab-{{ $key }}">
                    <div class="lmo-perf">
                        <section class="card-panel calendar-card mb-4">
                            <div class="card-header-roster">
                                <span class="title">{{ $conf['label'] }} — Weekly Mapping</span>
                                <span class="badge-count">{{ $byNama->count() }} karyawan</span>
                            </div>

                            @if(isset($conf['error']))
                                <div class="alert alert-warning rounded-0 mb-0 border-0 border-bottom">
                                    <small>Gagal mengambil data dari tabel <code>{{ $key }}</code>: {{ $conf['error'] }}</small>
                                </div>
                            @endif

                            <div class="calendar-scroll">
                                <table class="roster-calendar-table">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            @foreach($weekDays as $day)
                                                @php
                                                    $isWeekend = $day->isWeekend();
                                                    $isToday = $day->toDateString() === $todayStr;
                                                @endphp
                                                <th class="{{ $isWeekend ? 'weekend' : '' }} {{ $isToday ? 'today-col' : '' }}">
                                                    <span class="day-name">{{ $day->translatedFormat('D') }}</span>
                                                    <span class="day-date">{{ $day->format('d M') }}</span>
                                                    @if($isToday)
                                                        <span class="current-pill" style="position: static; display: inline-block; margin-top: 4px;">Hari ini</span>
                                                    @endif
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($byNama as $nama => $byDate)
                                            <tr>
                                                <td class="roster-name-cell">
                                                    <span>{{ $nama }}</span>
                                                    @php $any = $byDate->first(); @endphp
                                                    @if($any && !empty($any->site))
                                                        <div class="site-tag">Site: {{ $any->site }}</div>
                                                    @endif
                                                </td>
                                                @foreach($weekDays as $day)
                                                    @php
                                                        $dateKey = $day->toDateString();
                                                        $row = $byDate[$dateKey] ?? null;
                                                        $isWeekend = $day->isWeekend();
                                                        $isToday = $day->toDateString() === $todayStr;
                                                        $hasData = $row !== null;
                                                    @endphp
                                                    <td class="day-cell {{ $hasData ? 'state-good' : 'state-neutral' }} {{ $isWeekend ? 'weekend' : '' }} {{ $isToday ? 'today-cell selected' : '' }}">
                                                        <div class="roster-day-inner {{ $row ? 'has-data' : 'empty' }}">
                                                            @if($row)
                                                                <ul class="lokasi-list">
                                                                    <li class="lokasi-item">
                                                                        @if($row->kode_roster ?? $row->kode_lokasi ?? null)
                                                                            <span class="kode-badge">{{ $row->kode_roster ?? $row->kode_lokasi }}</span>
                                                                        @endif
                                                                        <span class="lokasi-nama">{{ $row->lokasi ?? 'Lokasi' }}</span>
                                                                        <div class="lokasi-detail">
                                                                            @if(!empty($row->sublokasi))
                                                                                <span>Sub: {{ Str::limit($row->sublokasi, 30) }}</span>
                                                                            @endif
                                                                            @if(!empty($row->shift))
                                                                                <span>Shift: {{ $row->shift }}</span>
                                                                            @endif
                                                                            @if(!empty($row->safety))
                                                                                <span>Safety: {{ $row->safety }}</span>
                                                                            @endif
                                                                        </div>
                                                                    </li>
                                                                </ul>
                                                            @else
                                                                <span class="roster-empty-dot"></span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @empty
                                            <tr class="roster-empty-row">
                                                <td colspan="{{ 1 + $weekDays->count() }}">
                                                    Belum ada data roster pada minggu ini untuk tabel {{ $conf['label'] }}.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

