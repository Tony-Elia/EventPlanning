<?php

namespace Modules\Service\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Modules\Service\Models\ServicePackage;
use Modules\Service\Models\PackageItem;
use Modules\Service\Http\Requests\StorePackageRequest;
use Modules\Service\Http\Requests\UpdatePackageRequest;
use Modules\Service\Http\Requests\PackageItemRequest;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of packages.
     * Public access.
     */
    public function index(Request $request)
    {
        $query = ServicePackage::query()->with(['provider', 'items.service'])->withCount('items');

        // Optionally filter by provider
        if ($request->has('provider_id')) {
            $query->where('provider_id', $request->provider_id);
        }

        $packages = $query->paginate(15);

        return $this->successResponse($packages);
    }

    /**
     * Display the specified package with items.
     * Public access.
     */
    public function show($id)
    {
        $package = ServicePackage::with(['provider', 'items.service'])
            ->withCount('items')
            ->find($id);

        if (!$package) {
            return $this->notFoundResponse('Package not found');
        }

        return $this->successResponse($package);
    }

    public function myPackages(Request $request)
    {
        return $this->successResponse(ServicePackage::with(['provider', 'items.service'])
            ->withCount('items')
            ->where('provider_id', $request->user()->id)
            ->paginate(15), 'Packages fetched successfully');
    }

    /**
     * Store a newly created package.
     * Provider only.
     */
    public function store(StorePackageRequest $request)
    {
        $validated = $request->validated();
        $validated['provider_id'] = $request->user()->id;
        info('create package request: ', $validated['items']);
        $package = ServicePackage::create($validated);
        $package->items()->createMany($validated['items']);

        return $this->createdResponse($package->load('items.service'), 'Package created successfully');
    }

    /**
     * Update the specified package.
     * Provider only (own packages).
     */
    public function update(UpdatePackageRequest $request, $id)
    {
        $package = ServicePackage::with('items')->withCount('items')->find($id);

        if (!$package) {
            return $this->notFoundResponse('Package not found');
        }

        // Ensure the user owns this package
        if ($package->provider_id !== $request->user()->id) {
            return $this->forbiddenResponse('You are not authorized to update this package');
        }

        $package->update($request->validated());

        return $this->successResponse($package, 'Package updated successfully');
    }

    public function updatePackageItem(PackageItemRequest $request, $packageId, $itemId)
    {
        $package_item = PackageItem::with('package')->find($itemId);

        if(!$package_item || $package_item->package_id != $packageId)
            return $this->notFoundResponse('Package Item Not Found');
        if($package_item->package->provider_id != $request->user()->id)
            return $this->forbiddenResponse('You are not authorized to update this package');

        $package_item->update($request->validated());
        return $this->successResponse($package_item, 'Package item updated successfully');
    }

    /**
     * Remove the specified package.
     * Provider only (own packages).
     */
    public function destroy(Request $request, $id)
    {
        $package = ServicePackage::find($id);

        if (!$package) {
            return $this->notFoundResponse('Package not found');
        }

        // Ensure the user owns this package
        if ($package->provider_id !== $request->user()->id) {
            return $this->forbiddenResponse('You are not authorized to delete this package');
        }

        $package->delete();

        return $this->successResponse(null, 'Package deleted successfully');
    }

    /**
     * Add an item to a package.
     * Provider only (own packages).
     */
    public function addItem(PackageItemRequest $request, $id)
    {
        $package = ServicePackage::find($id);

        if (!$package) {
            return $this->notFoundResponse('Package not found');
        }

        // Ensure the user owns this package
        if ($package->provider_id !== $request->user()->id) {
            return $this->forbiddenResponse('You are not authorized to modify this package');
        }

        $validated = $request->validated();
        $validated['package_id'] = $id;

        $item = PackageItem::create($validated);

        return $this->createdResponse($item, 'Item added to package successfully');
    }

    /**
     * Remove an item from a package.
     * Provider only (own packages).
     */
    public function removeItem(Request $request, $packageId, $itemId)
    {
        $package = ServicePackage::find($packageId);

        if (!$package) {
            return $this->notFoundResponse('Package not found');
        }

        // Ensure the user owns this package
        if ($package->provider_id !== $request->user()->id) {
            return $this->forbiddenResponse('You are not authorized to modify this package');
        }

        $item = PackageItem::where('package_id', $packageId)
            ->where('id', $itemId)
            ->first();

        if (!$item) {
            return $this->notFoundResponse('Package item not found');
        }

        $item->delete();

        return $this->successResponse(null, 'Item removed from package successfully');
    }
}
