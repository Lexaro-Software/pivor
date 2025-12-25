<?php

namespace App\Livewire;

use App\Modules\Clients\Models\Client;
use App\Modules\Communications\Models\Communication;
use App\Modules\Contacts\Models\Contact;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        $stats = [
            'total_clients' => Client::visibleTo($user)->count(),
            'active_clients' => Client::visibleTo($user)->where('status', 'active')->count(),
            'prospects' => Client::visibleTo($user)->where('status', 'prospect')->count(),
            'total_contacts' => Contact::visibleTo($user)->count(),
            'pending_tasks' => Communication::visibleTo($user)->where('type', 'task')->where('status', 'pending')->count(),
            'overdue_tasks' => Communication::visibleTo($user)->where('type', 'task')
                ->where('due_at', '<', now())
                ->whereNull('completed_at')
                ->count(),
            'new_clients_month' => Client::visibleTo($user)->where('created_at', '>=', $startOfMonth)->count(),
            'communications_week' => Communication::visibleTo($user)->where('created_at', '>=', $startOfWeek)->count(),
            'tasks_completed_week' => Communication::visibleTo($user)
                ->where('type', 'task')
                ->where('completed_at', '>=', $startOfWeek)
                ->count(),
        ];

        $recentClients = Client::visibleTo($user)->latest()->take(5)->get();
        $recentCommunications = Communication::visibleTo($user)
            ->with(['client', 'contact'])
            ->latest()
            ->take(5)
            ->get();
        $upcomingTasks = Communication::visibleTo($user)
            ->with(['client', 'contact'])
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
            'userName' => $user->name,
        ])->layout('components.layouts.app', ['title' => 'Dashboard']);
    }
}
