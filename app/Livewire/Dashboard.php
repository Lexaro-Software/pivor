<?php

namespace App\Livewire;

use App\Modules\Clients\Models\Client;
use App\Modules\Communications\Models\Communication;
use App\Modules\Contacts\Models\Contact;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'total_clients' => Client::count(),
            'active_clients' => Client::where('status', 'active')->count(),
            'prospects' => Client::where('status', 'prospect')->count(),
            'total_contacts' => Contact::count(),
            'pending_tasks' => Communication::where('type', 'task')->where('status', 'pending')->count(),
            'overdue_tasks' => Communication::where('type', 'task')
                ->where('due_at', '<', now())
                ->whereNull('completed_at')
                ->count(),
        ];

        $recentClients = Client::latest()->take(5)->get();
        $recentCommunications = Communication::with(['client', 'contact'])
            ->latest()
            ->take(5)
            ->get();
        $upcomingTasks = Communication::with(['client', 'contact'])
            ->where('type', 'task')
            ->where('status', 'pending')
            ->whereNotNull('due_at')
            ->orderBy('due_at')
            ->take(5)
            ->get();

        return view('livewire.dashboard', [
            'stats' => $stats,
            'recentClients' => $recentClients,
            'recentCommunications' => $recentCommunications,
            'upcomingTasks' => $upcomingTasks,
        ])->layout('components.layouts.app', ['title' => 'Dashboard']);
    }
}
