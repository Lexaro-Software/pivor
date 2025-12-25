<?php

namespace App\Modules\Clients\Livewire;

use App\Modules\Clients\Models\Client;
use Livewire\Component;

class ClientShow extends Component
{
    public Client $client;

    public function mount(Client $client): void
    {
        $this->client = $client->load(['contacts', 'communications', 'assignedUser']);
    }

    public function render()
    {
        return view('clients::livewire.client-show')
            ->layout('components.layouts.app', ['title' => $this->client->display_name]);
    }
}
