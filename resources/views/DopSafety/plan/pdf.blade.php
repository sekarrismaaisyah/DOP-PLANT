<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>DOP - {{ strtoupper($plan->site) }}</title>
    <style>
        @page { 
            margin: 15px; 
        } 
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 7px; 
            color: #000;
        }
        
        /* Kop Surat */
        .header-table { width: 100%; margin-bottom: 5px; }
        .header-table td { border: none; vertical-align: middle; }
        
        /* Tabel Data Utama */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }
        
        /* Warna Khusus Sesuai Gambar */
        .bg-green-header {
            background-color: #9ccf53; /* Hijau Terang */
            font-weight: bold;
        }
        .bg-brown-shift {
            background-color: #a64d13; /* Cokelat Bata */
            color: #ffffff;
            font-weight: bold;
            font-size: 8px;
            padding: 4px;
        }
        .bg-black-section {
            background-color: #000000;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 4px 6px;
        }

        /* Tabel Tanda Tangan */
        .sig-container {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px 0; /* Memberi jarak antar box */
            margin-top: 20px;
        }
        .sig-box {
            width: 20%;
            border: none;
            vertical-align: top;
            padding: 0;
        }
        .sig-inner-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }
        .sig-inner-table td {
            border: none;
            text-align: center;
            padding: 3px;
            font-size: 7px;
            font-weight: bold;
        }
        .sig-top { background-color: #000; color: #fff; }
        .sig-mid { background-color: #fff; height: 35px; } /* Area TTD */
        .sig-name { 
            background-color: #9ccf53; 
            color: #000; 
            border-top: 1px solid #000; 
            border-bottom: 1px solid #000; 
        }
        .sig-bot { background-color: #000; color: #fff; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td width="20%"><h1 style="margin: 0; font-size: 18px; color: #1e3a8a;">PAMA</h1></td>
            <td width="60%" style="text-align: center;">
                <div style="font-weight: bold; font-size: 11px;">DAILY OPERATION PLANNING (DOP)</div>
                <div style="font-weight: bold; font-size: 9px;">PT. PAMA PERSADA - PLANT DEPT.</div>
                <div style="font-weight: bold; font-size: 9px;">"SITE {{ strtoupper($plan->site) }}"</div>
            </td>
            <td width="20%" style="text-align: right;">
                <div style="font-weight: bold; font-size: 9px;">berau coal</div>
                <div style="font-style: italic; font-size: 6px;">better energy brighter future</div>
            </td>
        </tr>
    </table>

    <div style="font-weight: bold; font-size: 9px; margin-bottom: 5px;">
        HARI / TANGGAL : &nbsp;&nbsp;&nbsp; {{ strtoupper(\Carbon\Carbon::parse($plan->plan_date)->translatedFormat('l, d F Y')) }}
    </div>

    <table class="data-table">
        <thead>
            <tr class="bg-green-header">
                <th rowspan="2" width="2%">No.</th>
                <th rowspan="2" width="4%">COMPLETED</th>
                <th rowspan="2" width="5%">Kode Unit</th>
                <th rowspan="2" width="6%">Section</th>
                <th rowspan="2" width="6%">Lokasi</th>
                <th rowspan="2" width="11%">Detail Pekerjaan</th>
                <th rowspan="2" width="4%">IZIN KERJA</th>
                <th rowspan="2" width="4%">CCTV</th>
                <th rowspan="2" width="8%">Alat Bantu / Peralatan</th>
                <th colspan="2">LIST PEKERJA</th>
                <th rowspan="2" width="6%">Group Leader (L1)</th>
                <th rowspan="2" width="4%">SID</th>
                <th rowspan="2" width="6%">Section Head (L2)</th>
                <th rowspan="2" width="4%">SID</th>
                <th rowspan="2" width="6%">SHE Leader (L3)</th>
                <th rowspan="2" width="4%">SID</th>
                <th rowspan="2" width="6%">Dept. Head (L4)</th>
                <th rowspan="2" width="4%">SID</th>
                <th rowspan="2" width="4%">PJA BC</th>
            </tr>
            <tr class="bg-green-header">
                <th width="7%">NAMA</th>
                <th width="4%">SID</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="20" class="bg-brown-shift">
                    SHIFT {{ $plan->shift }}
                </td>
            </tr>

            @php $no = 1; @endphp
            @foreach($itemsBySection as $sectionName => $items)
                <tr>
                    <td colspan="20" class="bg-black-section">
                        {{ strtoupper($sectionName) }}
                    </td>
                </tr>

                @foreach($items as $item)
                    @php
                       $workers = \App\Support\DopSafety\DopSafetyPlanTableStructure::workersToDisplayCells(is_array($item->workers) ? $item->workers : []);
                    @endphp
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td style="font-weight: bold; color: {{ $item->approval_status === 'done' ? 'green' : 'red' }};">
                            {{ $item->approval_status === 'done' ? 'OK' : 'NOK' }}
                        </td>
                        <td>{{ $item->unit_code }}</td>
                        <td>{{ $item->section_name }}</td>
                        <td>{{ $item->location }}</td>
                        <td style="text-align: left;">{{ $item->job_detail }}</td>
                        <td>{{ $item->work_permit }}</td>
                        
                        <td>{{ $item->cctv }}</td>
                        
                        <td style="text-align: left;">{{ is_array($item->tools) ? implode(', ', $item->tools) : $item->tools }}</td>
                        
                        <td>{!! str_replace(';', '<br>', htmlspecialchars($workers['names'])) !!}</td>
                        <td>{!! str_replace(';', '<br>', htmlspecialchars($workers['sids'])) !!}</td>
                        
                        <td>{{ $item->group_leader }}</td>
                        <td>{{ $item->group_leader_sid }}</td>
                        <td>{{ $item->section_head }}</td>
                        <td>{{ $item->section_head_sid }}</td>
                        <td>{{ $item->she_leader }}</td>
                        <td>{{ $item->she_leader_sid }}</td>
                        <td>{{ $item->dept_head }}</td>
                        <td>{{ $item->dept_head_sid }}</td>
                        <td>{{ $item->pja_bc }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <table class="sig-container">
        <tr>
            <td class="sig-box">
                <table class="sig-inner-table">
                    <tr><td class="sig-top">Dibuat Oleh</td></tr>
                    <tr><td class="sig-mid"></td></tr>
                    <tr><td class="sig-name">{{ 'RICKY MAULANA FAGI' }}</td></tr>
                    <tr><td class="sig-bot">{{ 'LCE' }}</td></tr>
                </table>
            </td>
            
            <td class="sig-box">
                <table class="sig-inner-table">
                    <tr><td class="sig-top">Mengetahui</td></tr>
                    <tr><td class="sig-mid"></td></tr>
                    <tr><td class="sig-name">{{ strtoupper($plan->acknowledged_1_name ?? 'KUSDIHARTO') }}</td></tr>
                    <tr><td class="sig-bot">Plant Dept. Head</td></tr>
                </table>
            </td>
            
            <td class="sig-box">
                <table class="sig-inner-table">
                    <tr><td class="sig-top">Mengetahui</td></tr>
                    <tr><td class="sig-mid"></td></tr>
                    <tr><td class="sig-name">{{ strtoupper($plan->acknowledged_2_name ?? 'DANI EKO K') }}</td></tr>
                    <tr><td class="sig-bot">SHE Dept. Head</td></tr>
                </table>
            </td>
            
            <td class="sig-box">
                <table class="sig-inner-table">
                    <tr><td class="sig-top">Mengetahui</td></tr>
                    <tr><td class="sig-mid"></td></tr>
                    <tr><td class="sig-name">{{ strtoupper($plan->acknowledged_3_name ?? 'YOHANES YUDO HARSANTO') }}</td></tr>
                    <tr><td class="sig-bot">PROJECT MANAGER</td></tr>
                </table>
            </td>
            
            <td class="sig-box">
                <table class="sig-inner-table">
                    <tr><td class="sig-top">Mengetahui</td></tr>
                    <tr><td class="sig-mid"></td></tr>
                    <tr><td class="sig-name">{{ strtoupper($plan->acknowledged_4_name ?? 'DAVI TANTRA') }}</td></tr>
                    <tr><td class="sig-bot">Safety Operation Superintendent</td></tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>