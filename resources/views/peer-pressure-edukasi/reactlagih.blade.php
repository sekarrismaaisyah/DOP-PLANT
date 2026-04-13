@extends('layouts.master')

@section('title', 'Incident Back Analysis')

@section('css')
<style>
  .iba-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; }
  .iba-title { font-size:11px; text-transform:uppercase; letter-spacing:.08em; font-weight:700; color:#475569; }
  .iba-value { font-weight:800; color:#0f172a; }
  .iba-grid { display:grid; gap:12px; }
  .iba-scrollbar-hide::-webkit-scrollbar { display:none; }
  .iba-scrollbar-hide { -ms-overflow-style:none; scrollbar-width:none; }
</style>
@endsection

@section('content')
<x-page-title title="Peer Pressure Edukasi" pagetitle="Incident Back Analysis Tool" />

<div class="row g-3">
  <div class="col-12">
    <div class="iba-card p-3 p-md-4">
      <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
        <div>
          <div class="iba-title">Incident Back Analysis</div>
          <h4 class="mb-1">Statistical Monitoring Dashboard</h4>
          <p class="mb-0 text-muted small">Versi Blade HTML/CSS/JS tanpa React.</p>
        </div>
        <div class="d-flex gap-2 align-items-end">
          <div>
            <label class="form-label mb-1 small fw-semibold">Site</label>
            <select id="iba-site" class="form-select form-select-sm"></select>
          </div>
          <button id="iba-reset" class="btn btn-sm btn-outline-secondary">Reset</button>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-8">
    <div class="iba-card p-3">
      <div class="iba-title mb-2">Statistical Score Trend</div>
      <div style="height:320px"><canvas id="iba-trend-chart"></canvas></div>
    </div>
  </div>

  <div class="col-12 col-xl-4 iba-grid">
    <div class="iba-card p-3">
      <div class="iba-title">Average Score</div>
      <div id="iba-avg-score" class="iba-value fs-3">0</div>
    </div>
    <div class="iba-card p-3">
      <div class="iba-title">Peak Week</div>
      <div id="iba-peak-week" class="iba-value fs-5">-</div>
    </div>
    <div class="iba-card p-3">
      <div class="iba-title">Latest Week</div>
      <div id="iba-latest-week" class="iba-value fs-5">-</div>
    </div>
  </div>

  <div class="col-12">
    <div class="iba-card p-3">
      <div class="iba-title mb-2">Weekly Detail</div>
      <div class="table-responsive iba-scrollbar-hide">
        <table class="table table-sm align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Week</th>
              <th class="text-end">Hazard</th>
              <th class="text-end">TBC</th>
              <th class="text-end">Blindspot</th>
              <th class="text-end">Coverage %</th>
              <th class="text-end">Actual Incident</th>
              <th class="text-end">Score</th>
            </tr>
          </thead>
          <tbody id="iba-tbody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(() => {
  const SITE_ROWS = {
    "All Site": [
      { week:"W40", hazard:23980, tbc:8830, blindspotTbc:24, coverageArea:73.2, actualIncidents:0 },
      { week:"W41", hazard:23640, tbc:8650, blindspotTbc:28, coverageArea:72.1, actualIncidents:1 },
      { week:"W42", hazard:23410, tbc:8510, blindspotTbc:31, coverageArea:71.4, actualIncidents:0 },
      { week:"W43", hazard:23120, tbc:8440, blindspotTbc:34, coverageArea:70.9, actualIncidents:1 },
      { week:"W44", hazard:22840, tbc:8335, blindspotTbc:39, coverageArea:69.7, actualIncidents:2 },
      { week:"W45", hazard:22680, tbc:8280, blindspotTbc:43, coverageArea:68.4, actualIncidents:2 },
      { week:"W46", hazard:22490, tbc:8210, blindspotTbc:46, coverageArea:67.6, actualIncidents:3 },
      { week:"W47", hazard:22720, tbc:8290, blindspotTbc:37, coverageArea:69.1, actualIncidents:1 },
      { week:"W48", hazard:22950, tbc:8355, blindspotTbc:35, coverageArea:69.8, actualIncidents:1 },
      { week:"W49", hazard:23280, tbc:8480, blindspotTbc:32, coverageArea:70.5, actualIncidents:0 },
      { week:"W50", hazard:22600, tbc:8190, blindspotTbc:42, coverageArea:67.8, actualIncidents:1 },
      { week:"W51", hazard:23540, tbc:8595, blindspotTbc:29, coverageArea:71.3, actualIncidents:0 }
    ],
    "LMO": [
      { week:"W40", hazard:7210, tbc:2520, blindspotTbc:9, coverageArea:75.0, actualIncidents:0 },
      { week:"W41", hazard:7050, tbc:2440, blindspotTbc:11, coverageArea:73.9, actualIncidents:1 },
      { week:"W42", hazard:6970, tbc:2400, blindspotTbc:12, coverageArea:73.0, actualIncidents:0 },
      { week:"W43", hazard:6900, tbc:2370, blindspotTbc:14, coverageArea:72.2, actualIncidents:1 },
      { week:"W44", hazard:6840, tbc:2345, blindspotTbc:16, coverageArea:71.6, actualIncidents:1 },
      { week:"W45", hazard:6760, tbc:2310, blindspotTbc:17, coverageArea:70.7, actualIncidents:2 }
    ],
    "SMO": [
      { week:"W40", hazard:7945, tbc:2790, blindspotTbc:8, coverageArea:73.6, actualIncidents:0 },
      { week:"W41", hazard:7890, tbc:2765, blindspotTbc:9, coverageArea:73.0, actualIncidents:0 },
      { week:"W42", hazard:7750, tbc:2705, blindspotTbc:11, coverageArea:71.8, actualIncidents:1 },
      { week:"W43", hazard:7640, tbc:2670, blindspotTbc:13, coverageArea:70.9, actualIncidents:1 },
      { week:"W44", hazard:7520, tbc:2620, blindspotTbc:14, coverageArea:69.9, actualIncidents:2 },
      { week:"W45", hazard:7445, tbc:2590, blindspotTbc:16, coverageArea:69.0, actualIncidents:2 }
    ],
    "GMO": [
      { week:"W40", hazard:7130, tbc:2520, blindspotTbc:7, coverageArea:71.5, actualIncidents:0 },
      { week:"W41", hazard:7010, tbc:2485, blindspotTbc:8, coverageArea:70.8, actualIncidents:1 },
      { week:"W42", hazard:6950, tbc:2460, blindspotTbc:9, coverageArea:70.0, actualIncidents:0 },
      { week:"W43", hazard:6880, tbc:2435, blindspotTbc:10, coverageArea:69.6, actualIncidents:0 },
      { week:"W44", hazard:6795, tbc:2400, blindspotTbc:11, coverageArea:68.8, actualIncidents:1 },
      { week:"W45", hazard:6735, tbc:2375, blindspotTbc:12, coverageArea:68.2, actualIncidents:1 }
    ]
  };

  const siteSelect = document.getElementById('iba-site');
  const tbody = document.getElementById('iba-tbody');
  const avgEl = document.getElementById('iba-avg-score');
  const peakEl = document.getElementById('iba-peak-week');
  const latestEl = document.getElementById('iba-latest-week');
  const resetBtn = document.getElementById('iba-reset');
  let trendChart = null;

  function scoreOf(row) {
    const hazardNorm = row.hazard / 300;
    const tbcNorm = row.tbc / 120;
    const blindspotNorm = row.blindspotTbc * 1.5;
    const incidentNorm = row.actualIncidents * 8;
    const coveragePenalty = Math.max(0, 80 - row.coverageArea);
    return Math.min(100, Math.max(0, hazardNorm + tbcNorm + blindspotNorm + incidentNorm + coveragePenalty));
  }

  function render(site) {
    const rows = (SITE_ROWS[site] || []).map(r => ({ ...r, score: scoreOf(r) }));
    tbody.innerHTML = rows.map(r => `
      <tr>
        <td>${r.week}</td>
        <td class="text-end">${r.hazard.toLocaleString('en-US')}</td>
        <td class="text-end">${r.tbc.toLocaleString('en-US')}</td>
        <td class="text-end">${r.blindspotTbc.toLocaleString('en-US')}</td>
        <td class="text-end">${r.coverageArea.toFixed(1)}</td>
        <td class="text-end">${r.actualIncidents}</td>
        <td class="text-end fw-bold">${r.score.toFixed(1)}</td>
      </tr>
    `).join('');

    const avg = rows.length ? rows.reduce((s, r) => s + r.score, 0) / rows.length : 0;
    const peak = rows.reduce((m, r) => r.score > m.score ? r : m, rows[0] || { week: '-', score: 0 });
    const latest = rows[rows.length - 1] || { week: '-', score: 0 };

    avgEl.textContent = avg.toFixed(1);
    peakEl.textContent = `${peak.week} (${peak.score.toFixed(1)})`;
    latestEl.textContent = `${latest.week} (${latest.score.toFixed(1)})`;

    const labels = rows.map(r => r.week);
    const scores = rows.map(r => r.score);
    const actuals = rows.map(r => r.actualIncidents);
    if (trendChart) trendChart.destroy();
    trendChart = new Chart(document.getElementById('iba-trend-chart').getContext('2d'), {
      type: 'line',
      data: {
        labels,
        datasets: [
          { label: 'Statistical Score', data: scores, borderColor: '#0f172a', backgroundColor: 'rgba(15,23,42,.06)', tension: .3, yAxisID: 'y' },
          { label: 'Actual Incident', data: actuals, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,.06)', tension: .25, yAxisID: 'y1' }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
        scales: {
          y: { min: 0, max: 100, grid: { color: 'rgba(148,163,184,.2)' } },
          y1: { position: 'right', min: 0, suggestedMax: 4, grid: { display: false } }
        }
      }
    });
  }

  Object.keys(SITE_ROWS).forEach(site => {
    const opt = document.createElement('option');
    opt.value = site;
    opt.textContent = site;
    siteSelect.appendChild(opt);
  });

  siteSelect.value = 'All Site';
  siteSelect.addEventListener('change', () => render(siteSelect.value));
  resetBtn.addEventListener('click', () => {
    siteSelect.value = 'All Site';
    render('All Site');
  });

  render('All Site');
})();
</script>
@endsection
import React, { useEffect, useMemo, useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import {
  AlertTriangle,
  ShieldCheck,
  FileText,
  MapPinned,
  Users,
  Eye,
  Activity,
  TrendingUp,
  BarChart3,
  Target,
  Gauge,
  LayoutDashboard,
  Sigma,
  BrainCircuit,
} from 'lucide-react';
import {
  ResponsiveContainer,
  LineChart,
  Line,
  CartesianGrid,
  XAxis,
  YAxis,
  Tooltip,
  ReferenceLine,
  BarChart,
  Bar,
  Cell,
} from 'recharts';

const FEATURE_META = [
  {
    key: 'blindspotTbc',
    label: 'Blindspot TBC',
    description: 'Area risiko yang belum cukup tertangkap atau ditindaklanjuti.',
    icon: Eye,
  },
  {
    key: 'coverageArea',
    label: 'Daily Coverage Area',
    description: 'Jangkauan pengawasan terhadap area kritikal operasi.',
    icon: MapPinned,
  },
  {
    key: 'goldenRules',
    label: 'Golden Rules',
    description: 'Disiplin terhadap kontrol kritikal dan aturan fatal risk.',
    icon: Activity,
  },
  {
    key: 'hazard',
    label: 'Pelaporan Hazard',
    description: 'Sensitivitas identifikasi deviasi dan hazard lapangan.',
    icon: FileText,
  },
  {
    key: 'tbc',
    label: 'Pelaporan TBC',
    description: 'Kemampuan mengangkat concern hazard signifikan.',
    icon: AlertTriangle,
  },
  {
    key: 'rfidSupervisor',
    label: 'RFID Pengawas',
    description: 'Kapasitas presence pengawasan di lapangan.',
    icon: ShieldCheck,
  },
  {
    key: 'ratioNonToSupervisor',
    label: 'Rasio Non Pengawas : Pengawas',
    description: 'Ketimpangan antara eksposur aktivitas dan kapasitas pengawasan.',
    icon: Users,
  },
];

const SITE_ROWS = {
  'All Site': [
    { week: 'W40', actualIncidents: 0, hazard: 23980, rfidNonSupervisor: 13180, rfidSupervisor: 4175, tbc: 8830, goldenRules: 3, blindspotTbc: 24, coverageArea: 73.2 },
    { week: 'W41', actualIncidents: 1, hazard: 23640, rfidNonSupervisor: 13260, rfidSupervisor: 4090, tbc: 8650, goldenRules: 2, blindspotTbc: 28, coverageArea: 72.1 },
    { week: 'W42', actualIncidents: 0, hazard: 23410, rfidNonSupervisor: 13310, rfidSupervisor: 4045, tbc: 8510, goldenRules: 2, blindspotTbc: 31, coverageArea: 71.4 },
    { week: 'W43', actualIncidents: 1, hazard: 23120, rfidNonSupervisor: 13395, rfidSupervisor: 3980, tbc: 8440, goldenRules: 2, blindspotTbc: 34, coverageArea: 70.9 },
    { week: 'W44', actualIncidents: 2, hazard: 22840, rfidNonSupervisor: 13490, rfidSupervisor: 3920, tbc: 8335, goldenRules: 1, blindspotTbc: 39, coverageArea: 69.7 },
    { week: 'W45', actualIncidents: 2, hazard: 22680, rfidNonSupervisor: 13540, rfidSupervisor: 3895, tbc: 8280, goldenRules: 1, blindspotTbc: 43, coverageArea: 68.4 },
    { week: 'W46', actualIncidents: 3, hazard: 22490, rfidNonSupervisor: 13620, rfidSupervisor: 3845, tbc: 8210, goldenRules: 1, blindspotTbc: 46, coverageArea: 67.6 },
    { week: 'W47', actualIncidents: 1, hazard: 22720, rfidNonSupervisor: 13510, rfidSupervisor: 3905, tbc: 8290, goldenRules: 2, blindspotTbc: 37, coverageArea: 69.1 },
    { week: 'W48', actualIncidents: 1, hazard: 22950, rfidNonSupervisor: 13420, rfidSupervisor: 3940, tbc: 8355, goldenRules: 2, blindspotTbc: 35, coverageArea: 69.8 },
    { week: 'W49', actualIncidents: 0, hazard: 23280, rfidNonSupervisor: 13360, rfidSupervisor: 3995, tbc: 8480, goldenRules: 2, blindspotTbc: 32, coverageArea: 70.5 },
    { week: 'W50', actualIncidents: 1, hazard: 22600, rfidNonSupervisor: 13480, rfidSupervisor: 3875, tbc: 8190, goldenRules: 1, blindspotTbc: 42, coverageArea: 67.8 },
    { week: 'W51', actualIncidents: 0, hazard: 23540, rfidNonSupervisor: 13295, rfidSupervisor: 4048, tbc: 8595, goldenRules: 2, blindspotTbc: 29, coverageArea: 71.3 },
  ],
  LMO: [
    { week: 'W40', actualIncidents: 0, hazard: 7210, rfidNonSupervisor: 3850, rfidSupervisor: 1228, tbc: 2520, goldenRules: 2, blindspotTbc: 9, coverageArea: 75.0 },
    { week: 'W41', actualIncidents: 1, hazard: 7050, rfidNonSupervisor: 3890, rfidSupervisor: 1200, tbc: 2440, goldenRules: 2, blindspotTbc: 11, coverageArea: 73.9 },
    { week: 'W42', actualIncidents: 0, hazard: 6970, rfidNonSupervisor: 3925, rfidSupervisor: 1188, tbc: 2400, goldenRules: 2, blindspotTbc: 12, coverageArea: 73.0 },
    { week: 'W43', actualIncidents: 1, hazard: 6900, rfidNonSupervisor: 3960, rfidSupervisor: 1168, tbc: 2370, goldenRules: 1, blindspotTbc: 14, coverageArea: 72.2 },
    { week: 'W44', actualIncidents: 1, hazard: 6840, rfidNonSupervisor: 3995, rfidSupervisor: 1149, tbc: 2345, goldenRules: 1, blindspotTbc: 16, coverageArea: 71.6 },
    { week: 'W45', actualIncidents: 2, hazard: 6760, rfidNonSupervisor: 4035, rfidSupervisor: 1128, tbc: 2310, goldenRules: 1, blindspotTbc: 17, coverageArea: 70.7 },
    { week: 'W46', actualIncidents: 2, hazard: 6715, rfidNonSupervisor: 4060, rfidSupervisor: 1110, tbc: 2295, goldenRules: 1, blindspotTbc: 18, coverageArea: 69.8 },
    { week: 'W47', actualIncidents: 1, hazard: 6860, rfidNonSupervisor: 4010, rfidSupervisor: 1150, tbc: 2350, goldenRules: 2, blindspotTbc: 15, coverageArea: 71.2 },
    { week: 'W48', actualIncidents: 0, hazard: 6965, rfidNonSupervisor: 3970, rfidSupervisor: 1174, tbc: 2395, goldenRules: 2, blindspotTbc: 13, coverageArea: 72.6 },
    { week: 'W49', actualIncidents: 0, hazard: 7055, rfidNonSupervisor: 3920, rfidSupervisor: 1191, tbc: 2425, goldenRules: 2, blindspotTbc: 11, coverageArea: 73.4 },
    { week: 'W50', actualIncidents: 1, hazard: 6795, rfidNonSupervisor: 4050, rfidSupervisor: 1122, tbc: 2305, goldenRules: 1, blindspotTbc: 17, coverageArea: 70.1 },
    { week: 'W51', actualIncidents: 0, hazard: 7140, rfidNonSupervisor: 3895, rfidSupervisor: 1210, tbc: 2475, goldenRules: 2, blindspotTbc: 10, coverageArea: 74.0 },
  ],
  SMO: [
    { week: 'W40', actualIncidents: 0, hazard: 7945, rfidNonSupervisor: 4290, rfidSupervisor: 1335, tbc: 2790, goldenRules: 3, blindspotTbc: 8, coverageArea: 73.6 },
    { week: 'W41', actualIncidents: 0, hazard: 7890, rfidNonSupervisor: 4325, rfidSupervisor: 1318, tbc: 2765, goldenRules: 2, blindspotTbc: 9, coverageArea: 73.0 },
    { week: 'W42', actualIncidents: 1, hazard: 7750, rfidNonSupervisor: 4375, rfidSupervisor: 1290, tbc: 2705, goldenRules: 2, blindspotTbc: 11, coverageArea: 71.8 },
    { week: 'W43', actualIncidents: 1, hazard: 7640, rfidNonSupervisor: 4420, rfidSupervisor: 1268, tbc: 2670, goldenRules: 1, blindspotTbc: 13, coverageArea: 70.9 },
    { week: 'W44', actualIncidents: 2, hazard: 7520, rfidNonSupervisor: 4460, rfidSupervisor: 1245, tbc: 2620, goldenRules: 1, blindspotTbc: 14, coverageArea: 69.9 },
    { week: 'W45', actualIncidents: 2, hazard: 7445, rfidNonSupervisor: 4490, rfidSupervisor: 1226, tbc: 2590, goldenRules: 1, blindspotTbc: 16, coverageArea: 69.0 },
    { week: 'W46', actualIncidents: 3, hazard: 7380, rfidNonSupervisor: 4540, rfidSupervisor: 1208, tbc: 2560, goldenRules: 1, blindspotTbc: 18, coverageArea: 68.1 },
    { week: 'W47', actualIncidents: 1, hazard: 7540, rfidNonSupervisor: 4465, rfidSupervisor: 1250, tbc: 2635, goldenRules: 2, blindspotTbc: 14, coverageArea: 70.4 },
    { week: 'W48', actualIncidents: 1, hazard: 7630, rfidNonSupervisor: 4410, rfidSupervisor: 1278, tbc: 2675, goldenRules: 2, blindspotTbc: 12, coverageArea: 71.0 },
    { week: 'W49', actualIncidents: 0, hazard: 7755, rfidNonSupervisor: 4360, rfidSupervisor: 1298, tbc: 2710, goldenRules: 2, blindspotTbc: 11, coverageArea: 71.9 },
    { week: 'W50', actualIncidents: 1, hazard: 7460, rfidNonSupervisor: 4515, rfidSupervisor: 1216, tbc: 2585, goldenRules: 1, blindspotTbc: 16, coverageArea: 68.9 },
    { week: 'W51', actualIncidents: 0, hazard: 7830, rfidNonSupervisor: 4335, rfidSupervisor: 1310, tbc: 2750, goldenRules: 2, blindspotTbc: 9, coverageArea: 72.5 },
  ],
  GMO: [
    { week: 'W40', actualIncidents: 0, hazard: 7130, rfidNonSupervisor: 3890, rfidSupervisor: 1260, tbc: 2520, goldenRules: 2, blindspotTbc: 7, coverageArea: 71.5 },
    { week: 'W41', actualIncidents: 1, hazard: 7010, rfidNonSupervisor: 3920, rfidSupervisor: 1248, tbc: 2485, goldenRules: 2, blindspotTbc: 8, coverageArea: 70.8 },
    { week: 'W42', actualIncidents: 0, hazard: 6950, rfidNonSupervisor: 3960, rfidSupervisor: 1235, tbc: 2460, goldenRules: 2, blindspotTbc: 9, coverageArea: 70.0 },
    { week: 'W43', actualIncidents: 0, hazard: 6880, rfidNonSupervisor: 4010, rfidSupervisor: 1218, tbc: 2435, goldenRules: 2, blindspotTbc: 10, coverageArea: 69.6 },
    { week: 'W44', actualIncidents: 1, hazard: 6795, rfidNonSupervisor: 4035, rfidSupervisor: 1204, tbc: 2400, goldenRules: 1, blindspotTbc: 11, coverageArea: 68.8 },
    { week: 'W45', actualIncidents: 1, hazard: 6735, rfidNonSupervisor: 4065, rfidSupervisor: 1190, tbc: 2375, goldenRules: 1, blindspotTbc: 12, coverageArea: 68.2 },
    { week: 'W46', actualIncidents: 2, hazard: 6660, rfidNonSupervisor: 4095, rfidSupervisor: 1176, tbc: 2355, goldenRules: 1, blindspotTbc: 13, coverageArea: 67.4 },
    { week: 'W47', actualIncidents: 1, hazard: 6780, rfidNonSupervisor: 4040, rfidSupervisor: 1201, tbc: 2405, goldenRules: 2, blindspotTbc: 10, coverageArea: 68.9 },
    { week: 'W48', actualIncidents: 0, hazard: 6865, rfidNonSupervisor: 3995, rfidSupervisor: 1220, tbc: 2440, goldenRules: 2, blindspotTbc: 9, coverageArea: 69.7 },
    { week: 'W49', actualIncidents: 0, hazard: 6940, rfidNonSupervisor: 3960, rfidSupervisor: 1232, tbc: 2470, goldenRules: 2, blindspotTbc: 8, coverageArea: 70.1 },
    { week: 'W50', actualIncidents: 1, hazard: 6705, rfidNonSupervisor: 4088, rfidSupervisor: 1185, tbc: 2370, goldenRules: 1, blindspotTbc: 12, coverageArea: 67.9 },
    { week: 'W51', actualIncidents: 0, hazard: 7040, rfidNonSupervisor: 3925, rfidSupervisor: 1244, tbc: 2492, goldenRules: 2, blindspotTbc: 8, coverageArea: 70.8 },
  ],
};

const DEFAULT_LOOKBACK = 6;
const RIDGE_ALPHA = 1.2;
const DEFAULT_ALERT_THRESHOLD = 30;

function fmt(num, digits = 2) {
  if (!Number.isFinite(num)) return '-';
  return Number(num).toLocaleString('en-US', {
    minimumFractionDigits: digits,
    maximumFractionDigits: digits,
  });
}

function mean(values) {
  if (!values.length) return 0;
  return values.reduce((sum, value) => sum + value, 0) / values.length;
}

function std(values) {
  if (values.length <= 1) return 1;
  const avg = mean(values);
  const variance = values.reduce((sum, value) => sum + (value - avg) ** 2, 0) / (values.length - 1);
  return Math.sqrt(variance) || 1;
}

function median(values) {
  if (!values.length) return 0;
  const sorted = [...values].sort((a, b) => a - b);
  const middle = Math.floor(sorted.length / 2);
  return sorted.length % 2 === 0 ? (sorted[middle - 1] + sorted[middle]) / 2 : sorted[middle];
}

function quantile(values, q) {
  if (!values.length) return 0;
  const sorted = [...values].sort((a, b) => a - b);
  const position = clamp(q, 0, 1) * (sorted.length - 1);
  const base = Math.floor(position);
  const rest = position - base;
  if (sorted[base + 1] !== undefined) {
    return sorted[base] + rest * (sorted[base + 1] - sorted[base]);
  }
  return sorted[base];
}

function clamp(value, min, max) {
  return Math.min(max, Math.max(min, value));
}

function toFiniteNumber(value, fallback = 0) {
  const parsed = Number(value);
  return Number.isFinite(parsed) ? parsed : fallback;
}

function enrichRow(row) {
  const hazard = toFiniteNumber(row.hazard);
  const rfidNonSupervisor = toFiniteNumber(row.rfidNonSupervisor);
  const rfidSupervisor = Math.max(toFiniteNumber(row.rfidSupervisor), 0);
  const tbc = toFiniteNumber(row.tbc);
  const goldenRules = toFiniteNumber(row.goldenRules);
  const blindspotTbc = toFiniteNumber(row.blindspotTbc);
  const coverageArea = toFiniteNumber(row.coverageArea);
  const actualIncidents = Math.max(toFiniteNumber(row.actualIncidents), 0);
  const ratioNonToSupervisor = rfidSupervisor > 0 ? rfidNonSupervisor / rfidSupervisor : 0;

  return {
    ...row,
    hazard,
    rfidNonSupervisor,
    rfidSupervisor,
    tbc,
    goldenRules,
    blindspotTbc,
    coverageArea,
    actualIncidents,
    ratioNonToSupervisor,
  };
}

function getFeatureValue(row, key) {
  return toFiniteNumber(row[key], 0);
}

function solveLinearSystem(matrix, vector) {
  const n = matrix.length;
  const augmented = matrix.map((row, rowIndex) => [...row, vector[rowIndex]]);

  for (let col = 0; col < n; col += 1) {
    let pivotRow = col;
    for (let row = col + 1; row < n; row += 1) {
      if (Math.abs(augmented[row][col]) > Math.abs(augmented[pivotRow][col])) {
        pivotRow = row;
      }
    }

    if (Math.abs(augmented[pivotRow][col]) < 1e-10) {
      augmented[col][col] = 1e-10;
    } else if (pivotRow !== col) {
      [augmented[col], augmented[pivotRow]] = [augmented[pivotRow], augmented[col]];
    }

    const pivot = augmented[col][col];
    for (let j = col; j <= n; j += 1) {
      augmented[col][j] /= pivot;
    }

    for (let row = 0; row < n; row += 1) {
      if (row === col) continue;
      const factor = augmented[row][col];
      for (let j = col; j <= n; j += 1) {
        augmented[row][j] -= factor * augmented[col][j];
      }
    }
  }

  return augmented.map((row) => row[n]);
}

function fitStatisticalModel(rows) {
  const enrichedRows = rows.map(enrichRow);
  const featureKeys = FEATURE_META.map((feature) => feature.key);
  const xRaw = enrichedRows.map((row) => featureKeys.map((key) => getFeatureValue(row, key)));
  const yRaw = enrichedRows.map((row) => row.actualIncidents);

  const xMeans = featureKeys.map((_, index) => mean(xRaw.map((row) => row[index])));
  const xStds = featureKeys.map((_, index) => {
    const value = std(xRaw.map((row) => row[index]));
    return value > 0 ? value : 1;
  });
  const yMean = mean(yRaw);
  const yStd = std(yRaw) || 1;

  const xStandardized = xRaw.map((row) => row.map((value, index) => (value - xMeans[index]) / xStds[index]));
  const yStandardized = yRaw.map((value) => (value - yMean) / yStd);

  const dimension = featureKeys.length;
  const xtx = Array.from({ length: dimension }, () => Array.from({ length: dimension }, () => 0));
  const xty = Array.from({ length: dimension }, () => 0);

  for (let i = 0; i < xStandardized.length; i += 1) {
    for (let j = 0; j < dimension; j += 1) {
      xty[j] += xStandardized[i][j] * yStandardized[i];
      for (let k = 0; k < dimension; k += 1) {
        xtx[j][k] += xStandardized[i][j] * xStandardized[i][k];
      }
    }
  }

  for (let i = 0; i < dimension; i += 1) {
    xtx[i][i] += RIDGE_ALPHA;
  }

  const betas = solveLinearSystem(xtx, xty).map((value) => (Number.isFinite(value) ? value : 0));

  function predict(rawRow) {
    const enrichedRow = enrichRow(rawRow);
    const standardizedFeatures = featureKeys.map((key, index) => (getFeatureValue(enrichedRow, key) - xMeans[index]) / xStds[index]);
    const yStandardizedHat = standardizedFeatures.reduce((sum, value, index) => sum + value * betas[index], 0);
    const predictedIncidents = Math.max(yMean + yStandardizedHat * yStd, 0);

    return {
      predictedIncidents,
      standardizedFeatures,
      enrichedRow,
    };
  }

  const fitted = enrichedRows.map((row) => {
    const prediction = predict(row);
    return {
      ...row,
      predictedIncidents: prediction.predictedIncidents,
      standardizedFeatures: prediction.standardizedFeatures,
    };
  });

  const fittedPredictions = fitted.map((row) => row.predictedIncidents);
  const minPred = Math.min(...fittedPredictions);
  const maxPred = Math.max(...fittedPredictions);
  const scoreRange = maxPred - minPred || 1;
  const scoreThresholdYellow = quantile(fittedPredictions, 0.5);
  const scoreThresholdRed = quantile(fittedPredictions, 0.8);

  function predictedToScore(predictedIncidents) {
    return clamp(((predictedIncidents - minPred) / scoreRange) * 100, 0, 100);
  }

  function predictedToStatus(predictedIncidents) {
    if (predictedIncidents >= scoreThresholdRed) return 'Merah';
    if (predictedIncidents >= scoreThresholdYellow) return 'Kuning';
    return 'Hijau';
  }

  return {
    featureKeys,
    xMeans,
    xStds,
    yMean,
    yStd,
    betas,
    fitted,
    predict,
    predictedToScore,
    predictedToStatus,
    scoreThresholdYellow,
    scoreThresholdRed,
  };
}

function getBaselineWindow(rows, selectedIndex, lookback) {
  const safeIndex = selectedIndex >= 0 ? selectedIndex : rows.length - 1;
  const safeLookback = clamp(Math.floor(toFiniteNumber(lookback, DEFAULT_LOOKBACK)), 2, Math.max(rows.length - 1, 2));
  const start = Math.max(0, safeIndex - safeLookback);
  let baselineRows = rows.slice(start, safeIndex);

  if (baselineRows.length >= 2) {
    return baselineRows;
  }

  baselineRows = rows.filter((_, index) => index !== safeIndex).slice(0, Math.min(safeLookback, Math.max(rows.length - 1, 0)));
  return baselineRows;
}

function computeBaselineStats(rows, selectedIndex, lookback) {
  const baselineRows = getBaselineWindow(rows, selectedIndex, lookback).map(enrichRow);
  const byFeature = FEATURE_META.reduce((accumulator, feature) => {
    const values = baselineRows.map((row) => getFeatureValue(row, feature.key));
    accumulator[feature.key] = {
      mean: mean(values),
      median: median(values),
      std: std(values) || 1,
    };
    return accumulator;
  }, {});

  return { baselineRows, byFeature };
}

function computeContributionHistory(rows, model, lookback) {
  const history = FEATURE_META.reduce((accumulator, feature) => {
    accumulator[feature.key] = [];
    return accumulator;
  }, {});

  rows.forEach((row, rowIndex) => {
    const baselineStats = computeBaselineStats(rows, rowIndex, lookback);
    FEATURE_META.forEach((feature, featureIndex) => {
      const stats = baselineStats.byFeature[feature.key];
      const zScore = stats.std ? (getFeatureValue(row, feature.key) - stats.mean) / stats.std : 0;
      const contribution = zScore * model.betas[featureIndex];
      history[feature.key].push(Number.isFinite(contribution) ? contribution : 0);
    });
  });

  return history;
}

function contributionStatus(contribution, historyValues) {
  const safeContribution = Number.isFinite(contribution) ? contribution : 0;
  const positiveHistory = historyValues.filter((value) => value > 0);
  const yellowCutoff = positiveHistory.length ? quantile(positiveHistory, 0.5) : 0;
  const redCutoff = positiveHistory.length ? quantile(positiveHistory, 0.8) : 0;

  if (safeContribution <= 0) return { color: 'green', label: 'Hijau' };
  if (safeContribution >= redCutoff && redCutoff > 0) return { color: 'red', label: 'Merah' };
  if (safeContribution >= yellowCutoff && yellowCutoff > 0) return { color: 'yellow', label: 'Kuning' };
  return { color: 'yellow', label: 'Kuning' };
}

function averageRanks(values) {
  const indexed = values.map((value, index) => ({ value, index })).sort((a, b) => a.value - b.value);
  const ranks = new Array(values.length).fill(0);
  let i = 0;

  while (i < indexed.length) {
    let j = i;
    while (j < indexed.length && indexed[j].value === indexed[i].value) {
      j += 1;
    }
    const averageRank = (i + j - 1) / 2 + 1;
    for (let k = i; k < j; k += 1) {
      ranks[indexed[k].index] = averageRank;
    }
    i = j;
  }

  return ranks;
}

function pearsonCorrelation(xs, ys) {
  if (!xs.length || xs.length !== ys.length) return 0;
  const meanX = mean(xs);
  const meanY = mean(ys);
  let numerator = 0;
  let denominatorX = 0;
  let denominatorY = 0;

  for (let i = 0; i < xs.length; i += 1) {
    const dx = xs[i] - meanX;
    const dy = ys[i] - meanY;
    numerator += dx * dy;
    denominatorX += dx * dx;
    denominatorY += dy * dy;
  }

  if (denominatorX === 0 || denominatorY === 0) return 0;
  return numerator / Math.sqrt(denominatorX * denominatorY);
}

function spearmanCorrelation(xs, ys) {
  if (!xs.length || xs.length !== ys.length) return 0;
  return pearsonCorrelation(averageRanks(xs), averageRanks(ys));
}

function aucBinary(scores, labels) {
  if (!scores.length || scores.length !== labels.length) return 0.5;
  const positives = labels.filter((label) => label === 1).length;
  const negatives = labels.filter((label) => label === 0).length;
  if (positives === 0 || negatives === 0) return 0.5;

  const ranks = averageRanks(scores);
  let positiveRankSum = 0;
  for (let i = 0; i < labels.length; i += 1) {
    if (labels[i] === 1) {
      positiveRankSum += ranks[i];
    }
  }

  return (positiveRankSum - (positives * (positives + 1)) / 2) / (positives * negatives);
}

function confusionMetrics(scores, incidents, threshold) {
  const labels = incidents.map((value) => (value >= 1 ? 1 : 0));
  let tp = 0;
  let tn = 0;
  let fp = 0;
  let fn = 0;

  for (let i = 0; i < scores.length; i += 1) {
    const predicted = scores[i] >= threshold ? 1 : 0;
    const actual = labels[i];
    if (predicted === 1 && actual === 1) tp += 1;
    if (predicted === 0 && actual === 0) tn += 1;
    if (predicted === 1 && actual === 0) fp += 1;
    if (predicted === 0 && actual === 1) fn += 1;
  }

  const total = tp + tn + fp + fn;
  const accuracy = total ? (tp + tn) / total : 0;
  const precision = tp + fp ? tp / (tp + fp) : 0;
  const recall = tp + fn ? tp / (tp + fn) : 0;
  const specificity = tn + fp ? tn / (tn + fp) : 0;
  const f1 = precision + recall ? (2 * precision * recall) / (precision + recall) : 0;

  return {
    tp,
    tn,
    fp,
    fn,
    accuracy,
    precision,
    recall,
    specificity,
    f1,
    auc: aucBinary(scores, labels),
  };
}

function badgeClass(color) {
  if (color === 'red') return 'bg-red-100 text-red-700 border-red-200';
  if (color === 'yellow') return 'bg-amber-100 text-amber-700 border-amber-200';
  return 'bg-emerald-100 text-emerald-700 border-emerald-200';
}

function statusColor(status) {
  if (status === 'Merah') return 'red';
  if (status === 'Kuning') return 'yellow';
  return 'green';
}

function chartStatusColor(status) {
  if (status === 'Merah') return '#ef4444';
  if (status === 'Kuning') return '#f59e0b';
  return '#10b981';
}

function TrendTooltip({ active, payload, label }) {
  if (!active || !payload || !payload.length) return null;
  const point = payload[0]?.payload;
  if (!point) return null;

  return (
    <div className="rounded-2xl border border-slate-200 bg-white p-3 text-sm shadow-lg">
      <div className="font-medium text-slate-900">{label}</div>
      <div className="mt-1 text-slate-600">Statistical Score: {fmt(point.score, 1)}</div>
      <div className="text-slate-600">Predicted Incidents: {fmt(point.predictedIncidents, 2)}</div>
      <div className="text-slate-600">Actual Incidents: {fmt(point.actualIncidents, 0)}</div>
      <div className="text-slate-600">Top Driver: {point.topDriver}</div>
    </div>
  );
}

function PageSwitcher({ page, setPage }) {
  return (
    <div className="inline-flex rounded-2xl bg-slate-100 p-1 ring-1 ring-slate-200">
      <Button
        type="button"
        variant="ghost"
        onClick={() => setPage('dashboard')}
        className={`rounded-xl px-4 ${page === 'dashboard' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500'}`}
      >
        <LayoutDashboard className="mr-2 h-4 w-4" />
        Analysis Dashboard
      </Button>
      <Button
        type="button"
        variant="ghost"
        onClick={() => setPage('accuracy')}
        className={`rounded-xl px-4 ${page === 'accuracy' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500'}`}
      >
        <Target className="mr-2 h-4 w-4" />
        Accuracy Check
      </Button>
    </div>
  );
}

function KPIHeader({
  site,
  setSite,
  sites,
  alertThreshold,
  setAlertThreshold,
  summaryTrend,
  resetDemo,
  page,
  setPage,
  lookback,
  setLookback,
}) {
  return (
    <>
      <div className="flex flex-col gap-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 md:flex-row md:items-end md:justify-between">
        <div>
          <div className="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">Incident Back Analysis</div>
          <h1 className="mt-2 text-3xl font-semibold text-slate-900">Fully Statistical Back Analysis Tool</h1>
          <p className="mt-2 max-w-3xl text-sm text-slate-600">
            Baseline dihitung dari rolling historical window, bobot berasal dari koefisien ridge regression terstandarisasi, dan overall score berasal dari predicted incident risk yang dinormalisasi dari model site.
          </p>
        </div>
        <div className="flex flex-col items-start gap-3 md:items-end">
          <PageSwitcher page={page} setPage={setPage} />
          <Button onClick={resetDemo} className="rounded-2xl">Reset demo</Button>
        </div>
      </div>

      <div className="grid gap-4 md:grid-cols-6">
        <Card className="rounded-3xl border-0 shadow-sm ring-1 ring-slate-200 md:col-span-1">
          <CardContent className="p-5">
            <div className="text-xs uppercase tracking-[0.2em] text-slate-500">Site</div>
            <div className="mt-3">
              <Select value={site} onValueChange={setSite}>
                <SelectTrigger className="rounded-2xl">
                  <SelectValue placeholder="Pilih site" />
                </SelectTrigger>
                <SelectContent>
                  {sites.map((siteName) => (
                    <SelectItem key={siteName} value={siteName}>{siteName}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
          </CardContent>
        </Card>

        <Card className="rounded-3xl border-0 shadow-sm ring-1 ring-slate-200 md:col-span-1">
          <CardContent className="p-5">
            <div className="text-xs uppercase tracking-[0.2em] text-slate-500">Lookback baseline</div>
            <div className="mt-3">
              <Input
                type="number"
                min={2}
                max={10}
                value={lookback}
                onChange={(event) => setLookback(clamp(Math.floor(toFiniteNumber(event.target.value, DEFAULT_LOOKBACK)), 2, 10))}
                className="rounded-2xl"
              />
            </div>
            <div className="mt-2 text-xs text-slate-500">Jumlah minggu historis untuk rolling reference.</div>
          </CardContent>
        </Card>

        <Card className="rounded-3xl border-0 shadow-sm ring-1 ring-slate-200 md:col-span-1">
          <CardContent className="p-5">
            <div className="text-xs uppercase tracking-[0.2em] text-slate-500">Alert threshold</div>
            <div className="mt-3">
              <Input
                type="number"
                min={0}
                max={100}
                value={alertThreshold}
                onChange={(event) => setAlertThreshold(clamp(toFiniteNumber(event.target.value, DEFAULT_ALERT_THRESHOLD), 0, 100))}
                className="rounded-2xl"
              />
            </div>
            <div className="mt-2 text-xs text-slate-500">Dipakai hanya untuk klasifikasi di accuracy check.</div>
          </CardContent>
        </Card>

        <Card className="rounded-3xl border-0 shadow-sm ring-1 ring-slate-200 md:col-span-1">
          <CardContent className="p-5">
            <div className="text-xs uppercase tracking-[0.2em] text-slate-500">Average score</div>
            <div className="mt-2 text-3xl font-semibold text-slate-900">{fmt(summaryTrend.avg, 1)}</div>
            <div className="mt-1 text-sm text-slate-500">Mean statistical risk score</div>
          </CardContent>
        </Card>

        <Card className="rounded-3xl border-0 shadow-sm ring-1 ring-slate-200 md:col-span-1">
          <CardContent className="p-5">
            <div className="text-xs uppercase tracking-[0.2em] text-slate-500">Peak week</div>
            <div className="mt-2 text-3xl font-semibold text-slate-900">{summaryTrend.peak.week}</div>
            <div className="mt-1 text-sm text-slate-500">Score {fmt(summaryTrend.peak.score, 1)} · {summaryTrend.peak.status}</div>
          </CardContent>
        </Card>

        <Card className="rounded-3xl border-0 shadow-sm ring-1 ring-slate-200 md:col-span-1">
          <CardContent className="p-5">
            <div className="text-xs uppercase tracking-[0.2em] text-slate-500">Latest week</div>
            <div className="mt-2 text-3xl font-semibold text-slate-900">{summaryTrend.latest.week}</div>
            <div className="mt-1 text-sm text-slate-500">Score {fmt(summaryTrend.latest.score, 1)} · {summaryTrend.latest.status}</div>
          </CardContent>
        </Card>
      </div>
    </>
  );
}

function AccuracyPage({ weeklySeries, alertThreshold, sameWeekMetrics, nextWeekMetrics }) {
  return (
    <div className="space-y-6">
      <div className="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
        <Card className="rounded-3xl border-0 shadow-sm ring-1 ring-slate-200">
          <CardHeader>
            <CardTitle className="flex items-center gap-2 text-xl">
              <TrendingUp className="h-5 w-5 text-slate-700" />
              Overlay score statistik vs insiden aktual
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-6">
            <div className="h-[360px] w-full rounded-3xl bg-slate-50 p-4 ring-1 ring-slate-200">
              <ResponsiveContainer width="100%" height="100%">
                <LineChart data={weeklySeries} margin={{ top: 20, right: 10, left: 0, bottom: 5 }}>
                  <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" />
                  <XAxis dataKey="week" stroke="#64748b" fontSize={12} />
                  <YAxis yAxisId="left" domain={[0, 100]} stroke="#64748b" fontSize={12} />
                  <YAxis yAxisId="right" orientation="right" allowDecimals={false} domain={[0, 'dataMax + 1']} stroke="#64748b" fontSize={12} />
                  <Tooltip content={<TrendTooltip />} />
                  <ReferenceLine yAxisId="left" y={alertThreshold} stroke="#0f172a" strokeDasharray="4 4" />
                  <Line yAxisId="left" type="monotone" dataKey="score" stroke="#0f172a" strokeWidth={3} dot={{ r: 4 }} activeDot={{ r: 6 }} />
                  <Line yAxisId="right" type="monotone" dataKey="actualIncidents" stroke="#2563eb" strokeWidth={3} dot={{ r: 4 }} activeDot={{ r: 6 }} />
                </LineChart>
              </ResponsiveContainer>
            </div>
            <div className="rounded-3xl bg-slate-50 p-5 text-sm text-slate-700 ring-1 ring-slate-200">
              Same-week menilai seberapa baik score statistik menjelaskan minggu yang sedang berjalan. Next-week menilai seberapa baik score itu bekerja sebagai early warning satu minggu ke depan.
            </div>
          </CardContent>
        </Card>

        <Card className="rounded-3xl border-0 shadow-sm ring-1 ring-slate-200">
          <CardHeader>
            <CardTitle className="flex items-center gap-2 text-xl">
              <BarChart3 className="h-5 w-5 text-slate-700" />
              Tabel alert vs aktual
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="overflow-x-auto">
              <table className="min-w-full text-left text-sm">
                <thead className="border-b border-slate-200 text-slate-500">
                  <tr>
                    <th className="px-4 py-3">Week</th>
                    <th className="px-4 py-3">Score</th>
                    <th className="px-4 py-3">Pred. Inc</th>
                    <th className="px-4 py-3">Actual</th>
                    <th className="px-4 py-3">Prediksi</th>
                    <th className="px-4 py-3">Status</th>
                  </tr>
                </thead>
                <tbody>
                  {weeklySeries.map((row) => (
                    <tr key={row.week} className="border-b border-slate-100">
                      <td className="px-4 py-4 font-medium text-slate-900">{row.week}</td>
                      <td className="px-4 py-4 text-slate-900">{fmt(row.score, 1)}</td>
                      <td className="px-4 py-4 text-slate-900">{fmt(row.predictedIncidents, 2)}</td>
                      <td className="px-4 py-4 text-slate-900">{fmt(row.actualIncidents, 0)}</td>
                      <td className="px-4 py-4 text-slate-900">{row.score >= alertThreshold ? 'Alert' : 'No Alert'}</td>
                      <td className="px-4 py-4">
                        <Badge className={`rounded-full border px-3 py-1 ${badgeClass(statusColor(row.status))}`}>{row.status}</Badge>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>
      </div>

      <div className="grid gap-6 lg:grid-cols-2">
        <Card className="rounded-3xl border-0 shadow-sm ring-1 ring-slate-200">
          <CardHeader>
            <CardTitle className="flex items-center gap-2 text-xl">
              <Target className="h-5 w-5 text-slate-700" />
              Accuracy check — same week
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-5">
            <div className="grid gap-4 md:grid-cols-3">
              <div className="rounded-3xl bg-slate-900 p-5 text-white">
                <div className="text-xs uppercase tracking-[0.2em] text-slate-300">Pearson</div>
                <div className="mt-2 text-3xl font-semibold">{fmt(sameWeekMetrics.pearson, 2)}</div>
              </div>
              <div className="rounded-3xl bg-white p-5 ring-1 ring-slate-200">
                <div className="text-xs uppercase tracking-[0.2em] text-slate-500">Spearman</div>
                <div className="mt-2 text-3xl font-semibold text-slate-900">{fmt(sameWeekMetrics.spearman, 2)}</div>
              </div>
              <div className="rounded-3xl bg-white p-5 ring-1 ring-slate-200">
                <div className="text-xs uppercase tracking-[0.2em] text-slate-500">AUC</div>
                <div className="mt-2 text-3xl font-semibold text-slate-900">{fmt(sameWeekMetrics.auc, 2)}</div>
              </div>
            </div>

            <div className="grid gap-4 md:grid-cols-4">
              <div className="rounded-3xl bg-slate-50 p-4 ring-1 ring-slate-200"><div className="text-xs text-slate-500">Accuracy</div><div className="mt-2 text-2xl font-semibold text-slate-900">{fmt(sameWeekMetrics.accuracy * 100, 1)}%</div></div>
              <div className="rounded-3xl bg-slate-50 p-4 ring-1 ring-slate-200"><div className="text-xs text-slate-500">Precision</div><div className="mt-2 text-2xl font-semibold text-slate-900">{fmt(sameWeekMetrics.precision * 100, 1)}%</div></div>
              <div className="rounded-3xl bg-slate-50 p-4 ring-1 ring-slate-200"><div className="text-xs text-slate-500">Recall</div><div className="mt-2 text-2xl font-semibold text-slate-900">{fmt(sameWeekMetrics.recall * 100, 1)}%</div></div>
              <div className="rounded-3xl bg-slate-50 p-4 ring-1 ring-slate-200"><div className="text-xs text-slate-500">Specificity</div><div className="mt-2 text-2xl font-semibold text-slate-900">{fmt(sameWeekMetrics.specificity * 100, 1)}%</div></div>
            </div>
          </CardContent>
        </Card>

        <Card className="rounded-3xl border-0 shadow-sm ring-1 ring-slate-200">
          <CardHeader>
            <CardTitle className="flex items-center gap-2 text-xl">
              <Gauge className="h-5 w-5 text-slate-700" />
              Accuracy check — next week
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-5">
            <div className="grid gap-4 md:grid-cols-3">
              <div className="rounded-3xl bg-slate-900 p-5 text-white">
                <div className="text-xs uppercase tracking-[0.2em] text-slate-300">Pearson</div>
                <div className="mt-2 text-3xl font-semibold">{fmt(nextWeekMetrics.pearson, 2)}</div>
              </div>
              <div className="rounded-3xl bg-white p-5 ring-1 ring-slate-200">
                <div className="text-xs uppercase tracking-[0.2em] text-slate-500">Spearman</div>
                <div className="mt-2 text-3xl font-semibold text-slate-900">{fmt(nextWeekMetrics.spearman, 2)}</div>
              </div>
              <div className="rounded-3xl bg-white p-5 ring-1 ring-slate-200">
                <div className="text-xs uppercase tracking-[0.2em] text-slate-500">AUC</div>
                <div className="mt-2 text-3xl font-semibold text-slate-900">{fmt(nextWeekMetrics.auc, 2)}</div>
              </div>
            </div>

            <div className="grid gap-4 md:grid-cols-4">
              <div className="rounded-3xl bg-slate-50 p-4 ring-1 ring-slate-200"><div className="text-xs text-slate-500">Accuracy</div><div className="mt-2 text-2xl font-semibold text-slate-900">{fmt(nextWeekMetrics.accuracy * 100, 1)}%</div></div>
              <div className="rounded-3xl bg-slate-50 p-4 ring-1 ring-slate-200"><div className="text-xs text-slate-500">Precision</div><div className="mt-2 text-2xl font-semibold text-slate-900">{fmt(nextWeekMetrics.precision * 100, 1)}%</div></div>
              <div className="rounded-3xl bg-slate-50 p-4 ring-1 ring-slate-200"><div className="text-xs text-slate-500">Recall</div><div className="mt-2 text-2xl font-semibold text-slate-900">{fmt(nextWeekMetrics.recall * 100, 1)}%</div></div>
              <div className="rounded-3xl bg-slate-50 p-4 ring-1 ring-slate-200"><div className="text-xs text-slate-500">Specificity</div><div className="mt-2 text-2xl font-semibold text-slate-900">{fmt(nextWeekMetrics.specificity * 100, 1)}%</div></div>
            </div>

            <div className="rounded-3xl bg-amber-50 p-5 text-sm text-amber-800 ring-1 ring-amber-200">
              Bila same-week jauh lebih tinggi daripada next-week, maka model ini lebih kuat untuk back analysis daripada early warning murni.
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}

export default function IncidentBackAnalysisTool() {
  const sites = Object.keys(SITE_ROWS);
  const [page, setPage] = useState('dashboard');
  const [site, setSite] = useState('All Site');
  const [selectedWeek, setSelectedWeek] = useState('W50');
  const [lookback, setLookback] = useState(DEFAULT_LOOKBACK);
  const [alertThreshold, setAlertThreshold] = useState(DEFAULT_ALERT_THRESHOLD);
  const [actualOverride, setActualOverride] = useState(null);

  const siteRows = useMemo(() => SITE_ROWS[site].map(enrichRow), [site]);
  const model = useMemo(() => fitStatisticalModel(siteRows), [siteRows]);
  const selectedIndex = useMemo(() => {
    const index = siteRows.findIndex((row) => row.week === selectedWeek);
    return index >= 0 ? index : Math.max(siteRows.length - 1, 0);
  }, [siteRows, selectedWeek]);

  const selectedBaseRow = useMemo(() => {
    return siteRows[selectedIndex] ?? siteRows[siteRows.length - 1] ?? null;
  }, [siteRows, selectedIndex]);

  useEffect(() => {
    const defaultRow = SITE_ROWS[site][SITE_ROWS[site].length - 2] ?? SITE_ROWS[site][SITE_ROWS[site].length - 1];
    if (defaultRow) {
      setSelectedWeek(defaultRow.week);
      setActualOverride(null);
    }
  }, [site]);

  useEffect(() => {
    setActualOverride(null);
  }, [selectedWeek]);

  const actualRow = useMemo(() => {
    if (!selectedBaseRow) return null;
    return actualOverride ? enrichRow({ ...selectedBaseRow, ...actualOverride }) : selectedBaseRow;
  }, [selectedBaseRow, actualOverride]);

  const baselineStats = useMemo(() => {
    return computeBaselineStats(siteRows, selectedIndex, lookback);
  }, [siteRows, selectedIndex, lookback]);

  const contributionHistory = useMemo(() => {
    return computeContributionHistory(siteRows, model, lookback);
  }, [siteRows, model, lookback]);

  const weeklySeries = useMemo(() => {
    return siteRows.map((row, rowIndex) => {
      const prediction = model.predict(row);
      const score = model.predictedToScore(prediction.predictedIncidents);
      const status = model.predictedToStatus(prediction.predictedIncidents);
      const rollingBaseline = computeBaselineStats(siteRows, rowIndex, lookback);
      const contributions = FEATURE_META.map((feature, featureIndex) => {
        const stats = rollingBaseline.byFeature[feature.key];
        const zScore = stats.std ? (getFeatureValue(row, feature.key) - stats.mean) / stats.std : 0;
        return {
          key: feature.key,
          contribution: zScore * model.betas[featureIndex],
        };
      }).sort((a, b) => b.contribution - a.contribution);

      const topDriverKey = contributions[0]?.key;
      const topDriver = FEATURE_META.find((feature) => feature.key === topDriverKey)?.label ?? 'Terkendali';

      return {
        week: row.week,
        score,
        status,
        actualIncidents: row.actualIncidents,
        predictedIncidents: prediction.predictedIncidents,
        topDriver,
        fill: chartStatusColor(status),
      };
    });
  }, [siteRows, model, lookback]);

  const analysis = useMemo(() => {
    if (!actualRow) {
      return {
        score: 0,
        status: 'Hijau',
        predictedIncidents: 0,
        indicators: [],
        coefficientTable: [],
        topDrivers: [],
        actionPriority: [],
        narrative: 'Tidak ada data minggu terpilih.',
      };
    }

    const prediction = model.predict(actualRow);
    const score = model.predictedToScore(prediction.predictedIncidents);
    const status = model.predictedToStatus(prediction.predictedIncidents);

    const indicators = FEATURE_META.map((feature, featureIndex) => {
      const stats = baselineStats.byFeature[feature.key];
      const actualValue = getFeatureValue(actualRow, feature.key);
      const zScore = stats.std ? (actualValue - stats.mean) / stats.std : 0;
      const beta = model.betas[featureIndex];
      const contribution = zScore * beta;
      const contributionState = contributionStatus(contribution, contributionHistory[feature.key] ?? []);

      return {
        ...feature,
        actual: actualValue,
        baselineMean: stats.mean,
        baselineMedian: stats.median,
        baselineStd: stats.std,
        zScore,
        beta,
        contribution,
        direction: contribution >= 0 ? 'Menaikkan predicted risk' : 'Menurunkan predicted risk',
        status: contributionState,
      };
    }).sort((a, b) => b.contribution - a.contribution);

    const topDrivers = indicators.filter((item) => item.contribution > 0).slice(0, 3);
    const topProtectors = indicators.filter((item) => item.contribution < 0).sort((a, b) => a.contribution - b.contribution).slice(0, 2);
    const coefficientTable = FEATURE_META.map((feature, index) => ({ ...feature, beta: model.betas[index] })).sort((a, b) => Math.abs(b.beta) - Math.abs(a.beta));

    const narrativeParts = [
      `Pada ${site} minggu ${selectedWeek}, model statistik menghasilkan predicted incident level sebesar ${fmt(prediction.predictedIncidents, 2)} dengan score ${fmt(score, 1)} dan status ${status.toLowerCase()}.`,
      `Reference minggu ini dihitung dari rolling baseline ${baselineStats.baselineRows.length} minggu sebelumnya, dengan pendekatan mean dan standard deviation per indikator.`,
    ];

    if (actualRow.actualIncidents > 0) {
      narrativeParts.push(`Insiden aktual tercatat ${fmt(actualRow.actualIncidents, 0)}, sehingga pembacaan difokuskan pada kontributor statistik yang mendorong predicted risk ke arah positif.`);
    } else {
      narrativeParts.push('Belum ada insiden aktual, namun score tetap dibaca sebagai tekanan risiko relatif terhadap pola historis site.');
    }

    if (topDrivers.length) {
      narrativeParts.push(`Kontributor risiko terbesar minggu ini adalah ${topDrivers.map((item) => item.label).join(', ')}.`);
    }

    if (topProtectors.length) {
      narrativeParts.push(`Sementara itu, faktor yang masih menahan kenaikan risiko adalah ${topProtectors.map((item) => item.label).join(', ')}.`);
    }

    const actionPriority = topDrivers.map((item, index) => `${index + 1}. ${item.label} — kontribusi ${fmt(item.contribution, 2)} pada model, dengan deviasi z-score ${fmt(item.zScore, 2)} dari rolling baseline.`);

    return {
      score,
      status,
      predictedIncidents: prediction.predictedIncidents,
      indicators,
      coefficientTable,
      topDrivers,
      actionPriority,
      narrative: narrativeParts.join(' '),
    };
  }, [actualRow, model, baselineStats, contributionHistory, site, selectedWeek]);

  const summaryTrend = useMemo(() => {
    if (!weeklySeries.length) {
      return {
        avg: 0,
        peak: { week: '-', score: 0, status: 'Hijau' },
        latest: { week: '-', score: 0, status: 'Hijau' },
      };
    }

    const avg = mean(weeklySeries.map((row) => row.score));
    const peak = weeklySeries.reduce((max, row) => (row.score > max.score ? row : max), weeklySeries[0]);
    const latest = weeklySeries[weeklySeries.length - 1];
    return { avg, peak, latest };
  }, [weeklySeries]);

  const validationMetrics = useMemo(() => {
    const sameWeekScores = weeklySeries.map((row) => row.score);
    const sameWeekIncidents = weeklySeries.map((row) => row.actualIncidents);
    const nextWeekScores = weeklySeries.slice(0, -1).map((row) => row.score);
    const nextWeekIncidents = weeklySeries.slice(1).map((row) => row.actualIncidents);

    return {
      sameWeek: {
        pearson: pearsonCorrelation(sameWeekScores, sameWeekIncidents),
        spearman: spearmanCorrelation(sameWeekScores, sameWeekIncidents),
        ...confusionMetrics(sameWeekScores, sameWeekIncidents, alertThreshold),
      },
      nextWeek: {
        pearson: pearsonCorrelation(nextWeekScores, nextWeekIncidents),
        spearman: spearmanCorrelation(nextWeekScores, nextWeekIncidents),
        ...confusionMetrics(nextWeekScores, nextWeekIncidents, alertThreshold),
      },
    };
  }, [weeklySeries, alertThreshold]);

  const handleWeekChange = (week) => {
    setSelectedWeek(week);
  };

  const setActualValue = (field, value) => {
    if (!selectedBaseRow) return;
    const normalizedValue = field === 'actualIncidents' ? Math.max(toFiniteNumber(value, selectedBaseRow[field]), 0) : toFiniteNumber(value, selectedBaseRow[field]);
    setActualOverride((previous) => ({
      ...(previous ?? {}),
      [field]: normalizedValue,
    }));
  };

  const resetDemo = () => {
    setSite('All Site');
    setPage('dashboard');
    setLookback(DEFAULT_LOOKBACK);
    setAlertThreshold(DEFAULT_ALERT_THRESHOLD);
    setActualOverride(null);
  };

  return (
    <div className="min-h-screen bg-slate-50 p-6">
      <div className="mx-auto max-w-7xl space-y-6">
        <KPIHeader
          site={site}
          setSite={setSite}
          sites={sites}
          alertThreshold={alertThreshold}
          setAlertThreshold={setAlertThreshold}
          summaryTrend={summaryTrend}
          resetDemo={resetDemo}
          page={page}
          setPage={setPage}
          lookback={lookback}
          setLookback={setLookback}
        />

        {page === 'accuracy' ? (
          <AccuracyPage
            weeklySeries={weeklySeries}
            alertThreshold={alertThreshold}
            sameWeekMetrics={validationMetrics.sameWeek}
            nextWeekMetrics={validationMetrics.nextWeek}
          />
        ) : (
          <>
            <div className="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
              <Card className="rounded-3xl border-0 shadow-sm ring-1 ring-slate-200">
                <CardHeader>
                  <CardTitle className="flex items-center gap-2 text-xl">
                    <TrendingUp className="h-5 w-5 text-slate-700" />
                    Statistical score trend per week
                  </CardTitle>
                </CardHeader>
                <CardContent className="space-y-6">
                  <div className="h-[340px] w-full rounded-3xl bg-slate-50 p-4 ring-1 ring-slate-200">
                    <ResponsiveContainer width="100%" height="100%">
                      <LineChart data={weeklySeries} margin={{ top: 20, right: 10, left: 0, bottom: 5 }}>
                        <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" />
                        <XAxis dataKey="week" stroke="#64748b" fontSize={12} />
                        <YAxis domain={[0, 100]} stroke="#64748b" fontSize={12} />
                        <Tooltip content={<TrendTooltip />} />
                        <ReferenceLine y={50} stroke="#f59e0b" strokeDasharray="4 4" />
                        <ReferenceLine y={80} stroke="#ef4444" strokeDasharray="4 4" />
                        <Line type="monotone" dataKey="score" stroke="#0f172a" strokeWidth={3} dot={{ r: 4 }} activeDot={{ r: 6 }} />
                      </LineChart>
                    </ResponsiveContainer>
                  </div>

                  <div className="grid gap-4 md:grid-cols-3">
                    <div className="rounded-3xl bg-white p-5 ring-1 ring-slate-200">
                      <div className="text-xs uppercase tracking-[0.2em] text-slate-500">Predicted incident</div>
                      <div className="mt-2 text-3xl font-semibold text-slate-900">{fmt(analysis.predictedIncidents, 2)}</div>
                      <div className="mt-1 text-sm text-slate-500">Fitted from standardized ridge model</div>
                    </div>
                    <div className="rounded-3xl bg-white p-5 ring-1 ring-slate-200">
                      <div className="text-xs uppercase tracking-[0.2em] text-slate-500">Score</div>
                      <div className="mt-2 text-3xl font-semibold text-slate-900">{fmt(analysis.score, 1)}</div>
                      <div className="mt-1 text-sm text-slate-500">Normalized from model prediction</div>
                    </div>
                    <div className="rounded-3xl bg-white p-5 ring-1 ring-slate-200">
                      <div className="text-xs uppercase tracking-[0.2em] text-slate-500">Status</div>
                      <Badge className={`mt-3 rounded-full border px-3 py-1 text-sm ${badgeClass(statusColor(analysis.status))}`}>{analysis.status}</Badge>
                      <div className="mt-3 text-sm text-slate-500">Derived from site-specific score quantiles</div>
                    </div>
                  </div>
                </CardContent>
              </Card>

              <Card className="rounded-3xl border-0 shadow-sm ring-1 ring-slate-200">
                <CardHeader>
                  <CardTitle className="flex items-center gap-2 text-xl">
                    <BrainCircuit className="h-5 w-5 text-slate-700" />
                    Top statistical drivers
                  </CardTitle>
                </CardHeader>
                <CardContent className="space-y-3">
                  {analysis.topDrivers.length === 0 ? (
                    <div className="rounded-3xl bg-emerald-50 p-5 text-sm text-emerald-700 ring-1 ring-emerald-200">
                      Tidak ada kontribusi positif besar pada minggu ini. Indikator utama cenderung netral atau protektif terhadap predicted risk.
                    </div>
                  ) : (
                    analysis.topDrivers.map((item) => {
                      const Icon = item.icon;
                      return (
                        <div key={item.key} className="flex items-start gap-3 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200">
                          <div className="rounded-2xl bg-white p-2 ring-1 ring-slate-200"><Icon className="h-4 w-4 text-slate-700" /></div>
                          <div>
                            <div className="font-medium text-slate-900">{item.label}</div>
                            <div className="mt-1 text-sm text-slate-500">β = {fmt(item.beta, 2)} · z = {fmt(item.zScore, 2)} · contribution = {fmt(item.contribution, 2)}</div>
                            <div className="mt-1 text-sm text-slate-500">{item.description}</div>
                          </div>
                        </div>
                      );
                    })
                  )}
                </CardContent>
              </Card>
            </div>

            <div className="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
              <Card className="rounded-3xl border-0 shadow-sm ring-1 ring-slate-200">
                <CardHeader>
                  <CardTitle className="text-xl">Selected week and rolling reference</CardTitle>
                </CardHeader>
                <CardContent className="space-y-6">
                  <div className="grid gap-4 md:grid-cols-3">
                    <div className="space-y-2">
                      <Label>Week</Label>
                      <Select value={selectedWeek} onValueChange={handleWeekChange}>
                        <SelectTrigger className="rounded-2xl">
                          <SelectValue placeholder="Pilih week" />
                        </SelectTrigger>
                        <SelectContent>
                          {SITE_ROWS[site].map((item) => (
                            <SelectItem key={item.week} value={item.week}>{item.week}</SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                    </div>
                    <div className="space-y-2">
                      <Label>Site</Label>
                      <Input value={site} readOnly className="rounded-2xl bg-slate-50" />
                    </div>
                    <div className="space-y-2">
                      <Label>Actual Incidents</Label>
                      <Input type="number" value={actualRow?.actualIncidents ?? 0} onChange={(event) => setActualValue('actualIncidents', event.target.value)} className="rounded-2xl" />
                    </div>
                  </div>

                  <div className="grid gap-6 md:grid-cols-2">
                    <Card className="rounded-3xl bg-slate-50 shadow-none ring-1 ring-slate-200">
                      <CardHeader>
                        <CardTitle className="text-base">Actual values</CardTitle>
                      </CardHeader>
                      <CardContent className="grid gap-4">
                        {[
                          ['hazard', 'Pelaporan Hazard'],
                          ['rfidSupervisor', 'RFID Pengawas'],
                          ['tbc', 'Pelaporan TBC'],
                          ['goldenRules', 'Golden Rules'],
                          ['blindspotTbc', 'Blindspot TBC'],
                          ['coverageArea', 'Daily Coverage Area (%)'],
                          ['rfidNonSupervisor', 'RFID Non Pengawas'],
                        ].map(([field, label]) => (
                          <div className="space-y-2" key={field}>
                            <Label>{label}</Label>
                            <Input type="number" value={actualRow?.[field] ?? 0} onChange={(event) => setActualValue(field, event.target.value)} className="rounded-2xl bg-white" />
                          </div>
                        ))}
                      </CardContent>
                    </Card>

                    <Card className="rounded-3xl bg-slate-50 shadow-none ring-1 ring-slate-200">
                      <CardHeader>
                        <CardTitle className="text-base">Rolling baseline reference</CardTitle>
                      </CardHeader>
                      <CardContent className="space-y-3">
                        <div className="rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                          <div className="text-sm font-medium text-slate-900">Window used</div>
                          <div className="mt-1 text-sm text-slate-500">{baselineStats.baselineRows.length} minggu historis sebelum {selectedWeek}</div>
                        </div>
                        {FEATURE_META.map((feature) => (
                          <div key={feature.key} className="rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                            <div className="font-medium text-slate-900">{feature.label}</div>
                            <div className="mt-1 text-sm text-slate-500">Mean {fmt(baselineStats.byFeature[feature.key].mean, 2)} · Median {fmt(baselineStats.byFeature[feature.key].median, 2)} · Std {fmt(baselineStats.byFeature[feature.key].std, 2)}</div>
                          </div>
                        ))}
                      </CardContent>
                    </Card>
                  </div>
                </CardContent>
              </Card>

              <div className="space-y-6">
                <Card className="rounded-3xl border-0 shadow-sm ring-1 ring-slate-200">
                  <CardHeader>
                    <CardTitle className="text-xl">Narrative insight</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <Textarea value={analysis.narrative} readOnly className="min-h-[260px] rounded-3xl bg-slate-50 text-sm leading-6" />
                  </CardContent>
                </Card>

                <Card className="rounded-3xl border-0 shadow-sm ring-1 ring-slate-200">
                  <CardHeader>
                    <CardTitle className="text-xl">Priority actions</CardTitle>
                  </CardHeader>
                  <CardContent className="space-y-3">
                    {analysis.actionPriority.length === 0 ? (
                      <div className="rounded-3xl bg-emerald-50 p-5 text-sm text-emerald-700 ring-1 ring-emerald-200">
                        Tidak ada kontribusi risiko yang menonjol pada minggu ini. Pertahankan konsistensi kontrol dan monitoring historis.
                      </div>
                    ) : (
                      analysis.actionPriority.map((line, index) => (
                        <div key={`${line}-${index}`} className="rounded-3xl bg-slate-50 p-4 text-sm text-slate-700 ring-1 ring-slate-200">{line}</div>
                      ))
                    )}
                  </CardContent>
                </Card>
              </div>
            </div>

            <Card className="rounded-3xl border-0 shadow-sm ring-1 ring-slate-200">
              <CardHeader>
                <CardTitle className="text-xl">Indicator contribution table</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="overflow-x-auto">
                  <table className="min-w-full text-left text-sm">
                    <thead className="border-b border-slate-200 text-slate-500">
                      <tr>
                        <th className="px-4 py-3">Priority</th>
                        <th className="px-4 py-3">Indicator</th>
                        <th className="px-4 py-3">Actual</th>
                        <th className="px-4 py-3">Baseline Mean</th>
                        <th className="px-4 py-3">Z-score</th>
                        <th className="px-4 py-3">Std. β</th>
                        <th className="px-4 py-3">Contribution</th>
                        <th className="px-4 py-3">Direction</th>
                        <th className="px-4 py-3">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      {analysis.indicators.map((item, index) => (
                        <tr key={item.key} className="border-b border-slate-100">
                          <td className="px-4 py-4 font-medium text-slate-900">{index + 1}</td>
                          <td className="px-4 py-4 font-medium text-slate-900">{item.label}</td>
                          <td className="px-4 py-4 text-slate-900">{fmt(item.actual, 2)}</td>
                          <td className="px-4 py-4 text-slate-900">{fmt(item.baselineMean, 2)}</td>
                          <td className={`px-4 py-4 font-medium ${item.zScore >= 0 ? 'text-slate-900' : 'text-slate-500'}`}>{fmt(item.zScore, 2)}</td>
                          <td className={`px-4 py-4 font-medium ${item.beta >= 0 ? 'text-red-600' : 'text-emerald-600'}`}>{fmt(item.beta, 2)}</td>
                          <td className={`px-4 py-4 font-medium ${item.contribution >= 0 ? 'text-red-600' : 'text-emerald-600'}`}>{fmt(item.contribution, 2)}</td>
                          <td className="px-4 py-4 text-slate-600">{item.direction}</td>
                          <td className="px-4 py-4">
                            <Badge className={`rounded-full border px-3 py-1 ${badgeClass(item.status.color)}`}>{item.status.label}</Badge>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </CardContent>
            </Card>

            <div className="grid gap-6 lg:grid-cols-2">
              <Card className="rounded-3xl border-0 shadow-sm ring-1 ring-slate-200">
                <CardHeader>
                  <CardTitle className="flex items-center gap-2 text-xl">
                    <Sigma className="h-5 w-5 text-slate-700" />
                    Statistical methodology
                  </CardTitle>
                </CardHeader>
                <CardContent className="space-y-4 text-sm text-slate-600">
                  <div className="rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200">
                    <div className="font-medium text-slate-900">Baseline / reference</div>
                    <div className="mt-2">Rolling baseline dihitung dari {lookback} minggu historis sebelumnya pada site yang sama. Untuk tiap indikator digunakan mean, median, dan standard deviation.</div>
                  </div>
                  <div className="rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200">
                    <div className="font-medium text-slate-900">Weight / coefficient</div>
                    <div className="mt-2">Bobot indikator berasal dari standardized coefficient ridge regression yang di-fit pada histori site. Tidak ada expert weight manual pada versi ini.</div>
                  </div>
                  <div className="rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200">
                    <div className="font-medium text-slate-900">Overall score</div>
                    <div className="mt-2">Score mingguan berasal dari predicted incident level model lalu dinormalisasi ke 0–100 pada distribusi fitted prediction site yang sama.</div>
                  </div>
                </CardContent>
              </Card>

              <Card className="rounded-3xl border-0 shadow-sm ring-1 ring-slate-200">
                <CardHeader>
                  <CardTitle className="text-xl">Model coefficient ranking</CardTitle>
                </CardHeader>
                <CardContent className="space-y-3">
                  {analysis.coefficientTable.map((item) => {
                    const Icon = item.icon;
                    return (
                      <div key={item.key} className="flex items-start gap-3 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200">
                        <div className="rounded-2xl bg-white p-2 ring-1 ring-slate-200"><Icon className="h-4 w-4 text-slate-700" /></div>
                        <div className="flex-1">
                          <div className="flex items-center justify-between gap-3">
                            <div className="font-medium text-slate-900">{item.label}</div>
                            <div className={`text-sm font-semibold ${item.beta >= 0 ? 'text-red-600' : 'text-emerald-600'}`}>β = {fmt(item.beta, 2)}</div>
                          </div>
                          <div className="mt-1 text-sm text-slate-500">{item.description}</div>
                        </div>
                      </div>
                    );
                  })}
                </CardContent>
              </Card>
            </div>
          </>
        )}
      </div>
    </div>
  );
}
