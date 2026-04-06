<?php

declare(strict_types=1);

namespace App\Services\SistemRoster;

use App\Models\DailyOperationPlan;
use App\Models\RosterPlanning;
use App\Services\ClickHouseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Filter site planning (IKK/DOP/Roster acuan): konstanta UI, pola match sumber data, dan query bantu.
 */
final class PlanningSiteService
{
    /** @var list<string> */
    public const FILTER_SITES = [
        'BMO 1',
        'BMO 2',
        'BMO 3',
        'GMO',
        'LMO',
        'SMO',
        'PMO',
        'MARINE',
        'HOTE',
        'EXPLORASI',
    ];

    /**
     * Pola teks untuk mencocokkan site di DOP & IKK (substring).
     *
     * @var array<string, list<string>>
     */
    private const SITE_MATCH_PATTERNS = [
        'BMO 1' => ['BMO 1', 'BMO1'],
        'BMO 2' => ['BMO 2', 'BMO2'],
        'BMO 3' => ['BMO 3', 'BMO3'],
        'GMO' => ['GMO'],
        'LMO' => ['LMO'],
        'SMO' => ['SMO'],
        'PMO' => ['PMO'],
        'MARINE' => ['MARINE'],
        'HOTE' => ['HOTE', 'HO'],
        'EXPLORASI' => ['EXPLORASI', 'Eksplorasi', 'Explorasi', 'Exploration'],
    ];

    /**
     * Label site pada baris roster acuan (nilai di ROSTER_REFERENCE_TABLES) yang ditampilkan per tab.
     * Site tanpa tabel roster → array kosong (tab tetap menampilkan DOP + IKK).
     *
     * @var array<string, list<string>>
     */
    private const FILTER_TO_ROSTER_SITE_LABELS = [
        'BMO 1' => ['BMO 1'],
        'BMO 2' => [],
        'BMO 3' => ['BMO 3'],
        'GMO' => ['GMO'],
        'LMO' => ['LMO'],
        'SMO' => [],
        'PMO' => [],
        'MARINE' => [],
        'HOTE' => ['HOTE'],
        'EXPLORASI' => [],
    ];

    public function normalizeFilterSite(?string $filterSite): string
    {
        $filterSite = trim((string) ($filterSite ?? ''));
        if ($filterSite === '' || ! in_array($filterSite, self::FILTER_SITES, true)) {
            return '';
        }

        return $filterSite;
    }

    /**
     * Tab + dropdown: urutan konsisten dengan FILTER_SITES.
     *
     * @return list<array{value: string, label: string, slug: string}>
     */
    public function getPlanningSiteTabs(): array
    {
        $tabs = [
            [
                'value' => '',
                'label' => 'Semua',
                'slug' => 'semua',
            ],
        ];
        foreach (self::FILTER_SITES as $site) {
            $tabs[] = [
                'value' => $site,
                'label' => $site,
                'slug' => Str::slug($site),
            ];
        }

        return $tabs;
    }

    /**
     * Apakah baris roster acuan dengan label site (mis. "BMO 1") ditampilkan untuk filter tab ini.
     */
    public function rosterSiteLabelMatchesFilter(string $rosterSiteLabel, string $filterSite): bool
    {
        if ($filterSite === '') {
            return true;
        }
        $allowed = self::FILTER_TO_ROSTER_SITE_LABELS[$filterSite] ?? null;
        if ($allowed === null) {
            return strcasecmp(trim($rosterSiteLabel), trim($filterSite)) === 0;
        }

        return in_array($rosterSiteLabel, $allowed, true);
    }

    /**
     * Filter daftar planning: IKK, DOP, dan baris Roster yang sudah tersimpan di roster_plannings.
     *
     * IKK/DOP: ID dari sumber (daily_operation_plans, ikk_work_permit) + fallback kolom site.
     * Roster (source_type = Roster): filter lewat kolom site di roster_plannings (sama pola seperti IKK/DOP).
     */
    public function applyIkkDopSourceFilter(Builder $query, string $start, string $end, string $filterSite): void
    {
        if ($filterSite === '') {
            $query->whereIn('source_type', ['IKK', 'DOP', 'Roster']);

            return;
        }

        $dopIdsStr = array_map('strval', $this->getFilteredDopIdsForPlanning($start, $end, $filterSite));
        $ikkWpIds = $this->getFilteredIkkWpIdsFromClickHouse($start, $end, $filterSite);
        $patterns = $this->getSiteMatchPatternsForFilter($filterSite);

        $query->where(function (Builder $outer) use ($dopIdsStr, $ikkWpIds, $patterns): void {
            $outer->where(function (Builder $ikkdop) use ($dopIdsStr, $ikkWpIds, $patterns): void {
                $ikkdop->whereIn('source_type', ['IKK', 'DOP']);
                if ($ikkWpIds === null) {
                    $this->applyIkkDopFilterClickHouseUnavailable($ikkdop, $dopIdsStr, $patterns);
                } else {
                    $ikkStr = array_map('strval', $ikkWpIds);
                    $this->applyIkkDopFilterClickHouseAvailable($ikkdop, $dopIdsStr, $ikkStr, $patterns);
                }
            });

            if ($patterns !== []) {
                $outer->orWhere(function (Builder $r) use ($patterns): void {
                    $r->where('source_type', 'Roster');
                    $r->where(function ($sub) use ($patterns): void {
                        $this->applySiteLikePatterns($sub, $patterns);
                    });
                });
            }
        });
    }

    /**
     * ClickHouse tidak tersedia: IKK difilter lewat kolom site di roster_plannings; DOP lewat ID sumber atau site.
     */
    private function applyIkkDopFilterClickHouseUnavailable(Builder $query, array $dopIdsStr, array $patterns): void
    {
        $query->where(function ($q) use ($dopIdsStr, $patterns) {
            if (empty($dopIdsStr) && empty($patterns)) {
                $q->whereRaw('0 = 1');

                return;
            }

            $hasDopClause = ! empty($dopIdsStr) || ! empty($patterns);
            if ($hasDopClause) {
                $q->where(function ($q2) use ($dopIdsStr, $patterns): void {
                    $this->applyDopRowMatch($q2, $dopIdsStr, $patterns);
                });
            }

            if (! empty($patterns)) {
                $method = $hasDopClause ? 'orWhere' : 'where';
                $q->{$method}(function ($q2) use ($patterns): void {
                    $this->applyIkkRowMatch($q2, [], $patterns);
                });
            }
        });
    }

    /**
     * Baris DOP: source_type DOP dan (source_id ∈ daftar DOP per site/periode ATAU kolom site cocok pola tab).
     */
    private function applyDopRowMatch(Builder $q2, array $dopIdsStr, array $patterns): void
    {
        $q2->where('source_type', 'DOP');
        $q2->where(function ($sub) use ($dopIdsStr, $patterns): void {
            $this->applyDopSourceIdOrSitePatterns($sub, $dopIdsStr, $patterns);
        });
    }

    /**
     * @param  list<string>  $ikkStr
     */
    private function applyIkkRowMatch(Builder $q2, array $ikkStr, array $patterns): void
    {
        $q2->where('source_type', 'IKK');
        $q2->where(function ($sub) use ($ikkStr, $patterns): void {
            $this->applyIkkSourceIdOrSitePatterns($sub, $ikkStr, $patterns);
        });
    }

    private function applyDopSourceIdOrSitePatterns(Builder $sub, array $dopIdsStr, array $patterns): void
    {
        if (! empty($dopIdsStr) && ! empty($patterns)) {
            $sub->whereIn('source_id', $dopIdsStr)
                ->orWhere(function ($w) use ($patterns): void {
                    $this->applySiteLikePatterns($w, $patterns);
                });
        } elseif (! empty($dopIdsStr)) {
            $sub->whereIn('source_id', $dopIdsStr);
        } elseif (! empty($patterns)) {
            $sub->where(function ($w) use ($patterns): void {
                $this->applySiteLikePatterns($w, $patterns);
            });
        } else {
            $sub->whereRaw('0 = 1');
        }
    }

    /**
     * @param  list<string>  $ikkStr
     */
    private function applyIkkSourceIdOrSitePatterns(Builder $sub, array $ikkStr, array $patterns): void
    {
        if (! empty($ikkStr) && ! empty($patterns)) {
            $sub->whereIn('source_id', $ikkStr)
                ->orWhere(function ($w) use ($patterns): void {
                    $this->applySiteLikePatterns($w, $patterns);
                });
        } elseif (! empty($ikkStr)) {
            $sub->whereIn('source_id', $ikkStr);
        } elseif (! empty($patterns)) {
            $sub->where(function ($w) use ($patterns): void {
                $this->applySiteLikePatterns($w, $patterns);
            });
        } else {
            $sub->whereRaw('0 = 1');
        }
    }

    /**
     * ClickHouse tersedia: ID IKK dari CH + fallback site; DOP dari daily_operation_plans + fallback site.
     *
     * Penting: gabungkan DOP | IKK dengan `where(function{DOP})->orWhere(function{IKK})` pada builder yang sama
     * (bukan `where($closureDop)->orWhere($closureIkk)`), agar SQL setara dengan jalur CH unavailable.
     *
     * @param  list<string>  $ikkStr
     */
    private function applyIkkDopFilterClickHouseAvailable(Builder $query, array $dopIdsStr, array $ikkStr, array $patterns): void
    {
        $query->where(function ($q) use ($dopIdsStr, $ikkStr, $patterns): void {
            if (empty($dopIdsStr) && empty($ikkStr) && empty($patterns)) {
                $q->whereRaw('0 = 1');

                return;
            }

            $hasDop = ! empty($dopIdsStr) || ! empty($patterns);
            $hasIkk = ! empty($ikkStr) || ! empty($patterns);

            if ($hasDop && $hasIkk) {
                $q->where(function ($q2) use ($dopIdsStr, $patterns): void {
                    $this->applyDopRowMatch($q2, $dopIdsStr, $patterns);
                })->orWhere(function ($q2) use ($ikkStr, $patterns): void {
                    $this->applyIkkRowMatch($q2, $ikkStr, $patterns);
                });
            } elseif ($hasDop) {
                $q->where(function ($q2) use ($dopIdsStr, $patterns): void {
                    $this->applyDopRowMatch($q2, $dopIdsStr, $patterns);
                });
            } elseif ($hasIkk) {
                $q->where(function ($q2) use ($ikkStr, $patterns): void {
                    $this->applyIkkRowMatch($q2, $ikkStr, $patterns);
                });
            }
        });
    }

    private function applySiteLikePatterns(Builder $q, array $patterns): void
    {
        foreach ($patterns as $i => $pat) {
            if ($i === 0) {
                $q->where('site', 'like', '%' . $pat . '%');
            } else {
                $q->orWhere('site', 'like', '%' . $pat . '%');
            }
        }
    }

    /**
     * @return list<string>
     */
    public function getSiteMatchPatternsForFilter(string $filterSite): array
    {
        if ($filterSite === '' || ! isset(self::SITE_MATCH_PATTERNS[$filterSite])) {
            return [];
        }

        return self::SITE_MATCH_PATTERNS[$filterSite];
    }

    /**
     * ID DOP di daily_operation_plans yang cocok site (unit_id / site) & periode.
     *
     * @return list<int|string>
     */
    public function getFilteredDopIdsForPlanning(string $start, string $end, string $filterSite): array
    {
        $q = DailyOperationPlan::query()->whereBetween('tanggal', [$start, $end]);
        $patterns = $this->getSiteMatchPatternsForFilter($filterSite);
        if (! empty($patterns)) {
            $q->where(function ($w) use ($patterns) {
                foreach ($patterns as $i => $pat) {
                    $like = '%' . $pat . '%';
                    if ($i === 0) {
                        $w->where(function ($w2) use ($like) {
                            $w2->where('unit_id', 'like', $like)
                                ->orWhere('site', 'like', $like);
                        });
                    } else {
                        $w->orWhere(function ($w2) use ($like) {
                            $w2->where('unit_id', 'like', $like)
                                ->orWhere('site', 'like', $like);
                        });
                    }
                }
            });
        }

        return $q->orderBy('id')->pluck('id')->all();
    }

    /**
     * ID work permit IKK dari ClickHouse (ra_site_name) yang overlap periode & status.
     * Null = ClickHouse tidak tersedia → fallback LIKE di applyIkkDopSourceFilter.
     *
     * @return list<string>|null
     */
    public function getFilteredIkkWpIdsFromClickHouse(string $startDate, string $endDate, string $filterSite): ?array
    {
        $patterns = $this->getSiteMatchPatternsForFilter($filterSite);
        if (empty($patterns)) {
            return [];
        }

        try {
            if (! class_exists(ClickHouseService::class)) {
                return null;
            }
            $clickHouse = app(ClickHouseService::class);
            if (! method_exists($clickHouse, 'query') || ! $clickHouse->isConnected()) {
                return null;
            }

            $startEsc = addslashes($startDate);
            $endEsc = addslashes($endDate);
            $siteConds = [];
            foreach ($patterns as $pat) {
                $p = addslashes($pat);
                $siteConds[] = "(lower(ifNull(toString(ra_site_name), '')) LIKE lower('%{$p}%'))";
            }
            $siteSql = implode(' OR ', $siteConds);
            $sql = "
                SELECT DISTINCT toString(id) AS id
                FROM hse_automation.ikk_work_permit
                WHERE (deleted_at IS NULL OR deleted_at = toDateTime(0))
                  AND toDate(start_date) <= toDate('{$endEsc}')
                  AND toDate(end_date) >= toDate('{$startEsc}')
                  AND status IN ('APPROVED', 'EXPIRED')
                  AND ({$siteSql})
            ";
            $rows = $clickHouse->query($sql);
            $ids = [];
            foreach ($rows ?? [] as $row) {
                $id = $row['id'] ?? null;
                if (is_array($id) && isset($id[0])) {
                    $id = $id[0];
                }
                if ($id !== null && $id !== '') {
                    $ids[(string) $id] = true;
                }
            }

            return array_keys($ids);
        } catch (\Throwable $e) {
            Log::warning('PlanningSiteService getFilteredIkkWpIdsFromClickHouse: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Daftar perusahaan untuk dropdown: dari baris roster_plannings IKK/DOP yang lolos filter sumber yang sama.
     *
     * @return Collection<int, string>
     */
    public function getPerusahaanListForPlanningFilter(string $filterStartDate, string $filterEndDate, string $filterSite): Collection
    {
        $q = RosterPlanning::query()
            ->whereBetween('tanggal', [$filterStartDate, $filterEndDate])
            ->whereNotNull('perusahaan_pic')
            ->where('perusahaan_pic', '!=', '');
        $this->applyIkkDopSourceFilter($q, $filterStartDate, $filterEndDate, $filterSite);

        return $q->distinct()->orderBy('perusahaan_pic')->pluck('perusahaan_pic');
    }
}
