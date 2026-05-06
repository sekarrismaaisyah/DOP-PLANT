<?php

namespace App\Services;

use App\Models\MinuteIssue;
use Illuminate\Support\Collection;

class SemanticSimilarityService
{
    private array $stopwords = [
        'yang', 'dan', 'di', 'ke', 'dari', 'untuk', 'pada', 'dengan', 'dalam',
        'adalah', 'ini', 'itu', 'karena', 'agar', 'atau', 'sebagai', 'oleh',
        'belum', 'sudah', 'telah', 'akan', 'perlu', 'harus', 'terkait', 'dilakukan', 'melakukan',
    ];

    public function normalizeText(string $text): string
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;

        return trim($text);
    }

    public function tokenize(string $text): array
    {
        $normalized = $this->normalizeText($text);
        if ($normalized === '') {
            return [];
        }

        return collect(explode(' ', $normalized))
            ->filter(fn (string $token): bool => $token !== '' && !in_array($token, $this->stopwords, true))
            ->values()
            ->all();
    }

    public function buildVector(array $tokens): array
    {
        $vector = [];
        foreach ($tokens as $token) {
            $vector[$token] = ($vector[$token] ?? 0) + 1;
        }

        return $vector;
    }

    public function cosineSimilarity(array $vectorA, array $vectorB): float
    {
        if ($vectorA === [] || $vectorB === []) {
            return 0.0;
        }

        $dot = 0;
        foreach ($vectorA as $term => $tfA) {
            $dot += $tfA * ($vectorB[$term] ?? 0);
        }

        $normA = sqrt(array_sum(array_map(fn ($val): float => $val * $val, $vectorA)));
        $normB = sqrt(array_sum(array_map(fn ($val): float => $val * $val, $vectorB)));

        if ($normA == 0.0 || $normB == 0.0) {
            return 0.0;
        }

        return $dot / ($normA * $normB);
    }

    public function getSimilarityPairs(float $threshold = 55, bool $crossSiteOnly = false, ?string $search = null): Collection
    {
        $issues = MinuteIssue::query()
            ->with(['eventMinute.event.site'])
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('catatan_meeting', 'like', '%' . $search . '%')
                        ->orWhere('pic', 'like', '%' . $search . '%');
                });
            })
            ->get();

        $prepared = $issues->map(function (MinuteIssue $issue): array {
            $tokens = $this->tokenize($issue->catatan_meeting);
            return [
                'issue' => $issue,
                'vector' => $this->buildVector($tokens),
            ];
        })->values();

        $pairs = collect();
        $ratio = $threshold / 100;
        for ($i = 0; $i < $prepared->count(); $i++) {
            for ($j = $i + 1; $j < $prepared->count(); $j++) {
                $issueA = $prepared[$i]['issue'];
                $issueB = $prepared[$j]['issue'];

                $siteA = optional(optional(optional($issueA->eventMinute)->event)->site)->name;
                $siteB = optional(optional(optional($issueB->eventMinute)->event)->site)->name;

                if ($crossSiteOnly && $siteA === $siteB) {
                    continue;
                }

                $similarity = $this->cosineSimilarity($prepared[$i]['vector'], $prepared[$j]['vector']);
                if ($similarity < $ratio) {
                    continue;
                }

                $similarityPercent = round($similarity * 100, 2);
                $isCrossSite = $siteA !== $siteB;
                $level = $similarityPercent >= 80 ? 'High Similarity' : ($similarityPercent >= 65 ? 'Medium Similarity' : 'Low Similarity');
                $actionSignal = $similarityPercent >= 75
                    ? ($isCrossSite ? 'Potential systemic issue' : 'Recurring local issue')
                    : 'Monitor pattern';

                $pairs->push([
                    'issue_a_id' => $issueA->id,
                    'issue_b_id' => $issueB->id,
                    'site_a' => $siteA,
                    'site_b' => $siteB,
                    'issue_a' => $issueA->catatan_meeting,
                    'issue_b' => $issueB->catatan_meeting,
                    'similarity' => $similarityPercent,
                    'level' => $level,
                    'action_signal' => $actionSignal,
                    'cross_site' => $isCrossSite,
                ]);
            }
        }

        return $pairs->sortByDesc('similarity')->values();
    }

    public function getRepeatedGroups(Collection $pairs): Collection
    {
        $graph = [];
        foreach ($pairs as $pair) {
            $a = (int) $pair['issue_a_id'];
            $b = (int) $pair['issue_b_id'];
            $graph[$a][] = $b;
            $graph[$b][] = $a;
        }

        $visited = [];
        $groups = collect();
        $groupNo = 1;
        foreach (array_keys($graph) as $node) {
            if (isset($visited[$node])) {
                continue;
            }

            $stack = [$node];
            $component = [];
            while ($stack !== []) {
                $current = array_pop($stack);
                if (isset($visited[$current])) {
                    continue;
                }
                $visited[$current] = true;
                $component[] = $current;
                foreach ($graph[$current] ?? [] as $neighbor) {
                    if (!isset($visited[$neighbor])) {
                        $stack[] = $neighbor;
                    }
                }
            }

            if (count($component) < 2) {
                continue;
            }

            $issues = MinuteIssue::query()->with(['eventMinute.event.site'])->whereIn('id', $component)->get();
            $sites = $issues->map(fn (MinuteIssue $issue): ?string => optional(optional(optional($issue->eventMinute)->event)->site)->name)
                ->filter()->unique()->values();
            $topTerms = $issues
                ->flatMap(fn (MinuteIssue $issue): array => $this->tokenize($issue->catatan_meeting))
                ->countBy()
                ->sortDesc()
                ->take(5)
                ->keys()
                ->values();

            $groups->push([
                'group' => 'G' . str_pad((string) $groupNo, 3, '0', STR_PAD_LEFT),
                'issue_ids' => $component,
                'issues' => $issues,
                'sites' => $sites,
                'issue_count' => count($component),
                'top_terms' => $topTerms,
            ]);
            $groupNo++;
        }

        return $groups;
    }
}
