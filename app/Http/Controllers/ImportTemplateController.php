<?php

namespace App\Http\Controllers;

use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use League\Csv\Bom;
use League\Csv\Writer;
use SplTempFileObject;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportTemplateController extends Controller
{
    /**
     * Whitelist of allowed importer classes.
     * Only these importers can be downloaded as templates.
     *
     * @var array<string, class-string<Importer>>
     */
    protected static array $allowedImporters = [
        'dosen' => \App\Filament\Imports\DosenImporter::class,
        'mahasiswa' => \App\Filament\Imports\MahasiswaImporter::class,
    ];

    /**
     * Generate and download a CSV template for the given importer.
     */
    public function download(string $importer): StreamedResponse
    {
        if (! array_key_exists($importer, static::$allowedImporters)) {
            abort(404, 'Template not found.');
        }

        $importerClass = static::$allowedImporters[$importer];
        $columns = $importerClass::getColumns();

        $csv = Writer::createFromFileObject(new SplTempFileObject);

        // Header row
        $csv->insertOne(array_map(
            fn (ImportColumn $column): string => $column->getExampleHeader(),
            $columns,
        ));

        // Example data rows
        $columnExamples = array_map(
            fn (ImportColumn $column): array => $column->getExamples(),
            $columns,
        );

        $exampleRowsCount = array_reduce(
            $columnExamples,
            fn (int $count, array $exampleData): int => max($count, count($exampleData)),
            initial: 0,
        );

        $exampleRows = [];
        foreach ($columnExamples as $exampleData) {
            for ($i = 0; $i < $exampleRowsCount; $i++) {
                $exampleRows[$i][] = $exampleData[$i] ?? '';
            }
        }

        $csv->insertAll($exampleRows);

        return response()->streamDownload(function () use ($csv): void {
            $csv->setOutputBOM(Bom::Utf8);
            echo $csv->toString();
        }, "template-import-{$importer}.csv", [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
