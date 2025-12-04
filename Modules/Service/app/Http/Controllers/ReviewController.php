<?php

namespace Modules\Service\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Modules\Service\Models\Review;
use Modules\Service\Http\Requests\StoreReviewRequest;
use Modules\Service\Http\Requests\UpdateReviewRequest;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use ApiResponse;

    /**
     * Display reviews for a specific reviewable (service or venue).
     * Public access.
     */
    public function index(Request $request)
    {
        $request->validate([
            'reviewable_type' => 'required|in:service,venue',
            'reviewable_id' => 'required|integer',
        ]);

        $reviewableType = $request->reviewable_type === 'service' 
            ? 'Modules\Service\Models\Service' 
            : 'Modules\Service\Models\Venue';

        $reviews = Review::with(['user'])
            ->where('reviewable_type', $reviewableType)
            ->where('reviewable_id', $request->reviewable_id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $this->successResponse($reviews);
    }

    /**
     * Store a newly created review.
     * Customer only.
     */
    public function store(StoreReviewRequest $request)
    {
        $validated = $request->validated();

        $reviewableType = $validated['reviewable_type'] === 'service' 
            ? 'Modules\Service\Models\Service' 
            : 'Modules\Service\Models\Venue';

        $validated['reviewable_type'] = $reviewableType;
        $validated['user_id'] = $request->user()->id;

        $review = Review::create($validated);

        return $this->createdResponse($review, 'Review created successfully');
    }

    /**
     * Update the specified review.
     * Customer only (own reviews).
     */
    public function update(UpdateReviewRequest $request, $id)
    {
        $review = Review::find($id);

        if (!$review) {
            return $this->notFoundResponse('Review not found');
        }

        // Ensure the user owns this review
        if ($review->user_id !== $request->user()->id) {
            return $this->forbiddenResponse('You are not authorized to update this review');
        }

        $review->update($request->validated());

        return $this->successResponse($review, 'Review updated successfully');
    }

    /**
     * Remove the specified review.
     * Customer (own reviews) or Admin.
     */
    public function destroy(Request $request, $id)
    {
        $review = Review::find($id);

        if (!$review) {
            return $this->notFoundResponse('Review not found');
        }

        // Check if user owns the review or is admin
        $user = $request->user();
        if ($review->user_id !== $user->id && !$user->hasRole('admin')) {
            return $this->forbiddenResponse('You are not authorized to delete this review');
        }

        $review->delete();

        return $this->successResponse(null, 'Review deleted successfully');
    }
}
