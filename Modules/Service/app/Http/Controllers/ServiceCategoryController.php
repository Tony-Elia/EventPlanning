<?php

namespace Modules\Service\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Modules\Service\Http\Resources\ServiceCategoryResource;
use Modules\Service\Models\ServiceCategory;
use Modules\Service\Http\Requests\ServiceCategoryRequest;
use Illuminate\Support\Str;

class ServiceCategoryController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of service categories.
     */
    public function index()
    {
        $categories = ServiceCategory::query()->withCount('services');
        if(request('services', false)) {
            $categories->with('services');
        }
        $categories = $categories->paginate(15);
        return $this->successResponse(ServiceCategoryResource::collection($categories));
    }

    /**
     * Display the specified service category.
     */
    public function show($id)
    {
        $category = ServiceCategory::with('services')->withCount('services')->find($id);
        if (!$category) {
            return $this->notFoundResponse('Service category not found');
        }

        return $this->successResponse($category);
    }

    /**
     * Store a newly created service category.
     * Admin only.
     */
    public function store(ServiceCategoryRequest $request)
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name']);

        $category = ServiceCategory::create($validated);

        return $this->createdResponse($category, 'Service category created successfully');
    }

    /**
     * Update the specified service category.
     * Admin only.
     */
    public function update(ServiceCategoryRequest $request, $id)
    {
        $category = ServiceCategory::withCount('services')->find($id);

        if (!$category) {
            return $this->notFoundResponse('Service category not found');
        }

        $validated = $request->validated();

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return $this->successResponse($category, 'Service category updated successfully');
    }

    /**
     * Remove the specified service category.
     * Admin only.
     */
    public function destroy($id)
    {
        $category = ServiceCategory::find($id);

        if (!$category) {
            return $this->notFoundResponse('Service category not found');
        }

        $category->delete();

        return $this->successResponse(null, 'Service category deleted successfully');
    }
}
