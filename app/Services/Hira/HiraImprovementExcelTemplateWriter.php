<?php

declare(strict_types=1);

namespace App\Services\Hira;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

/**
 * Menulis template Excel dengan dropdown yang kompatibel Microsoft Excel.
 *
 * PhpSpreadsheet menulis operator="between" pada TYPE_LIST sehingga Excel
 * sering mengabaikan dropdown. File di-patch setelah disimpan.
 */
final class HiraImprovementExcelTemplateWriter
{
    public static function download(Spreadsheet $spreadsheet, string $filename): BinaryFileResponse
    {
        $xlsxPath = self::createTempPath();

        try {
            self::save($spreadsheet, $xlsxPath);
        } catch (\Throwable $exception) {
            @unlink($xlsxPath);

            throw $exception;
        }

        return response()->download($xlsxPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend();
    }

    public static function save(Spreadsheet $spreadsheet, string $path): void
    {
        (new Xlsx($spreadsheet))->save($path);
        self::patchListValidationXml($path);
    }

    private static function createTempPath(): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'hira_tpl_');
        if ($tempPath === false) {
            throw new \RuntimeException('Gagal menyiapkan file template sementara.');
        }

        $xlsxPath = $tempPath.'.xlsx';
        if (! rename($tempPath, $xlsxPath)) {
            @unlink($tempPath);

            throw new \RuntimeException('Gagal menyiapkan file template sementara.');
        }

        return $xlsxPath;
    }

    private static function patchListValidationXml(string $xlsxPath): void
    {
        $zip = new ZipArchive;
        if ($zip->open($xlsxPath) !== true) {
            return;
        }

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = $zip->getNameIndex($index);
            if ($name === false || ! preg_match('#^xl/worksheets/sheet\d+\.xml$#', $name)) {
                continue;
            }

            $xml = $zip->getFromIndex($index);
            if ($xml === false || ! str_contains($xml, 'dataValidations')) {
                continue;
            }

            $patched = preg_replace_callback(
                '/<dataValidation[^>]*type="list"[^>]*>/',
                static function (array $matches): string {
                    $tag = $matches[0];
                    $tag = (string) preg_replace('/\s+operator="[^"]*"/', '', $tag);
                    $tag = (string) preg_replace('/showDropDown="1"/', 'showDropDown="0"', $tag);

                    return $tag;
                },
                $xml,
            );

            if (is_string($patched)) {
                $patched = (string) preg_replace(
                    '/<formula1>&quot;([^<]*)<\/formula1>/',
                    '<formula1>"$1"</formula1>',
                    $patched,
                );
                $zip->addFromString($name, $patched);
            }
        }

        $zip->close();
    }
}
