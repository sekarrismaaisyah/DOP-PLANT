<?php

namespace Tests\Unit\SistemRoster;

use App\Services\SistemRoster\PlanningSiteService;
use PHPUnit\Framework\TestCase;

class PlanningSiteServiceTest extends TestCase
{
    public function test_roster_site_label_matches_filter_respects_tab_and_semua(): void
    {
        $s = new PlanningSiteService();

        $this->assertTrue($s->rosterSiteLabelMatchesFilter('BMO 1', ''));
        $this->assertTrue($s->rosterSiteLabelMatchesFilter('BMO 3', ''));

        $this->assertTrue($s->rosterSiteLabelMatchesFilter('BMO 1', 'BMO 1'));
        $this->assertFalse($s->rosterSiteLabelMatchesFilter('BMO 3', 'BMO 1'));

        $this->assertTrue($s->rosterSiteLabelMatchesFilter('BMO 3', 'BMO 3'));
        $this->assertFalse($s->rosterSiteLabelMatchesFilter('BMO 1', 'BMO 3'));

        $this->assertTrue($s->rosterSiteLabelMatchesFilter('GMO', 'GMO'));
        $this->assertFalse($s->rosterSiteLabelMatchesFilter('BMO 1', 'GMO'));

        $this->assertFalse($s->rosterSiteLabelMatchesFilter('GMO', 'PMO'));
    }

    public function test_normalize_filter_site_rejects_unknown(): void
    {
        $s = new PlanningSiteService();

        $this->assertSame('', $s->normalizeFilterSite(''));
        $this->assertSame('', $s->normalizeFilterSite(null));
        $this->assertSame('BMO 1', $s->normalizeFilterSite('BMO 1'));
        $this->assertSame('BMO 3', $s->normalizeFilterSite('BMO 3'));
        $this->assertSame('', $s->normalizeFilterSite('BMO 99'));
    }

    public function test_planning_site_tabs_include_semua_and_ordered_sites(): void
    {
        $s = new PlanningSiteService();
        $tabs = $s->getPlanningSiteTabs();

        $this->assertSame('', $tabs[0]['value']);
        $this->assertSame('Semua', $tabs[0]['label']);
        $this->assertSame(PlanningSiteService::FILTER_SITES[0], $tabs[1]['value']);
    }
}
