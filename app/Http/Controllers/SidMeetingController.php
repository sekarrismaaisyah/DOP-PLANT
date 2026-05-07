<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Event;
use App\Models\EventMinute;
use App\Models\MeetingType;
use App\Models\MinuteIssue;
use App\Models\Site;
use App\Services\SemanticSimilarityService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SidMeetingController extends Controller
{
    public function dashboard(): View
    {
        return view('sid-meeting.desain');
    }

    public function sites(): View
    {
        return view('sid-meeting.master-sites', ['rows' => Site::query()->orderBy('name')->get()]);
    }

    public function storeSite(Request $request): RedirectResponse
    {
        $payload = $request->validate(['name' => 'required|string|max:255', 'code' => 'nullable|string|max:50', 'is_active' => 'nullable|boolean']);
        Site::query()->create($payload + ['is_active' => $request->boolean('is_active', true)]);
        return back()->with('success', 'Site berhasil disimpan.');
    }

    public function updateSite(Request $request, Site $site): RedirectResponse
    {
        $payload = $request->validate(['name' => 'required|string|max:255', 'code' => 'nullable|string|max:50', 'is_active' => 'nullable|boolean']);
        $site->update($payload + ['is_active' => $request->boolean('is_active')]);
        return back()->with('success', 'Site berhasil diupdate.');
    }

    public function destroySite(Site $site): RedirectResponse
    {
        $site->delete();
        return back()->with('success', 'Site berhasil dihapus.');
    }

    public function meetingTypes(): View
    {
        return view('sid-meeting.master-meeting-types', ['rows' => MeetingType::query()->orderBy('name')->get()]);
    }

    public function storeMeetingType(Request $request): RedirectResponse
    {
        $payload = $request->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string|max:255', 'is_active' => 'nullable|boolean']);
        MeetingType::query()->create($payload + ['is_active' => $request->boolean('is_active', true)]);
        return back()->with('success', 'Jenis meeting berhasil disimpan.');
    }

    public function updateMeetingType(Request $request, MeetingType $meetingType): RedirectResponse
    {
        $payload = $request->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string|max:255', 'is_active' => 'nullable|boolean']);
        $meetingType->update($payload + ['is_active' => $request->boolean('is_active')]);
        return back()->with('success', 'Jenis meeting berhasil diupdate.');
    }

    public function destroyMeetingType(MeetingType $meetingType): RedirectResponse
    {
        $meetingType->delete();
        return back()->with('success', 'Jenis meeting berhasil dihapus.');
    }

    public function companies(): View
    {
        $rows = Company::query()->with('sites')->orderBy('name')->get();
        return view('sid-meeting.master-companies', ['rows' => $rows, 'sites' => Site::query()->orderBy('name')->get()]);
    }

    public function storeCompany(Request $request): RedirectResponse
    {
        $payload = $request->validate(['name' => 'required|string|max:255', 'code' => 'nullable|string|max:50', 'is_active' => 'nullable|boolean', 'site_ids' => 'array']);
        $company = Company::query()->create($payload + ['is_active' => $request->boolean('is_active', true)]);
        $syncData = collect($request->input('site_ids', []))->mapWithKeys(fn ($siteId): array => [$siteId => ['is_required' => true]])->all();
        $company->sites()->sync($syncData);
        return back()->with('success', 'Perusahaan berhasil disimpan.');
    }

    public function updateCompany(Request $request, Company $company): RedirectResponse
    {
        $payload = $request->validate(['name' => 'required|string|max:255', 'code' => 'nullable|string|max:50', 'is_active' => 'nullable|boolean', 'site_ids' => 'array']);
        $company->update($payload + ['is_active' => $request->boolean('is_active')]);
        $syncData = collect($request->input('site_ids', []))->mapWithKeys(fn ($siteId): array => [$siteId => ['is_required' => true]])->all();
        $company->sites()->sync($syncData);
        return back()->with('success', 'Perusahaan berhasil diupdate.');
    }

    public function destroyCompany(Company $company): RedirectResponse
    {
        $company->delete();
        return back()->with('success', 'Perusahaan berhasil dihapus.');
    }

    public function employees(Request $request): View
    {
        $query = Employee::query()->with('company');
        if ($term = $request->string('q')->toString()) {
            $query->where(function ($inner) use ($term): void {
                $inner->where('kode_sid', 'like', '%' . $term . '%')
                    ->orWhere('nama', 'like', '%' . $term . '%')
                    ->orWhereHas('company', fn ($q) => $q->where('name', 'like', '%' . $term . '%'));
            });
        }
        return view('sid-meeting.master-employees', ['rows' => $query->latest()->paginate(20), 'companies' => Company::query()->orderBy('name')->get()]);
    }

    public function storeEmployee(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'kode_sid' => 'required|string|max:50|unique:employees,kode_sid',
            'nama' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'jabatan_struktural' => 'nullable|string|max:255',
            'jabatan_fungsional' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
        Employee::query()->create($payload + ['is_active' => $request->boolean('is_active', true)]);
        return back()->with('success', 'Employee berhasil disimpan.');
    }

    public function updateEmployee(Request $request, Employee $employee): RedirectResponse
    {
        $payload = $request->validate([
            'kode_sid' => 'required|string|max:50|unique:employees,kode_sid,' . $employee->id,
            'nama' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'jabatan_struktural' => 'nullable|string|max:255',
            'jabatan_fungsional' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
        $employee->update($payload + ['is_active' => $request->boolean('is_active')]);
        return back()->with('success', 'Employee berhasil diupdate.');
    }

    public function destroyEmployee(Employee $employee): RedirectResponse
    {
        $employee->delete();
        return back()->with('success', 'Employee berhasil dihapus.');
    }

    public function events(Request $request): View
    {
        $query = Event::query()->with(['site', 'meetingType'])->latest('meeting_date');
        foreach (['site_id', 'week', 'status'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }
        if ($search = $request->string('q')->toString()) {
            $query->where('event_code', 'like', '%' . $search . '%');
        }

        return view('sid-meeting.events', [
            'rows' => $query->paginate(20),
            'sites' => Site::query()->orderBy('name')->get(),
            'meetingTypes' => MeetingType::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function storeEvent(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'meeting_type_id' => 'required|exists:meeting_types,id',
            'site_id' => 'required|exists:sites,id',
            'meeting_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'nullable|string',
        ]);

        $event = $this->createEventWithUniqueCode(function (string $eventCode) use ($payload): Event {
            return Event::query()->create([
                ...$payload,
                'week' => Carbon::parse($payload['meeting_date'])->format('o-\WW'),
                'event_code' => $eventCode,
                'qr_token' => (string) Str::uuid(),
                'status' => $payload['status'] ?? 'draft',
                'created_by' => auth()->id(),
            ]);
        }, $payload['meeting_date']);

        return back()->with('success', 'Event berhasil dibuat.');
    }

    public function showEvent(Event $event): View
    {
        $event->load(['site', 'meetingType', 'attendances', 'eventMinute.issues']);
        $eligibleCompanies = Company::query()->whereHas('sites', fn ($q) => $q->where('sites.id', $event->site_id))->get();
        $attendedCompanyNames = $event->attendances->pluck('perusahaan_snapshot')->unique();
        return view('sid-meeting.event-detail', [
            'event' => $event,
            'employees' => Employee::query()->where('is_active', true)->with('company')->orderBy('nama')->get(),
            'eligibleCompanies' => $eligibleCompanies,
            'attendedCompanyNames' => $attendedCompanyNames,
        ]);
    }

    public function closeEvent(Event $event): RedirectResponse
    {
        $event->update(['status' => 'closed', 'closed_at' => now()]);
        return back()->with('success', 'Event berhasil ditutup.');
    }

    public function manualAttendance(Request $request, Event $event): RedirectResponse
    {
        $request->validate(['kode_sid' => 'required|string']);
        $employee = Employee::query()->with('company')->where('kode_sid', $request->input('kode_sid'))->where('is_active', true)->first();
        if (!$employee) {
            return back()->with('error', 'SID tidak ditemukan.');
        }
        $duplicate = Attendance::query()->where('event_id', $event->id)->where('employee_id', $employee->id)->exists();
        if ($duplicate) {
            return back()->with('error', 'SID sudah hadir pada event ini.');
        }
        Attendance::query()->create([
            'event_id' => $event->id,
            'employee_id' => $employee->id,
            'kode_sid' => $employee->kode_sid,
            'nama_snapshot' => $employee->nama,
            'perusahaan_snapshot' => $employee->company->name,
            'jabatan_struktural_snapshot' => $employee->jabatan_struktural,
            'jabatan_fungsional_snapshot' => $employee->jabatan_fungsional,
            'attended_at' => now(),
            'input_method' => 'manual',
        ]);

        return back()->with('success', 'Absensi manual berhasil disimpan.');
    }

    public function saveMinutes(Request $request, Event $event): RedirectResponse
    {
        $payload = $request->validate(['title' => 'nullable|string|max:255', 'notulis' => 'nullable|string|max:255', 'location' => 'nullable|string|max:255']);
        $minute = EventMinute::query()->updateOrCreate(['event_id' => $event->id], [...$payload, 'updated_by' => auth()->id()]);
        return redirect()->route('sid-meeting.events.show', $event)->with('success', 'Header notulensi disimpan.');
    }

    public function storeIssue(Request $request, Event $event): RedirectResponse
    {
        $minute = EventMinute::query()->firstOrCreate(['event_id' => $event->id], ['updated_by' => auth()->id()]);
        $payload = $request->validate([
            'section' => 'required|in:enviro,safety,general',
            'nomor' => 'required|integer|min:1',
            'catatan_meeting' => 'required|string',
            'issued_by' => 'nullable|string|max:255',
            'pic' => 'nullable|string|max:255',
            'due_date' => 'nullable|date',
            'status' => 'required|in:Open,Progress,Closed,Overdue',
            'keterangan' => 'nullable|string',
        ]);
        $minute->issues()->create($payload);
        return back()->with('success', 'Issue notulen berhasil disimpan.');
    }

    public function deleteIssue(MinuteIssue $issue): RedirectResponse
    {
        $issue->delete();
        return back()->with('success', 'Issue berhasil dihapus.');
    }

    public function reports(Request $request): View
    {
        $attendanceQuery = Attendance::query()->with(['event.site', 'event.meetingType', 'employee.company']);
        if ($request->filled('site_id')) {
            $attendanceQuery->whereHas('event', fn ($q) => $q->where('site_id', $request->input('site_id')));
        }
        if ($request->filled('week')) {
            $attendanceQuery->whereHas('event', fn ($q) => $q->where('week', $request->input('week')));
        }
        $attendanceRows = $attendanceQuery->latest('attended_at')->paginate(20, ['*'], 'attendance_page');

        $issuesQuery = MinuteIssue::query()->with(['eventMinute.event.site', 'eventMinute.event.meetingType']);
        if ($request->filled('section')) {
            $issuesQuery->where('section', $request->input('section'));
        }
        if ($request->filled('status')) {
            $issuesQuery->where('status', $request->input('status'));
        }
        $issueRows = $issuesQuery->latest()->paginate(20, ['*'], 'issues_page');

        return view('sid-meeting.reports', ['attendanceRows' => $attendanceRows, 'issueRows' => $issueRows, 'sites' => Site::query()->orderBy('name')->get()]);
    }

    public function exportAttendanceCsv(Request $request): StreamedResponse
    {
        $rows = Attendance::query()->with(['event.site', 'event.meetingType', 'employee.company'])->latest('attended_at')->get();
        return response()->streamDownload(function () use ($rows): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Tanggal', 'Week', 'Site', 'Jenis Meeting', 'Kode Event', 'Kode SID', 'Nama', 'Perusahaan', 'Jabatan Struktural', 'Jabatan Fungsional', 'Timestamp']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    optional($row->event)->meeting_date?->format('Y-m-d'),
                    optional($row->event)->week,
                    optional(optional($row->event)->site)->name,
                    optional(optional($row->event)->meetingType)->name,
                    optional($row->event)->event_code,
                    $row->kode_sid,
                    $row->nama_snapshot,
                    $row->perusahaan_snapshot,
                    $row->jabatan_struktural_snapshot,
                    $row->jabatan_fungsional_snapshot,
                    optional($row->attended_at)->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($out);
        }, 'attendance-export.csv');
    }

    public function exportMinutesCsv(): StreamedResponse
    {
        $rows = MinuteIssue::query()->with(['eventMinute.event.site', 'eventMinute.event.meetingType'])->latest()->get();
        return response()->streamDownload(function () use ($rows): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Tanggal', 'Week', 'Site', 'Jenis Meeting', 'Kode Event', 'Judul Notulen', 'Notulis', 'Lokasi', 'Section', 'No', 'Catatan Meeting', 'Issued By', 'PIC', 'Batas Waktu', 'Status', 'Keterangan', 'Updated At']);
            foreach ($rows as $row) {
                $event = optional($row->eventMinute)->event;
                fputcsv($out, [
                    optional($event?->meeting_date)->format('Y-m-d'),
                    $event?->week,
                    optional($event?->site)->name,
                    optional($event?->meetingType)->name,
                    $event?->event_code,
                    $row->eventMinute?->title,
                    $row->eventMinute?->notulis,
                    $row->eventMinute?->location,
                    $row->section,
                    $row->nomor,
                    $row->catatan_meeting,
                    $row->issued_by,
                    $row->pic,
                    optional($row->due_date)->format('Y-m-d'),
                    $row->computed_status,
                    $row->keterangan,
                    optional($row->updated_at)->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($out);
        }, 'minutes-export.csv');
    }

    public function performance(Request $request): View
    {
        $events = Event::query()->with(['site', 'attendances'])->get()->groupBy('week');
        $trend = $events->map(fn ($weekEvents, $week): array => [
            'week' => $week,
            'rate' => round($weekEvents->avg(fn (Event $event): float => $event->attendanceRate()), 2),
        ])->values();

        return view('sid-meeting.performance', [
            'trend' => $trend,
            'totalSite' => Site::query()->count(),
            'avgRate' => round($trend->avg('rate') ?? 0, 2),
            'weekCoverage' => $trend->count(),
            'companyRows' => $this->companyPerformanceRows(),
        ]);
    }

    public function semantic(Request $request, SemanticSimilarityService $service): View
    {
        $threshold = (float) $request->input('threshold', 55);
        $crossSiteOnly = $request->boolean('cross_site_only');
        $pairs = $service->getSimilarityPairs($threshold, $crossSiteOnly, $request->input('q'));
        $groups = $service->getRepeatedGroups($pairs);

        return view('sid-meeting.semantic', [
            'threshold' => $threshold,
            'crossSiteOnly' => $crossSiteOnly,
            'pairs' => $pairs,
            'groups' => $groups,
            'summary' => [
                'total_issue' => MinuteIssue::query()->count(),
                'similar_pairs' => $pairs->count(),
                'repeated_groups' => $groups->count(),
                'cross_site_pairs' => $pairs->where('cross_site', true)->count(),
            ],
        ]);
    }

    public function exportSimilarityCsv(Request $request, SemanticSimilarityService $service): StreamedResponse
    {
        $pairs = $service->getSimilarityPairs((float) $request->input('threshold', 55), $request->boolean('cross_site_only'));
        return response()->streamDownload(function () use ($pairs): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Similarity', 'Level', 'Site A', 'Issue A', 'Site B', 'Issue B', 'Action Signal']);
            foreach ($pairs as $pair) {
                fputcsv($out, [$pair['similarity'], $pair['level'], $pair['site_a'], $pair['issue_a'], $pair['site_b'], $pair['issue_b'], $pair['action_signal']]);
            }
            fclose($out);
        }, 'similarity-export.csv');
    }

    public function apiBootstrap(): JsonResponse
    {
        $events = Event::query()
            ->with(['site', 'meetingType', 'eventMinute.issues'])
            ->orderByDesc('meeting_date')
            ->get();

        $attendance = Attendance::query()
            ->with(['event:id,event_code', 'employee:id,kode_sid,nama'])
            ->orderByDesc('attended_at')
            ->get()
            ->map(function (Attendance $row): array {
                return [
                    'id' => (string) $row->id,
                    'eventId' => (string) $row->event_id,
                    'sid' => $row->kode_sid,
                    'name' => $row->nama_snapshot,
                    'company' => $row->perusahaan_snapshot,
                    'structuralPosition' => $row->jabatan_struktural_snapshot,
                    'functionalPosition' => $row->jabatan_fungsional_snapshot,
                    'timestamp' => optional($row->attended_at)->toIso8601String(),
                    'source' => strtoupper($row->input_method),
                    'employeeId' => (string) $row->employee_id,
                ];
            })
            ->values();

        $companies = Company::query()->with('sites:id,name')->orderBy('name')->get()->map(function (Company $company): array {
            return [
                'id' => 'COMP-' . $company->id,
                'name' => $company->name,
                'sites' => $company->sites->pluck('name')->values()->all(),
                'createdAt' => optional($company->created_at)->toIso8601String(),
                'updatedAt' => optional($company->updated_at)->toIso8601String(),
            ];
        })->values();

        $meetingTypes = MeetingType::query()->where('is_active', true)->orderBy('name')->pluck('name')->values();

        $eventRows = $events->map(function (Event $event): array {
            $minute = $event->eventMinute;
            $issues = collect($minute?->issues ?? []);
            return [
                'id' => (string) $event->id,
                'code' => $event->event_code,
                'qrToken' => $event->qr_token,
                'meetingType' => optional($event->meetingType)->name,
                'site' => optional($event->site)->name,
                'date' => optional($event->meeting_date)->format('Y-m-d'),
                'week' => $event->week,
                'startTime' => substr((string) $event->start_time, 0, 5),
                'endTime' => substr((string) $event->end_time, 0, 5),
                'manualStatus' => $event->computed_status,
                'closedAt' => optional($event->closed_at)->toIso8601String(),
                'createdAt' => optional($event->created_at)->toIso8601String(),
                'updatedAt' => optional($event->updated_at)->toIso8601String(),
                'minutes' => $minute ? [
                    'meetingTitle' => $minute->title,
                    'meetingType' => optional($event->meetingType)->name,
                    'meetingDate' => optional($event->meeting_date)->format('Y-m-d'),
                    'notulis' => $minute->notulis,
                    'location' => $minute->location,
                    'updatedAt' => optional($minute->updated_at)->toIso8601String(),
                    'enviroIssues' => $issues->where('section', 'enviro')->values()->map(fn ($i): array => $this->mapIssueToLegacy($i))->all(),
                    'safetyIssues' => $issues->where('section', 'safety')->values()->map(fn ($i): array => $this->mapIssueToLegacy($i))->all(),
                    'generalIssues' => $issues->where('section', 'general')->values()->map(fn ($i): array => $this->mapIssueToLegacy($i))->all(),
                ] : null,
            ];
        })->values();

        return response()->json([
            'events' => $eventRows,
            'attendance' => $attendance,
            'companies' => $companies,
            'meetingTypes' => $meetingTypes,
        ]);
    }

    public function apiStoreEvent(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'meeting_type' => 'required|string|max:255',
            'site' => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'week' => 'required|string|max:16',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $meetingType = MeetingType::query()->firstOrCreate(['name' => $payload['meeting_type']], ['is_active' => true]);
        $site = Site::query()->firstOrCreate(['name' => $payload['site']], ['is_active' => true]);

        $event = $this->createEventWithUniqueCode(function (string $eventCode) use ($payload, $meetingType, $site): Event {
            return Event::query()->create([
                'event_code' => $eventCode,
                'qr_token' => (string) Str::uuid(),
                'meeting_type_id' => $meetingType->id,
                'site_id' => $site->id,
                'meeting_date' => $payload['meeting_date'],
                'week' => $payload['week'],
                'start_time' => $payload['start_time'],
                'end_time' => $payload['end_time'],
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);
        }, $payload['meeting_date']);

        return response()->json(['ok' => true, 'id' => $event->id]);
    }

    public function apiStoreCompany(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => 'required|string|max:255',
            'sites' => 'array',
            'sites.*' => 'string|max:255',
        ]);

        $company = Company::query()->firstOrCreate(['name' => $payload['name']]);
        $siteIds = collect($payload['sites'] ?? [])
            ->map(fn (string $siteName): int => Site::query()->firstOrCreate(['name' => $siteName], ['is_active' => true])->id)
            ->unique()
            ->values();
        $company->sites()->sync($siteIds->mapWithKeys(fn ($siteId): array => [$siteId => ['is_required' => true]])->all());

        return response()->json(['ok' => true, 'id' => $company->id]);
    }

    public function apiStoreAttendance(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'event_id' => 'required|exists:events,id',
            'kode_sid' => 'required|string|max:50',
            'input_method' => 'nullable|in:qr,manual',
        ]);

        $event = Event::query()->findOrFail($payload['event_id']);
        $employee = Employee::query()->with('company')->where('kode_sid', $payload['kode_sid'])->where('is_active', true)->first();
        if (!$employee) {
            return response()->json(['ok' => false, 'message' => 'SID tidak ditemukan'], 422);
        }

        $exists = Attendance::query()->where('event_id', $event->id)->where('employee_id', $employee->id)->exists();
        if ($exists) {
            return response()->json(['ok' => false, 'message' => 'SID sudah melakukan absensi pada event ini'], 422);
        }

        Attendance::query()->create([
            'event_id' => $event->id,
            'employee_id' => $employee->id,
            'kode_sid' => $employee->kode_sid,
            'nama_snapshot' => $employee->nama,
            'perusahaan_snapshot' => optional($employee->company)->name,
            'jabatan_struktural_snapshot' => $employee->jabatan_struktural,
            'jabatan_fungsional_snapshot' => $employee->jabatan_fungsional,
            'attended_at' => now(),
            'input_method' => $payload['input_method'] ?? 'manual',
        ]);

        return response()->json(['ok' => true]);
    }

    public function apiSaveMinutes(Request $request, Event $event): JsonResponse
    {
        $payload = $request->validate([
            'title' => 'nullable|string|max:255',
            'notulis' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'meeting_type' => 'nullable|string|max:255',
            'meeting_date' => 'nullable|date',
            'issues' => 'array',
            'issues.*.section' => 'required|in:enviro,safety,general',
            'issues.*.nomor' => 'nullable|integer|min:1',
            'issues.*.note' => 'nullable|string',
            'issues.*.issuedBy' => 'nullable|string|max:255',
            'issues.*.pic' => 'nullable|string|max:255',
            'issues.*.dueDate' => 'nullable|date',
            'issues.*.status' => 'nullable|in:Open,Progress,Closed,Overdue',
            'issues.*.remark' => 'nullable|string',
        ]);

        DB::transaction(function () use ($event, $payload): void {
            $minute = EventMinute::query()->updateOrCreate(
                ['event_id' => $event->id],
                [
                    'title' => $payload['title'] ?? null,
                    'notulis' => $payload['notulis'] ?? null,
                    'location' => $payload['location'] ?? null,
                    'updated_by' => auth()->id(),
                ]
            );

            if (filled($payload['meeting_type'] ?? null)) {
                $meetingType = MeetingType::query()->firstOrCreate(
                    ['name' => trim((string) $payload['meeting_type'])],
                    ['is_active' => true]
                );
                $event->update(['meeting_type_id' => $meetingType->id]);
            }

            if (filled($payload['meeting_date'] ?? null)) {
                $event->update(['meeting_date' => $payload['meeting_date']]);
            }

            if (! array_key_exists('issues', $payload)) {
                return;
            }

            $minute->issues()->delete();

            foreach ($payload['issues'] as $index => $issue) {
                $note = trim((string) ($issue['note'] ?? ''));
                $issuedBy = trim((string) ($issue['issuedBy'] ?? ''));
                $pic = trim((string) ($issue['pic'] ?? ''));
                $remark = trim((string) ($issue['remark'] ?? ''));
                $dueRaw = $issue['dueDate'] ?? null;
                $hasContent = $note !== ''
                    || $issuedBy !== ''
                    || $pic !== ''
                    || $remark !== ''
                    || filled($dueRaw);

                if (! $hasContent) {
                    continue;
                }

                $minute->issues()->create([
                    'section' => $issue['section'],
                    'nomor' => (int) ($issue['nomor'] ?? ($index + 1)),
                    'catatan_meeting' => $note !== '' ? $note : '—',
                    'issued_by' => $issuedBy !== '' ? $issuedBy : null,
                    'pic' => $pic !== '' ? $pic : null,
                    'due_date' => filled($dueRaw) ? $dueRaw : null,
                    'status' => $issue['status'] ?? 'Open',
                    'keterangan' => $remark !== '' ? $remark : null,
                ]);
            }
        });

        return response()->json(['ok' => true]);
    }

    public function apiSync(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'events' => 'array',
            'attendance' => 'array',
            'companies' => 'array',
            'meetingTypes' => 'array',
        ]);

        DB::transaction(function () use ($payload): void {
            $eventRows = collect($payload['events'] ?? []);
            $companyRows = collect($payload['companies'] ?? []);
            $attendanceRows = collect($payload['attendance'] ?? []);
            $meetingTypeRows = collect($payload['meetingTypes'] ?? []);

            $meetingTypeNames = $meetingTypeRows
                ->map(fn ($name) => trim((string) $name))
                ->filter()
                ->merge($eventRows->map(fn ($row) => trim((string) ($row['meetingType'] ?? '')))->filter())
                ->unique()
                ->values();

            $meetingTypeMap = [];
            foreach ($meetingTypeNames as $name) {
                $meetingType = MeetingType::query()->firstOrCreate(['name' => $name], ['is_active' => true]);
                $meetingTypeMap[$name] = $meetingType->id;
            }

            $siteNames = $eventRows->map(fn ($row) => trim((string) ($row['site'] ?? '')))->filter()
                ->merge($companyRows->flatMap(fn ($row) => collect($row['sites'] ?? [])->map(fn ($site) => trim((string) $site))->filter()))
                ->unique()
                ->values();
            $siteMap = [];
            foreach ($siteNames as $name) {
                $site = Site::query()->firstOrCreate(['name' => $name], ['is_active' => true]);
                $siteMap[$name] = $site->id;
            }

            foreach ($companyRows as $row) {
                $name = trim((string) ($row['name'] ?? ''));
                if ($name === '') {
                    continue;
                }
                $company = Company::query()->firstOrCreate(['name' => $name], ['is_active' => true]);
                $sync = collect($row['sites'] ?? [])->mapWithKeys(function ($siteName) use ($siteMap): array {
                    $siteId = $siteMap[trim((string) $siteName)] ?? null;
                    return $siteId ? [$siteId => ['is_required' => true]] : [];
                })->all();
                if ($sync !== []) {
                    $company->sites()->sync($sync);
                }
            }

            $eventIdMap = [];
            $eventIds = [];
            foreach ($eventRows as $row) {
                $eventCode = trim((string) ($row['code'] ?? ''));
                if ($eventCode === '') {
                    $eventCode = $this->generateEventCode((string) ($row['date'] ?? now()->toDateString()));
                }
                $meetingTypeName = trim((string) ($row['meetingType'] ?? ''));
                $siteName = trim((string) ($row['site'] ?? ''));
                $meetingTypeId = $meetingTypeMap[$meetingTypeName] ?? null;
                $siteId = $siteMap[$siteName] ?? null;
                if (!$meetingTypeId || !$siteId) {
                    continue;
                }

                $statusText = strtolower((string) ($row['manualStatus'] ?? 'draft'));
                $status = match ($statusText) {
                    'closed' => 'closed',
                    'overrun' => 'overrun',
                    'open' => 'open',
                    'upcoming' => 'upcoming',
                    'expired' => 'expired',
                    default => 'draft',
                };

                $event = Event::query()->updateOrCreate(
                    ['event_code' => $eventCode],
                    [
                        'qr_token' => (string) ($row['qrToken'] ?? Str::uuid()),
                        'meeting_type_id' => $meetingTypeId,
                        'site_id' => $siteId,
                        'meeting_date' => $row['date'] ?? now()->toDateString(),
                        'week' => $row['week'] ?? Carbon::parse($row['date'] ?? now()->toDateString())->format('o-\WW'),
                        'start_time' => ($row['startTime'] ?? '08:00') . ':00',
                        'end_time' => ($row['endTime'] ?? '09:00') . ':00',
                        'status' => $status,
                        'closed_at' => $row['closedAt'] ?? null,
                        'created_by' => auth()->id(),
                    ]
                );

                $clientId = (string) ($row['id'] ?? $eventCode);
                $eventIdMap[$clientId] = $event->id;
                $eventIds[] = $event->id;

                $minutes = $row['minutes'] ?? null;
                if (is_array($minutes)) {
                    $minute = EventMinute::query()->updateOrCreate(
                        ['event_id' => $event->id],
                        [
                            'title' => $minutes['meetingTitle'] ?? null,
                            'notulis' => $minutes['notulis'] ?? null,
                            'location' => $minutes['location'] ?? null,
                            'updated_by' => auth()->id(),
                        ]
                    );
                    $minute->issues()->delete();
                    foreach (['enviro' => 'enviroIssues', 'safety' => 'safetyIssues', 'general' => 'generalIssues'] as $section => $key) {
                        foreach (($minutes[$key] ?? []) as $index => $issue) {
                            $note = trim((string) ($issue['note'] ?? ''));
                            if ($note === '') {
                                continue;
                            }
                            $minute->issues()->create([
                                'section' => $section,
                                'nomor' => (int) ($issue['nomor'] ?? ($index + 1)),
                                'catatan_meeting' => $note,
                                'issued_by' => $issue['issuedBy'] ?? null,
                                'pic' => $issue['pic'] ?? null,
                                'due_date' => $issue['dueDate'] ?? null,
                                'status' => $issue['status'] ?? 'Open',
                                'keterangan' => $issue['remark'] ?? null,
                            ]);
                        }
                    }
                }
            }

            if ($eventIds !== []) {
                Event::query()->whereNotIn('id', $eventIds)->delete();
            }

            Attendance::query()->delete();
            foreach ($attendanceRows as $row) {
                $clientEventId = (string) ($row['eventId'] ?? '');
                $eventId = $eventIdMap[$clientEventId] ?? (is_numeric($clientEventId) ? (int) $clientEventId : null);
                if (!$eventId) {
                    continue;
                }

                $companyName = trim((string) ($row['company'] ?? 'Tidak Ada Perusahaan'));
                $company = Company::query()->firstOrCreate(['name' => $companyName], ['is_active' => true]);
                $sid = trim((string) ($row['sid'] ?? ''));
                if ($sid === '') {
                    continue;
                }
                $employee = Employee::query()->firstOrCreate(
                    ['kode_sid' => $sid],
                    [
                        'nama' => $row['name'] ?? $sid,
                        'company_id' => $company->id,
                        'jabatan_struktural' => $row['structuralPosition'] ?? null,
                        'jabatan_fungsional' => $row['functionalPosition'] ?? null,
                        'is_active' => true,
                    ]
                );

                Attendance::query()->create([
                    'event_id' => $eventId,
                    'employee_id' => $employee->id,
                    'kode_sid' => $employee->kode_sid,
                    'nama_snapshot' => $row['name'] ?? $employee->nama,
                    'perusahaan_snapshot' => $companyName,
                    'jabatan_struktural_snapshot' => $row['structuralPosition'] ?? $employee->jabatan_struktural,
                    'jabatan_fungsional_snapshot' => $row['functionalPosition'] ?? $employee->jabatan_fungsional,
                    'attended_at' => $row['timestamp'] ?? now()->toDateTimeString(),
                    'input_method' => strtolower((string) ($row['source'] ?? 'manual')) === 'qr' ? 'qr' : 'manual',
                ]);
            }
        });

        return response()->json(['ok' => true]);
    }

    private function generateEventCode(string $meetingDate): string
    {
        $date = Carbon::parse($meetingDate)->format('Ymd');
        $prefix = "EV-{$date}-";
        $meetingDateOnly = Carbon::parse($meetingDate)->toDateString();

        return DB::transaction(function () use ($prefix, $date, $meetingDateOnly): string {
            $lastCode = Event::query()
                ->whereDate('meeting_date', $meetingDateOnly)
                ->where('event_code', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderByDesc('event_code')
                ->value('event_code');

            $next = 1;
            if (is_string($lastCode) && str_starts_with($lastCode, $prefix)) {
                $suffix = (int) substr($lastCode, -4);
                $next = max(1, $suffix + 1);
            }

            return sprintf('EV-%s-%04d', $date, $next);
        }, 3);
    }

    /**
     * Buat event dengan event_code unik walaupun ada concurrent insert.
     */
    private function createEventWithUniqueCode(callable $creator, string $meetingDate): Event
    {
        $attempts = 6;
        for ($i = 0; $i < $attempts; $i++) {
            $eventCode = $this->generateEventCode($meetingDate);
            try {
                /** @var Event $event */
                $event = $creator($eventCode);
                return $event;
            } catch (\Illuminate\Database\QueryException $e) {
                $message = (string) $e->getMessage();
                $isDuplicate = $e->getCode() === '23000'
                    && str_contains($message, 'Duplicate entry')
                    && str_contains($message, 'events_event_code_unique');
                if ($isDuplicate && $i < $attempts - 1) {
                    usleep(40_000); // beri jeda kecil agar hitungan berikutnya bergeser
                    continue;
                }
                throw $e;
            }
        }

        // Seharusnya tidak pernah sampai sini.
        throw new \RuntimeException('Gagal membuat event_code unik setelah beberapa percobaan.');
    }

    private function companyPerformanceRows()
    {
        $companies = Company::query()->with(['employees.attendances.event'])->get();
        $rank = 1;
        return $companies->map(function (Company $company) use (&$rank): array {
            $totalAbsensi = Attendance::query()->where('perusahaan_snapshot', $company->name)->count();
            $expectedEvent = Event::query()->count();
            $eventHadir = Attendance::query()->where('perusahaan_snapshot', $company->name)->distinct('event_id')->count('event_id');
            $rate = $expectedEvent > 0 ? round(($eventHadir / $expectedEvent) * 100, 2) : 0;
            $label = $rate >= 90 ? 'Excellent' : ($rate >= 75 ? 'Good' : ($rate >= 50 ? 'Need Attention' : 'Critical'));
            return [
                'rank' => $rank++,
                'company' => $company->name,
                'expected_event' => $expectedEvent,
                'event_hadir' => $eventHadir,
                'total_absensi' => $totalAbsensi,
                'rate' => $rate,
                'label' => $label,
            ];
        });
    }

    private function mapIssueToLegacy(MinuteIssue $issue): array
    {
        return [
            'note' => $issue->catatan_meeting,
            'issuedBy' => $issue->issued_by,
            'pic' => $issue->pic,
            'dueDate' => optional($issue->due_date)->format('Y-m-d'),
            'status' => $issue->computed_status,
            'remark' => $issue->keterangan,
            'nomor' => $issue->nomor,
        ];
    }
}
