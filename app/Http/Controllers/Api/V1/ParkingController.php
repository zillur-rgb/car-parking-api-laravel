<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ParkingResource;
use App\Models\Parking;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ParkingController extends Controller
{
    /**
     * Start a new parking session for a vehicle.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function start(Request $request)
    {
        // Validate incoming request data
        $parkingData = $request->validate([
            'vehicle_id' => [
                'required',
                'integer',
                'exists:vehicles,id,deleted_at,NULL,user_id,' . auth()->id()
            ],
            'zone_id' => ['required', 'integer', 'exists:zones,id']
        ]);

        // Check if there is already an active parking session for the same vehicle
        if (Parking::active()->where('vehicle_id', $request->vehicle_id)->exists()) {
            return response()->json(
                [
                    'errors' => ['general' => ['Can\'t start parking twice using the same vehicle. Please stop the currently active parking.']]
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // Create a new parking session
        $parking = Parking::create($parkingData);
        $parking->load('vehicle', 'zone');

        // Return the transformed parking session as a JSON response
        return ParkingResource::make($parking);
    }
}
