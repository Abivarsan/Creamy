<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Item\StoreItemRequest;
use App\Http\Requests\Item\UpdateItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ItemController extends Controller
{
    /**
     * Display a listing of inventory items.
     * Accessible by: Owner, Manager, Cashier.
     * Supports search query (?search=...) and category filter (?category=...).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Item::class);

        $perPage = (int) $request->query('per_page', 15);
        $search = $request->query('search');
        $category = $request->query('category');

        $items = Item::query()
            ->search($search)
            ->category($category)
            ->latest('id')
            ->paginate($perPage);

        return ItemResource::collection($items);
    }

    /**
     * Store a newly created inventory item.
     * Accessible by: Owner, Manager.
     * Forbidden for: Cashier.
     */
    public function store(StoreItemRequest $request): JsonResponse
    {
        Gate::authorize('create', Item::class);

        $item = Item::create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Inventory item created successfully.',
            'data' => new ItemResource($item),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified inventory item.
     * Accessible by: Owner, Manager, Cashier.
     */
    public function show(Item $item): JsonResponse
    {
        Gate::authorize('view', $item);

        return response()->json([
            'status' => 'success',
            'data' => new ItemResource($item),
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified inventory item.
     * Accessible by: Owner, Manager, Cashier (e.g., updating stock).
     */
    public function update(UpdateItemRequest $request, Item $item): JsonResponse
    {
        Gate::authorize('update', $item);

        $item->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Inventory item updated successfully.',
            'data' => new ItemResource($item->fresh()),
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified inventory item.
     * Accessible by: Owner only.
     * Forbidden for: Manager, Cashier.
     */
    public function destroy(Item $item): JsonResponse
    {
        Gate::authorize('delete', $item);

        $item->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Inventory item deleted successfully.',
        ], Response::HTTP_OK);
    }
}
