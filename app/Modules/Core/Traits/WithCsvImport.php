<?php

namespace App\Modules\Core\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\WithFileUploads;

trait WithCsvImport
{
    use WithFileUploads;

    public bool $showImportModal = false;
    public int $importStep = 1;
    public $csvFile = null;
    public array $csvHeaders = [];
    public array $csvPreview = [];
    public array $fieldMapping = [];
    public array $importResults = [];

    abstract protected function getImportFields(): array;
    abstract protected function getImportRules(): array;
    abstract protected function createFromImport(array $data): void;

    public function openImportModal(): void
    {
        $this->resetImport();
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->resetImport();
    }

    public function resetImport(): void
    {
        $this->importStep = 1;
        $this->csvFile = null;
        $this->csvHeaders = [];
        $this->csvPreview = [];
        $this->fieldMapping = [];
        $this->importResults = [];
    }

    public function updatedCsvFile(): void
    {
        $this->validate(['csvFile' => 'required|file|mimes:csv,txt|max:10240']);
        $this->parseCsvFile();
    }

    protected function parseCsvFile(): void
    {
        if (!$this->csvFile) return;

        $path = $this->csvFile->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) return;

        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';

        $headers = fgetcsv($handle, 0, $delimiter);
        if (!$headers) {
            fclose($handle);
            return;
        }

        $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
        $this->csvHeaders = array_map('trim', $headers);

        $this->csvPreview = [];
        $count = 0;
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false && $count < 5) {
            if (count($row) === count($this->csvHeaders)) {
                $this->csvPreview[] = array_combine($this->csvHeaders, $row);
            }
            $count++;
        }
        fclose($handle);

        $this->autoMapFields();
        $this->importStep = 2;
    }

    protected function autoMapFields(): void
    {
        $this->fieldMapping = [];
        $fields = $this->getImportFields();

        foreach ($this->csvHeaders as $header) {
            $headerNorm = str_replace([' ', '-', '_'], '', strtolower($header));

            foreach ($fields as $field => $label) {
                $fieldNorm = str_replace('_', '', strtolower($field));
                $labelNorm = str_replace([' ', '-', '_'], '', strtolower($label));

                if ($headerNorm === $fieldNorm || $headerNorm === $labelNorm) {
                    $this->fieldMapping[$header] = $field;
                    break;
                }
            }

            if (!isset($this->fieldMapping[$header])) {
                $this->fieldMapping[$header] = '';
            }
        }
    }

    public function executeImport(): void
    {
        $this->importResults = ['success' => 0, 'errors' => [], 'total' => 0];

        $path = $this->csvFile->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) return;

        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';

        fgetcsv($handle, 0, $delimiter); // Skip headers
        $rowNumber = 1;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNumber++;
                $this->importResults['total']++;

                if (count($row) !== count($this->csvHeaders)) {
                    $this->importResults['errors'][] = "Row {$rowNumber}: Column count mismatch";
                    continue;
                }

                $rowData = array_combine($this->csvHeaders, $row);
                $mapped = $this->mapRowData($rowData);

                $validator = Validator::make($mapped, $this->getImportRules());
                if ($validator->fails()) {
                    $this->importResults['errors'][] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                try {
                    $this->createFromImport($mapped);
                    $this->importResults['success']++;
                } catch (\Exception $e) {
                    $this->importResults['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->importResults['errors'][] = "Import failed: " . $e->getMessage();
        }

        fclose($handle);
        $this->importStep = 3;
    }

    protected function mapRowData(array $rowData): array
    {
        $mapped = [];
        foreach ($this->fieldMapping as $csvHeader => $field) {
            if (!empty($field) && isset($rowData[$csvHeader])) {
                $mapped[$field] = trim($rowData[$csvHeader]);
            }
        }
        return $mapped;
    }
}
