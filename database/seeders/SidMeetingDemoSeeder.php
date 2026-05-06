<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Event;
use App\Models\EventMinute;
use App\Models\MeetingType;
use App\Models\MinuteIssue;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SidMeetingDemoSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate(['email' => 'admin.sid@example.com'], ['name' => 'SID Admin', 'password' => Hash::make('password'), 'role' => 'admin']);
        User::query()->firstOrCreate(['email' => 'viewer.sid@example.com'], ['name' => 'SID Viewer', 'password' => Hash::make('password'), 'role' => 'viewer']);

        $sites = collect(['BMO 1', 'BMO 2', 'BMO 3', 'GMO', 'SMO', 'LMO', 'Marine', 'HOTE'])->map(fn ($name) => Site::query()->firstOrCreate(['name' => $name]));
        $types = collect(['Safety Talk', 'P5M', 'Toolbox Meeting', 'Incident Review', 'HSE Committee', 'Weekly Safety Meeting', 'Monthly Contractor Meeting'])
            ->map(fn ($name) => MeetingType::query()->firstOrCreate(['name' => $name]));

        $companies = collect(['PT Alpha', 'PT Bravo', 'PT Cakra', 'PT Delta'])->map(fn ($name) => Company::query()->firstOrCreate(['name' => $name]));
        foreach ($companies as $company) {
            $company->sites()->syncWithoutDetaching($sites->take(4)->mapWithKeys(fn ($site) => [$site->id => ['is_required' => true]])->all());
        }

        foreach (range(1, 40) as $i) {
            Employee::query()->firstOrCreate(
                ['kode_sid' => 'SID' . str_pad((string) $i, 4, '0', STR_PAD_LEFT)],
                ['nama' => 'Employee ' . $i, 'company_id' => $companies->random()->id, 'jabatan_struktural' => 'Supervisor', 'jabatan_fungsional' => 'Safety', 'is_active' => true]
            );
        }

        foreach (range(1, 10) as $i) {
            $date = now()->subDays(10 - $i)->toDateString();
            $event = Event::query()->firstOrCreate(
                ['event_code' => 'EV-' . now()->format('Ymd') . '-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT)],
                ['qr_token' => Str::random(40), 'meeting_type_id' => $types->random()->id, 'site_id' => $sites->random()->id, 'meeting_date' => $date, 'week' => now()->subDays(10 - $i)->format('o-\WW'), 'start_time' => '08:00:00', 'end_time' => '09:00:00', 'status' => 'closed']
            );
            $minute = EventMinute::query()->firstOrCreate(['event_id' => $event->id], ['title' => 'Notulen ' . $event->event_code, 'notulis' => 'Admin', 'location' => $event->site->name]);
            foreach (range(1, 3) as $n) {
                MinuteIssue::query()->firstOrCreate(
                    ['event_minute_id' => $minute->id, 'nomor' => $n, 'section' => ['enviro', 'safety', 'general'][($n - 1) % 3]],
                    ['catatan_meeting' => 'Temuan issue berulang area loading dan APD unit ' . $n, 'issued_by' => 'Lead', 'pic' => 'PIC ' . $n, 'due_date' => now()->addDays($n), 'status' => 'Open']
                );
            }
        }
    }
}
