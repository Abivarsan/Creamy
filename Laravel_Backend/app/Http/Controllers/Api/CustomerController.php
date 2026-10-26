<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     * Accessible by: Owner, Manager, Cashier.
     * Supports search query (?search=...) matching name, email, or phone.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Customer::class);

        $perPage = (int) $request->query('per_page', 15);
        $search = $request->query('search');

        $customers = Customer::query()
            ->search($search)
            ->latest('id')
            ->paginate($perPage);

        return CustomerResource::collection($customers);
    }

    /**
     * Store a newly created customer.
     * Accessible by: Owner, Manager, Cashier.
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        Gate::authorize('create', Customer::class);

        $customer = Customer::create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Customer created successfully.',
            'data' => new CustomerResource($customer),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified customer.
     * Accessible by: Owner, Manager, Cashier.
     */
    public function show(Customer $customer): JsonResponse
    {
        Gate::authorize('view', $customer);

        return response()->json([
            'status' => 'success',
            'data' => new CustomerResource($customer),
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified customer.
     * Accessible by: Owner, Manager, Cashier.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        Gate::authorize('update', $customer);

        $customer->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Customer updated successfully.',
            'data' => new CustomerResource($customer->fresh()),
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified customer.
     * Accessible by: Owner only.
     * Forbidden for: Manager, Cashier.
     */
    public function destroy(Customer $customer): JsonResponse
    {
        Gate::authorize('delete', $customer);

        $customer->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Customer deleted successfully.',
        ], Response::HTTP_OK);
    }
}
