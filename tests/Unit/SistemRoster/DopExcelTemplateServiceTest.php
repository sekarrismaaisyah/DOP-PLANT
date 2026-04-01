<?php

namespace Tests\Unit\SistemRoster;

use App\Services\SistemRoster\DopExcelTemplateService;
use PHPUnit\Framework\TestCase;

class DopExcelTemplateServiceTest extends TestCase
{
    public function test_validate_import_headers_accepts_exact_template_row(): void
    {
        $s = new DopExcelTemplateService();
        $row = DopExcelTemplateService::EXPECTED_HEADERS;

        $this->assertSame([], $s->validateImportHeaders($row));
    }

    public function test_validate_import_headers_rejects_wrong_label(): void
    {
        $s = new DopExcelTemplateService();
        $row = DopExcelTemplateService::EXPECTED_HEADERS;
        $row[0] = 'Tanggal Salah';

        $errors = $s->validateImportHeaders($row);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Kolom 1', $errors[0]);
    }

    public function test_validate_import_headers_rejects_extra_non_empty_column(): void
    {
        $s = new DopExcelTemplateService();
        $row = DopExcelTemplateService::EXPECTED_HEADERS;
        $row[] = 'Extra';

        $errors = $s->validateImportHeaders($row);
        $this->assertNotEmpty($errors);
    }
}
