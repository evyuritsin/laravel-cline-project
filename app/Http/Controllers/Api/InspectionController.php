<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $inspections = $request->user()
            ->inspections()
            ->with('rooms.photos')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($inspections);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'address' => 'required|string|max:500',
            'type' => 'required|in:move_in,move_out',
            'inspection_date' => 'required|date',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $user = $request->user();
        
        if ($user->isFree()) {
            $monthInspections = $user->inspections()
                ->whereMonth('created_at', now()->month)
                ->count();
            
            if ($monthInspections >= 1) {
                return response()->json([
                    'message' => 'Free tier limit reached. Upgrade to Starter for 5 inspections/month.',
                ], 403);
            }
        }

        $inspection = $user->inspections()->create([
            'address' => $request->address,
            'type' => $request->type,
            'inspection_date' => $request->inspection_date,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => 'draft',
        ]);

        return response()->json($inspection, 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $inspection = $request->user()
            ->inspections()
            ->with('rooms.photos')
            ->findOrFail($id);

        return response()->json($inspection);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $inspection = $request->user()
            ->inspections()
            ->findOrFail($id);

        $request->validate([
            'address' => 'sometimes|string|max:500',
            'type' => 'sometimes|in:move_in,move_out',
            'inspection_date' => 'sometimes|date',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'sometimes|in:draft,completed,sent',
            'notes' => 'nullable|string',
        ]);

        $inspection->update($request->only([
            'address', 'type', 'inspection_date', 
            'latitude', 'longitude', 'status', 'notes'
        ]));

        return response()->json($inspection);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $inspection = $request->user()
            ->inspections()
            ->findOrFail($id);

        $inspection->delete();

        return response()->json([
            'message' => 'Inspection deleted successfully',
        ]);
    }
}