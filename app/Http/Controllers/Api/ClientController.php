<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Modules\Clients\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Client::query()->visibleTo($request->user());

        // Search filter
        if ($search = $request->input('filter.search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('trading_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status = $request->input('filter.status')) {
            $query->where('status', $status);
        }

        // Type filter
        if ($type = $request->input('filter.type')) {
            $query->where('type', $type);
        }

        // Sorting
        $sortField = $request->input('sort', '-created_at');
        $sortDirection = 'asc';
        if (str_starts_with($sortField, '-')) {
            $sortDirection = 'desc';
            $sortField = substr($sortField, 1);
        }
        $allowedSorts = ['name', 'created_at', 'updated_at', 'status', 'type'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        // Pagination
        $perPage = min($request->input('per_page', 15), 100);

        return ClientResource::collection($query->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'trading_name' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:50',
            'type' => 'nullable|in:company,individual,government,non_profit',
            'status' => 'nullable|in:prospect,active,inactive',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'county' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'industry' => 'nullable|string|max:100',
            'employee_count' => 'nullable|integer|min:0',
            'annual_revenue' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['assigned_to'] = $request->user()->id;

        $client = Client::create($validated);

        return response()->json([
            'message' => 'Client created successfully.',
            'data' => new ClientResource($client),
        ], 201);
    }

    public function show(Request $request, Client $client): ClientResource
    {
        $this->authorizeAccess($request->user(), $client);

        return new ClientResource($client->load(['contacts', 'communications']));
    }

    public function update(Request $request, Client $client): JsonResponse
    {
        $this->authorizeAccess($request->user(), $client);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'trading_name' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:50',
            'type' => 'nullable|in:company,individual,government,non_profit',
            'status' => 'nullable|in:prospect,active,inactive',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'county' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'industry' => 'nullable|string|max:100',
            'employee_count' => 'nullable|integer|min:0',
            'annual_revenue' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);

        return response()->json([
            'message' => 'Client updated successfully.',
            'data' => new ClientResource($client),
        ]);
    }

    public function destroy(Request $request, Client $client): JsonResponse
    {
        $this->authorizeAccess($request->user(), $client);

        $client->delete();

        return response()->json([
            'message' => 'Client deleted successfully.',
        ]);
    }

    protected function authorizeAccess($user, Client $client): void
    {
        if (!$user->canViewAllRecords() && $client->assigned_to !== $user->id) {
            abort(403, 'You do not have access to this client.');
        }
    }
}
