<?php

namespace App\Modules\Clients\Livewire;

use App\Models\User;
use App\Modules\Clients\Models\Client;
use Livewire\Component;

class ClientForm extends Component
{
    public ?Client $client = null;

    public string $name = '';
    public string $trading_name = '';
    public string $registration_number = '';
    public string $vat_number = '';
    public string $type = 'company';
    public string $status = 'prospect';
    public string $email = '';
    public string $phone = '';
    public string $website = '';
    public string $address_line_1 = '';
    public string $address_line_2 = '';
    public string $city = '';
    public string $county = '';
    public string $postcode = '';
    public string $country = '';
    public string $industry = '';
    public ?int $employee_count = null;
    public ?float $annual_revenue = null;
    public ?int $assigned_to = null;
    public string $notes = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'trading_name' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:50',
            'type' => 'required|in:company,individual,organisation',
            'status' => 'required|in:active,inactive,prospect,archived',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'county' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:2',
            'industry' => 'nullable|string|max:100',
            'employee_count' => 'nullable|integer|min:0',
            'annual_revenue' => 'nullable|numeric|min:0',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:5000',
        ];
    }

    public function mount(?Client $client = null): void
    {
        if ($client && $client->exists) {
            $this->client = $client;
            $this->fill($client->toArray());
        }
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->client) {
            $this->client->update($validated);
            session()->flash('message', 'Client updated successfully.');
        } else {
            $client = Client::create($validated);
            session()->flash('message', 'Client created successfully.');
        }

        $this->redirect(route('clients.index'), navigate: true);
    }

    public function render()
    {
        $users = User::orderBy('name')->get();

        return view('clients::livewire.client-form', [
            'users' => $users,
            'isEditing' => (bool) $this->client,
        ])->layout('components.layouts.app', [
            'title' => $this->client ? 'Edit Client' : 'Create Client',
        ]);
    }
}
