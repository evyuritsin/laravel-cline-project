<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function store(Request $request, int $inspectionId): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'elements' => 'nullable|array',
        ]);

        $inspection = $request->user()
            ->inspections()
            ->findOrFail($inspectionId);

        $room = $inspection->rooms()->create([
            'name' => $request->name,
            'notes' => $request->notes,
            'sort_order' => $request->sort_order ?? 0,
            'elements' => $request->elements,
        ]);

        return response()->json($room, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $room = Room::whereHas('inspection', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'notes' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'elements' => 'nullable|array',
        ]);

        $room->update($request->only(['name', 'notes', 'sort_order', 'elements']));

        return response()->json($room);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $room = Room::whereHas('inspection', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($id);

        $room->delete();

        return response()->json([
            'message' => 'Room deleted successfully',
        ]);
    }
}