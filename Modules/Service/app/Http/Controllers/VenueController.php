<?php

namespace Modules\Service\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Modules\Service\Models\Venue;
use Modules\Service\Http\Requests\StoreVenueRequest;
use Modules\Service\Http\Requests\UpdateVenueRequest;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of venues with optional filters.
     * Public access.
     */

    public function index(Request $request)
    {
        $query = Venue::with(['provider'])
            ->where('is_active', true);

        // Filter by location
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter by capacity
        if ($request->has('min_capacity')) {
            $query->where('capacity', '>=', $request->min_capacity);
        }
        if ($request->has('max_capacity')) {
            $query->where('capacity', '<=', $request->max_capacity);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }

        $venues = $query->paginate(15);

        return $this->successResponse($venues);
    }

    /**
     * Display the specified venue.
     * Public access.
     */
    public function show($id)
    {
        $venue = Venue::with(['provider', 'reviews.user'])
            ->find($id);

        if (!$venue) {
            return $this->notFoundResponse('Venue not found');
        }

        return $this->successResponse($venue);
    }

    /**
     * Store a newly created venue.
     * Provider only.
     */
    public function store(StoreVenueRequest $request)
    {
        $validated = $request->validated();
        $validated['provider_id'] = $request->user()->id;

        $venue = Venue::create($validated);

        return $this->createdResponse($venue, 'Venue created successfully');
    }

    /**
     * Update the specified venue.
     * Provider only (own venues).
     */
    public function update(UpdateVenueRequest $request, $id)
    {
        $venue = Venue::find($id);

        if (!$venue) {
            return $this->notFoundResponse('Venue not found');
        }

        // Ensure the user owns this venue
        if ($venue->provider_id !== $request->user()->id) {
            return $this->forbiddenResponse('You are not authorized to update this venue');
        }

        $venue->update($request->validated());

        return $this->successResponse($venue, 'Venue updated successfully');
    }

    /**
     * Remove the specified venue.
     * Provider only (own venues).
     */
    public function destroy(Request $request, $id)
    {
        $venue = Venue::find($id);

        if (!$venue) {
            return $this->notFoundResponse('Venue not found');
        }

        // Ensure the user owns this venue
        if ($venue->provider_id !== $request->user()->id) {
            return $this->forbiddenResponse('You are not authorized to delete this venue');
        }

        $venue->delete();

        return $this->successResponse(null, 'Venue deleted successfully');
    }

    /**
     * Get provider's own venues.
     * Provider only.
     */
    public function myVenues(Request $request)
    {
        $venues = Venue::where('provider_id', $request->user()->id)
            ->paginate(15);

        return $this->successResponse($venues);
    }
}
