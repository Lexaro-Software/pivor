<?php

namespace App\Modules\Core\Livewire;

use App\Modules\Core\Models\User;
use App\Modules\Clients\Models\Client;
use App\Modules\Communications\Models\Communication;
use App\Modules\Contacts\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class DataImport extends Component
{
    use WithFileUploads;

    public int $step = 1;
    public string $importType = '';
    public $csvFile = null;
    public array $csvHeaders = [];
    public array $csvPreview = [];
    public array $fieldMapping = [];
    public array $importResults = [];
    public bool $importing = false;

    protected $listeners = ['resetImport'];

    public function getAvailableFieldsProperty(): array
    {
        return match ($this->importType) {
            'clients' => [
                'name' => ['label' => 'Company Name', 'required' => true],
                'trading_name' => ['label' => 'Trading Name', 'required' => false],
                'type' => ['label' => 'Type (company/individual/organisation)', 'required' => false],
                'status' => ['label' => 'Status (active/inactive/prospect/archived)', 'required' => false],
                'email' => ['label' => 'Email', 'required' => false],
                'phone' => ['label' => 'Phone', 'required' => false],
                'website' => ['label' => 'Website', 'required' => false],
                'address_line_1' => ['label' => 'Address Line 1', 'required' => false],
                'address_line_2' => ['label' => 'Address Line 2', 'required' => false],
                'city' => ['label' => 'City', 'required' => false],
                'county' => ['label' => 'County/State', 'required' => false],
                'postcode' => ['label' => 'Postcode/ZIP', 'required' => false],
                'country' => ['label' => 'Country (2-letter code)', 'required' => false],
                'industry' => ['label' => 'Industry', 'required' => false],
                'employee_count' => ['label' => 'Employee Count', 'required' => false],
                'annual_revenue' => ['label' => 'Annual Revenue', 'required' => false],
                'registration_number' => ['label' => 'Registration Number', 'required' => false],
                'vat_number' => ['label' => 'VAT Number', 'required' => false],
                'notes' => ['label' => 'Notes', 'required' => false],
            ],
            'contacts' => [
                'first_name' => ['label' => 'First Name', 'required' => true],
                'last_name' => ['label' => 'Last Name', 'required' => true],
                'email' => ['label' => 'Email', 'required' => false],
                'phone' => ['label' => 'Phone', 'required' => false],
                'mobile' => ['label' => 'Mobile', 'required' => false],
                'job_title' => ['label' => 'Job Title', 'required' => false],
                'department' => ['label' => 'Department', 'required' => false],
                'client_name' => ['label' => 'Client/Company Name (for matching)', 'required' => false],
                'address_line_1' => ['label' => 'Address Line 1', 'required' => false],
                'address_line_2' => ['label' => 'Address Line 2', 'required' => false],
                'city' => ['label' => 'City', 'required' => false],
                'county' => ['label' => 'County/State', 'required' => false],
                'postcode' => ['label' => 'Postcode/ZIP', 'required' => false],
                'country' => ['label' => 'Country (2-letter code)', 'required' => false],
                'linkedin_url' => ['label' => 'LinkedIn URL', 'required' => false],
                'status' => ['label' => 'Status (active/inactive/archived)', 'required' => false],
                'notes' => ['label' => 'Notes', 'required' => false],
            ],
            'communications' => [
                'type' => ['label' => 'Type (email/phone/meeting/note/task)', 'required' => true],
                'subject' => ['label' => 'Subject', 'required' => true],
                'content' => ['label' => 'Content', 'required' => false],
                'client_name' => ['label' => 'Client Name (for matching)', 'required' => false],
                'contact_name' => ['label' => 'Contact Name (for matching)', 'required' => false],
                'status' => ['label' => 'Status (pending/completed/cancelled)', 'required' => false],
                'due_at' => ['label' => 'Due Date (YYYY-MM-DD)', 'required' => false],
            ],
            default => [],
        };
    }

    public function selectType(string $type): void
    {
        $this->importType = $type;
        $this->step = 2;
    }

    public function updatedCsvFile(): void
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $this->parseCsvFile();
    }

    protected function parseCsvFile(): void
    {
        if (!$this->csvFile) {
            return;
        }

        $path = $this->csvFile->getRealPath();
        $handle = fopen($path, 'r');

        if (!$handle) {
            session()->flash('error', 'Could not read the CSV file.');
            return;
        }

        // Detect delimiter
        $firstLine = fgets($handle);
        rewind($handle);

        $delimiter = ',';
        if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
            $delimiter = ';';
        }

        // Read headers
        $headers = fgetcsv($handle, 0, $delimiter);
        if (!$headers) {
            session()->flash('error', 'Could not read CSV headers.');
            fclose($handle);
            return;
        }

        // Clean headers (remove BOM if present)
        $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
        $this->csvHeaders = array_map('trim', $headers);

        // Read preview rows (first 5)
        $this->csvPreview = [];
        $count = 0;
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false && $count < 5) {
            if (count($row) === count($this->csvHeaders)) {
                $this->csvPreview[] = array_combine($this->csvHeaders, $row);
            }
            $count++;
        }

        fclose($handle);

        // Auto-map fields based on header names
        $this->autoMapFields();

        $this->step = 3;
    }

    protected function autoMapFields(): void
    {
        $this->fieldMapping = [];
        $availableFields = $this->availableFields;

        foreach ($this->csvHeaders as $header) {
            $headerLower = strtolower(trim($header));
            $headerNormalized = str_replace([' ', '-', '_'], '', $headerLower);

            foreach ($availableFields as $field => $config) {
                $fieldNormalized = str_replace('_', '', $field);
                $labelNormalized = str_replace([' ', '-', '_'], '', strtolower($config['label']));

                if ($headerNormalized === $fieldNormalized || $headerNormalized === $labelNormalized || str_contains($labelNormalized, $headerNormalized)) {
                    $this->fieldMapping[$header] = $field;
                    break;
                }
            }

            if (!isset($this->fieldMapping[$header])) {
                $this->fieldMapping[$header] = '';
            }
        }
    }

    public function goToStep(int $step): void
    {
        if ($step < $this->step) {
            $this->step = $step;
        }
    }

    public function proceedToPreview(): void
    {
        // Check required fields are mapped
        $mappedFields = array_filter($this->fieldMapping);
        $requiredFields = array_filter($this->availableFields, fn($f) => $f['required']);

        foreach ($requiredFields as $field => $config) {
            if (!in_array($field, $mappedFields)) {
                session()->flash('error', "Required field '{$config['label']}' is not mapped.");
                return;
            }
        }

        $this->step = 4;
    }

    public function executeImport(): void
    {
        $this->importing = true;
        $this->importResults = [
            'success' => 0,
            'errors' => [],
            'total' => 0,
        ];

        $path = $this->csvFile->getRealPath();
        $handle = fopen($path, 'r');

        if (!$handle) {
            session()->flash('error', 'Could not read the CSV file.');
            $this->importing = false;
            return;
        }

        // Detect delimiter
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';

        // Skip headers
        fgetcsv($handle, 0, $delimiter);

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
                $mappedData = $this->mapRowData($rowData);

                $result = $this->importRow($mappedData, $rowNumber);

                if ($result === true) {
                    $this->importResults['success']++;
                } else {
                    $this->importResults['errors'][] = "Row {$rowNumber}: {$result}";
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->importResults['errors'][] = "Import failed: " . $e->getMessage();
        }

        fclose($handle);
        $this->importing = false;
        $this->step = 5;
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

    protected function importRow(array $data, int $rowNumber): bool|string
    {
        return match ($this->importType) {
            'clients' => $this->importClient($data),
            'contacts' => $this->importContact($data),
            'communications' => $this->importCommunication($data),
            default => 'Unknown import type',
        };
    }

    protected function importClient(array $data): bool|string
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'type' => 'nullable|in:company,individual,organisation',
            'status' => 'nullable|in:active,inactive,prospect,archived',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return implode(', ', $validator->errors()->all());
        }

        $data['type'] = $data['type'] ?? 'company';
        $data['status'] = $data['status'] ?? 'prospect';
        $data['assigned_to'] = auth()->id();

        Client::create($data);

        return true;
    }

    protected function importContact(array $data): bool|string
    {
        $validator = Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'status' => 'nullable|in:active,inactive,archived',
        ]);

        if ($validator->fails()) {
            return implode(', ', $validator->errors()->all());
        }

        // Try to match client by name
        if (!empty($data['client_name'])) {
            $client = Client::where('name', 'like', '%' . $data['client_name'] . '%')
                ->orWhere('trading_name', 'like', '%' . $data['client_name'] . '%')
                ->first();

            if ($client) {
                $data['client_id'] = $client->id;
            }
            unset($data['client_name']);
        }

        $data['status'] = $data['status'] ?? 'active';
        $data['assigned_to'] = auth()->id();

        Contact::create($data);

        return true;
    }

    protected function importCommunication(array $data): bool|string
    {
        $validator = Validator::make($data, [
            'type' => 'required|in:email,phone,meeting,note,task',
            'subject' => 'required|string|max:255',
            'status' => 'nullable|in:pending,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return implode(', ', $validator->errors()->all());
        }

        // Try to match client by name
        if (!empty($data['client_name'])) {
            $client = Client::where('name', 'like', '%' . $data['client_name'] . '%')
                ->orWhere('trading_name', 'like', '%' . $data['client_name'] . '%')
                ->first();

            if ($client) {
                $data['client_id'] = $client->id;
            }
            unset($data['client_name']);
        }

        // Try to match contact by name
        if (!empty($data['contact_name'])) {
            $nameParts = explode(' ', $data['contact_name'], 2);
            $contact = Contact::where('first_name', 'like', '%' . $nameParts[0] . '%');

            if (isset($nameParts[1])) {
                $contact->where('last_name', 'like', '%' . $nameParts[1] . '%');
            }

            $contact = $contact->first();

            if ($contact) {
                $data['contact_id'] = $contact->id;
            }
            unset($data['contact_name']);
        }

        $data['status'] = $data['status'] ?? 'completed';
        $data['created_by'] = auth()->id();

        if (!empty($data['due_at'])) {
            try {
                $data['due_at'] = \Carbon\Carbon::parse($data['due_at']);
            } catch (\Exception $e) {
                unset($data['due_at']);
            }
        }

        Communication::create($data);

        return true;
    }

    public function resetImport(): void
    {
        $this->reset();
        $this->step = 1;
    }

    public function render()
    {
        return view('core::livewire.data-import')->layout('components.layouts.app', ['title' => 'Import Data']);
    }
}
