<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Mp4Controller - API Controller for MP4 file management in /public/apps directory
 */
class Mp4Controller extends Controller
{
    /**
     * Base directory for MP4 files
     */
    protected string $baseDirectory = '/var/www/html/public/apps';

    /**
     * Get list of all MP4 files in the apps directory
     */
    public function index(): JsonResponse
    {
        try {
            $files = [];
            
            if (is_dir($this->baseDirectory)) {
                $allFiles = scandir($this->baseDirectory);
                
                foreach ($allFiles as $file) {
                    if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'mp4') {
                        $filePath = $this->baseDirectory . '/' . $file;
                        $files[] = [
                            'name' => $file,
                            'size' => filesize($filePath),
                            'modified' => date('Y-m-d H:i:s', filemtime($filePath)),
                            'url' => url('/apps/' . $file),
                            'api_url' => route('api.mp4.show', ['filename' => $file]),
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $files,
                'count' => count($files),
                'directory' => $this->baseDirectory,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to list MP4 files',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get information about a specific MP4 file
     */
    public function show(string $filename): JsonResponse
    {
        try {
            $filePath = $this->baseDirectory . '/' . $filename;
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'error' => 'File not found',
                    'filename' => $filename,
                ], 404);
            }

            if (pathinfo($filename, PATHINFO_EXTENSION) !== 'mp4') {
                return response()->json([
                    'success' => false,
                    'error' => 'File is not an MP4 file',
                    'filename' => $filename,
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $filename,
                    'size' => filesize($filePath),
                    'modified' => date('Y-m-d H:i:s', filemtime($filePath)),
                    'mime_type' => mime_content_type($filePath),
                    'url' => url('/apps/' . $filename),
                    'direct_url' => route('api.mp4.stream', ['filename' => $filename]),
                    'download_url' => route('api.mp4.download', ['filename' => $filename]),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to get file information',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Stream an MP4 file
     */
    public function stream(string $filename)
    {
        try {
            $filePath = $this->baseDirectory . '/' . $filename;
            
            if (!file_exists($filePath)) {
                return response('File not found', 404);
            }

            if (pathinfo($filename, PATHINFO_EXTENSION) !== 'mp4') {
                return response('File is not an MP4 file', 400);
            }

            $fileSize = filesize($filePath);
            $mimeType = mime_content_type($filePath);

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Length' => $fileSize,
                'Accept-Ranges' => 'bytes',
            ]);
        } catch (\Exception $e) {
            return response('Failed to stream file: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Download an MP4 file
     */
    public function download(string $filename)
    {
        try {
            $filePath = $this->baseDirectory . '/' . $filename;
            
            if (!file_exists($filePath)) {
                return response('File not found', 404);
            }

            if (pathinfo($filename, PATHINFO_EXTENSION) !== 'mp4') {
                return response('File is not an MP4 file', 400);
            }

            return response()->download($filePath, $filename, [
                'Content-Type' => 'video/mp4',
            ]);
        } catch (\Exception $e) {
            return response('Failed to download file: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Upload a new MP4 file
     */
    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:mp4|max:102400', // Max 100MB
            'filename' => 'sometimes|string|max:255|regex:/^[a-zA-Z0-9_\-\.]+$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $file = $request->file('file');
            
            // Generate filename if not provided
            $filename = $request->input('filename');
            if (!$filename) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $filename = Str::slug($originalName) . '_' . time() . '.mp4';
            } else {
                // Ensure filename has .mp4 extension
                if (pathinfo($filename, PATHINFO_EXTENSION) !== 'mp4') {
                    $filename .= '.mp4';
                }
            }

            $filePath = $this->baseDirectory . '/' . $filename;
            
            // Move uploaded file
            $file->move($this->baseDirectory, $filename);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => [
                    'filename' => $filename,
                    'size' => filesize($filePath),
                    'url' => url('/apps/' . $filename),
                    'api_url' => route('api.mp4.show', ['filename' => $filename]),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to upload file',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete an MP4 file
     */
    public function destroy(string $filename): JsonResponse
    {
        try {
            $filePath = $this->baseDirectory . '/' . $filename;
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'error' => 'File not found',
                    'filename' => $filename,
                ], 404);
            }

            if (pathinfo($filename, PATHINFO_EXTENSION) !== 'mp4') {
                return response()->json([
                    'success' => false,
                    'error' => 'File is not an MP4 file',
                    'filename' => $filename,
                ], 400);
            }

            if (!unlink($filePath)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to delete file',
                    'filename' => $filename,
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully',
                'filename' => $filename,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete file',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get MP4 file statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $totalSize = 0;
            $fileCount = 0;
            $files = [];
            
            if (is_dir($this->baseDirectory)) {
                $allFiles = scandir($this->baseDirectory);
                
                foreach ($allFiles as $file) {
                    if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'mp4') {
                        $filePath = $this->baseDirectory . '/' . $file;
                        $fileSize = filesize($filePath);
                        $totalSize += $fileSize;
                        $fileCount++;
                        $files[] = [
                            'name' => $file,
                            'size' => $fileSize,
                            'size_human' => $this->formatBytes($fileSize),
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_files' => $fileCount,
                    'total_size' => $totalSize,
                    'total_size_human' => $this->formatBytes($totalSize),
                    'directory' => $this->baseDirectory,
                    'files' => $files,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to get statistics',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}