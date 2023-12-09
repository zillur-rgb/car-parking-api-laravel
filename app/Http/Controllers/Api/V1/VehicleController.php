<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Controller for managing vehicles.
 */
class VehicleController extends Controller
{
    /**
     * Display a listing of the vehicles.
     */
    public function index()
    {
        // Retrieve and return a collection of all vehicles as a JSON resource.
        return VehicleResource::collection(Vehicle::all());
    }

    /**
     * Store a newly created vehicle in storage.
     */
    public function store(StoreVehicleRequest $request)
    {
        // Create a new vehicle with validated data and return it as a JSON resource.
        $vehicle = Vehicle::create($request->validated());
        return VehicleResource::make($vehicle);
    }

    /**
     * Display the specified vehicle.
     */
    public function show(Vehicle $vehicle)
    {
        // Return the specified vehicle as a JSON resource.
        return VehicleResource::make($vehicle);
    }

    /**
     * Update the specified vehicle in storage.
     */
    public function update(StoreVehicleRequest $request, Vehicle $vehicle)
    {
        // Update the vehicle with validated data and return it as a JSON resource.
        $vehicle->update($request->validated());

        return response()->json(VehicleResource::make($vehicle), Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified vehicle from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        // Delete the specified vehicle from storage.
        $vehicle->delete();

        // Return a response indicating success with no content.
        return response()->noContent();
    }
}
