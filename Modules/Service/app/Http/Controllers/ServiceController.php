<?php

namespace Modules\Service\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Modules\Service\Http\Requests\ServiceRequest;
use Modules\Service\Http\Resources\ServiceResource;
use Modules\Service\Models\Service;
use Modules\Service\Http\Requests\UpdateServiceRequest;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of services with optional filters.
     * Public access.
     */
    public function index(Request $request)
    {
        $query = Service::with(['provider', 'category'])
            ->withCount('reviews')
            ->where('is_active', true);

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by location
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }

        $services = $query->paginate(15);

        return $this->successResponse(ServiceResource::collection($services));
    }

    /**
     * Display the specified service.
     * Public access.
     */
    public function show($id)
    {
        $service = Service::with(['provider', 'category', 'reviews.user'])
            ->withCount('reviews')
            ->find($id);

        if (!$service) {
            return $this->notFoundResponse('Service not found');
        }

        return $this->successResponse($service);
    }

    /**
     * Store a newly created service.
     * Provider only.
     */
    public function store(ServiceRequest $request)
    {
        $validated = $request->validated();
        $validated['provider_id'] = $request->user()->id;

        $service = Service::create($validated);

        return $this->createdResponse($service, 'Service created successfully');
    }

    /**
     * Update the specified service.
     * Provider only (own services).
     */
    public function update(ServiceRequest $request, $id)
    {
        $service = Service::withCount('reviews')->find($id);

        if (!$service) {
            return $this->notFoundResponse('Service not found');
        }

        // Ensure the user owns this service
        if ($service->provider_id !== $request->user()->id) {
            return $this->forbiddenResponse('You are not authorized to update this service');
        }

        $service->update($request->validated());

        return $this->successResponse($service, 'Service updated successfully');
    }

    /**
     * Remove the specified service.
     * Provider only (own services).
     */
    public function destroy(Request $request, $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return $this->notFoundResponse('Service not found');
        }

        // Ensure the user owns this service
        if ($service->provider_id !== $request->user()->id) {
            return $this->forbiddenResponse('You are not authorized to delete this service');
        }

        $service->delete();

        return $this->successResponse(null, 'Service deleted successfully');
    }

    /**
     * Get provider's own services.
     * Provider only.
     */
    public function myServices(Request $request)
    {
        $services = Service::with(['category'])
            ->withCount('reviews')
            ->where('provider_id', $request->user()->id)
            ->paginate(15);

        return $this->successResponse(ServiceResource::collection($services));
    }
}
