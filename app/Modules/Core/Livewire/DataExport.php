<?php

namespace App\Modules\Core\Livewire;

use App\Modules\Clients\Models\Client;
use App\Modules\Communications\Models\Communication;
use App\Modules\Contacts\Models\Contact;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataExport extends Component
{
    public function exportClients(): StreamedResponse
    {
        $user = auth()->user();
        $clients = Client::visibleTo($user)->with('assignedUser')->get();

        $headers = ['ID', 'Name', 'Trading Name', 'Type', 'Status', 'Email', 'Phone', 'Website', 'Address Line 1', 'Address Line 2', 'City', 'County', 'Postcode', 'Country', 'Industry', 'Employee Count', 'Annual Revenue', 'Registration Number', 'VAT Number', 'Assigned To', 'Notes', 'Created At'];

        return $this->streamCsv('clients.csv', $headers, $clients, function ($client) {
            return [
                $client->id,
                $client->name,
                $client->trading_name,
                $client->type,
                $client->status,
                $client->email,
                $client->phone,
                $client->website,
                $client->address_line_1,
                $client->address_line_2,
                $client->city,
                $client->county,
                $client->postcode,
                $client->country,
                $client->industry,
                $client->employee_count,
                $client->annual_revenue,
                $client->registration_number,
                $client->vat_number,
                $client->assignedUser?->name,
                $client->notes,
                $client->created_at?->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function exportContacts(): StreamedResponse
    {
        $user = auth()->user();
        $contacts = Contact::visibleTo($user)->with(['client', 'assignedUser'])->get();

        $headers = ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Mobile', 'Job Title', 'Department', 'Client', 'Is Primary Contact', 'Address Line 1', 'Address Line 2', 'City', 'County', 'Postcode', 'Country', 'LinkedIn URL', 'Status', 'Assigned To', 'Notes', 'Created At'];

        return $this->streamCsv('contacts.csv', $headers, $contacts, function ($contact) {
            return [
                $contact->id,
                $contact->first_name,
                $contact->last_name,
                $contact->email,
                $contact->phone,
                $contact->mobile,
                $contact->job_title,
                $contact->department,
                $contact->client?->name,
                $contact->is_primary_contact ? 'Yes' : 'No',
                $contact->address_line_1,
                $contact->address_line_2,
                $contact->city,
                $contact->county,
                $contact->postcode,
                $contact->country,
                $contact->linkedin_url,
                $contact->status,
                $contact->assignedUser?->name,
                $contact->notes,
                $contact->created_at?->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function exportCommunications(): StreamedResponse
    {
        $user = auth()->user();
        $communications = Communication::visibleTo($user)->with(['client', 'contact', 'createdBy'])->get();

        $headers = ['ID', 'Type', 'Subject', 'Content', 'Client', 'Contact', 'Status', 'Due At', 'Completed At', 'Created By', 'Created At'];

        return $this->streamCsv('communications.csv', $headers, $communications, function ($comm) {
            return [
                $comm->id,
                $comm->type,
                $comm->subject,
                $comm->content,
                $comm->client?->name,
                $comm->contact ? $comm->contact->first_name . ' ' . $comm->contact->last_name : '',
                $comm->status,
                $comm->due_at?->format('Y-m-d H:i:s'),
                $comm->completed_at?->format('Y-m-d H:i:s'),
                $comm->createdBy?->name,
                $comm->created_at?->format('Y-m-d H:i:s'),
            ];
        });
    }

    protected function streamCsv(string $filename, array $headers, $data, callable $rowMapper): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $data, $rowMapper) {
            $handle = fopen('php://output', 'w');

            // Add BOM for Excel UTF-8 compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write headers
            fputcsv($handle, $headers);

            // Write data
            foreach ($data as $item) {
                fputcsv($handle, $rowMapper($item));
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function render()
    {
        $user = auth()->user();

        $counts = [
            'clients' => Client::visibleTo($user)->count(),
            'contacts' => Contact::visibleTo($user)->count(),
            'communications' => Communication::visibleTo($user)->count(),
        ];

        return view('core::livewire.data-export', [
            'counts' => $counts,
        ])->layout('components.layouts.app', ['title' => 'Export Data']);
    }
}
