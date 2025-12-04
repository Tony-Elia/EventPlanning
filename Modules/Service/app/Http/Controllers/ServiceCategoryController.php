<?php

namespace Modules\Service\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Modules\Service\Models\ServiceCategory;
use Modules\Service\Http\Requests\StoreServiceCategoryRequest;
use Modules\Service\Http\Requests\UpdateServiceCategoryRequest;
use Illuminate\Support\Str;

class ServiceCategoryController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of service categories.
     */
    public function index()
    {
        $categories = ServiceCategory::query();
        if(request('services', false)) {
            $categories->with('services');
        }
        $categories = $categories->get();
        return $this->successResponse($categories);
    }

    /**
     * Display the specified service category.
     */
    public function show($id)
    {
        $category = ServiceCategory::with('services')->find($id);
        if (!$category) {
            return $this->notFoundResponse('Service category not found');
        }
        
        return $this->successResponse($category);
    }

    /**
     * Store a newly created service category.
     * Admin only.
     */
    public function store(StoreServiceCategoryRequest $request)
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
    public function update(UpdateServiceCategoryRequest $request, $id)
    {
        $category = ServiceCategory::find($id);

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
