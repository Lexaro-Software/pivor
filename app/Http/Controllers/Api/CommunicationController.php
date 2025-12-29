<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommunicationResource;
use App\Modules\Communications\Models\Communication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommunicationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Communication::query()->visibleTo($request->user());

        // Search filter
        if ($search = $request->input('filter.search')) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Type filter
        if ($type = $request->input('filter.type')) {
            $query->where('type', $type);
        }

        // Status filter
        if ($status = $request->input('filter.status')) {
            $query->where('status', $status);
        }

        // Client filter
        if ($clientId = $request->input('filter.client_id')) {
            $query->where('client_id', $clientId);
        }

        // Contact filter
        if ($contactId = $request->input('filter.contact_id')) {
            $query->where('contact_id', $contactId);
        }

        // Priority filter
        if ($priority = $request->input('filter.priority')) {
            $query->where('priority', $priority);
        }

        // Direction filter
        if ($direction = $request->input('filter.direction')) {
            $query->where('direction', $direction);
        }

        // Sorting
        $sortField = $request->input('sort', '-created_at');
        $sortDirection = 'asc';
        if (str_starts_with($sortField, '-')) {
            $sortDirection = 'desc';
            $sortField = substr($sortField, 1);
        }
        $allowedSorts = ['subject', 'type', 'created_at', 'updated_at', 'due_at', 'priority', 'status'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        // Pagination
        $perPage = min($request->input('per_page', 15), 100);

        return CommunicationResource::collection($query->with(['client', 'contact'])->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:email,phone,meeting,note,task',
            'direction' => 'nullable|in:inbound,outbound,internal',
            'subject' => 'required|string|max:255',
            'content' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'due_at' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validated['created_by'] = $request->user()->id;
        $validated['assigned_to'] = $validated['assigned_to'] ?? $request->user()->id;

        $communication = Communication::create($validated);

        return response()->json([
            'message' => 'Communication created successfully.',
            'data' => new CommunicationResource($communication),
        ], 201);
    }

    public function show(Request $request, Communication $communication): CommunicationResource
    {
        $this->authorizeAccess($request->user(), $communication);

        return new CommunicationResource($communication->load(['client', 'contact', 'createdBy', 'assignedUser']));
    }

    public function update(Request $request, Communication $communication): JsonResponse
    {
        $this->authorizeAccess($request->user(), $communication);

        $validated = $request->validate([
            'type' => 'sometimes|required|in:email,phone,meeting,note,task',
            'direction' => 'nullable|in:inbound,outbound,internal',
            'subject' => 'sometimes|required|string|max:255',
            'content' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'due_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $communication->update($validated);

        return response()->json([
            'message' => 'Communication updated successfully.',
            'data' => new CommunicationResource($communication),
        ]);
    }

    public function destroy(Request $request, Communication $communication): JsonResponse
    {
        $this->authorizeAccess($request->user(), $communication);

        $communication->delete();

        return response()->json([
            'message' => 'Communication deleted successfully.',
        ]);
    }

    protected function authorizeAccess($user, Communication $communication): void
    {
        if (!$user->canViewAllRecords() &&
            $communication->created_by !== $user->id &&
            $communication->assigned_to !== $user->id) {
            abort(403, 'You do not have access to this communication.');
        }
    }
}
