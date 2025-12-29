<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Modules\Contacts\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContactController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Contact::query()->visibleTo($request->user());

        // Search filter
        if ($search = $request->input('filter.search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Client filter
        if ($clientId = $request->input('filter.client_id')) {
            $query->where('client_id', $clientId);
        }

        // Status filter
        if ($status = $request->input('filter.status')) {
            $query->where('status', $status);
        }

        // Primary contact filter
        if ($request->has('filter.is_primary')) {
            $query->where('is_primary_contact', $request->boolean('filter.is_primary'));
        }

        // Sorting
        $sortField = $request->input('sort', '-created_at');
        $sortDirection = 'asc';
        if (str_starts_with($sortField, '-')) {
            $sortDirection = 'desc';
            $sortField = substr($sortField, 1);
        }
        $allowedSorts = ['first_name', 'last_name', 'created_at', 'updated_at', 'status'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        // Pagination
        $perPage = min($request->input('per_page', 15), 100);

        return ContactResource::collection($query->with('client')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'client_id' => 'nullable|exists:clients,id',
            'is_primary_contact' => 'nullable|boolean',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'county' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'linkedin_url' => 'nullable|url|max:255',
            'status' => 'nullable|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $validated['assigned_to'] = $request->user()->id;

        $contact = Contact::create($validated);

        return response()->json([
            'message' => 'Contact created successfully.',
            'data' => new ContactResource($contact),
        ], 201);
    }

    public function show(Request $request, Contact $contact): ContactResource
    {
        $this->authorizeAccess($request->user(), $contact);

        return new ContactResource($contact->load(['client', 'communications']));
    }

    public function update(Request $request, Contact $contact): JsonResponse
    {
        $this->authorizeAccess($request->user(), $contact);

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'client_id' => 'nullable|exists:clients,id',
            'is_primary_contact' => 'nullable|boolean',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'county' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'linkedin_url' => 'nullable|url|max:255',
            'status' => 'nullable|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $contact->update($validated);

        return response()->json([
            'message' => 'Contact updated successfully.',
            'data' => new ContactResource($contact),
        ]);
    }

    public function destroy(Request $request, Contact $contact): JsonResponse
    {
        $this->authorizeAccess($request->user(), $contact);

        $contact->delete();

        return response()->json([
            'message' => 'Contact deleted successfully.',
        ]);
    }

    protected function authorizeAccess($user, Contact $contact): void
    {
        if (!$user->canViewAllRecords() && $contact->assigned_to !== $user->id) {
            abort(403, 'You do not have access to this contact.');
        }
    }
}
