<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\Room;
use App\Services\YandexStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PhotoController extends Controller
{
    public function __construct(
        private YandexStorageService $storageService
    ) {}

    public function store(Request $request, int $roomId): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|max:10240',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'description' => 'nullable|string',
            'taken_at' => 'nullable|date',
        ]);

        $room = Room::whereHas('inspection', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($roomId);

        $file = $request->file('photo');
        $path = $this->storageService->uploadPhoto(
            file: $file,
            inspectionId: $room->inspection_id
        );

        $photo = $room->photos()->create([
            'filename' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'taken_at' => $request->taken_at ?? now(),
            'description' => $request->description,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        return response()->json($photo, 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $photo = Photo::whereHas('room.inspection', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->findOrFail($id);

        $this->storageService->deletePhoto($photo->storage_path);
        $photo->delete();

        return response()->json([
            'message' => 'Photo deleted successfully',
        ]);
    }
}