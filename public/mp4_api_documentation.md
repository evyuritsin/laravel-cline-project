# MP4 File API Documentation

This API provides access to MP4 files in the `/public/apps` directory of the Laravel application.

## Base URL
```
http://localhost:8000/api/mp4
```

## Authentication
No authentication is required for this API.

## Endpoints

### 1. List all MP4 files
**GET** `/api/mp4/files`

Returns a list of all MP4 files in the `/public/apps` directory.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "name": "demo_video.mp4",
      "size": 24,
      "modified": "2026-02-24 20:11:34",
      "url": "http://localhost:8000/apps/demo_video.mp4",
      "api_url": "http://localhost:8000/api/mp4/file/demo_video.mp4"
    }
  ],
  "count": 1,
  "directory": "/var/www/html/public/apps"
}
```

### 2. Get file statistics
**GET** `/api/mp4/stats`

Returns statistics about MP4 files including total count and size.

**Response:**
```json
{
  "success": true,
  "data": {
    "total_files": 2,
    "total_size": 60,
    "total_size_human": "60 B",
    "directory": "/var/www/html/public/apps",
    "files": [
      {
        "name": "demo_video.mp4",
        "size": 24,
        "size_human": "24 B"
      }
    ]
  }
}
```

### 3. Get file information
**GET** `/api/mp4/file/{filename}`

Returns detailed information about a specific MP4 file.

**Parameters:**
- `filename` (path parameter): Name of the MP4 file

**Response:**
```json
{
  "success": true,
  "data": {
    "name": "test_video.mp4",
    "size": 36,
    "modified": "2026-02-24 20:11:29",
    "mime_type": "text/plain",
    "url": "http://localhost:8000/apps/test_video.mp4",
    "direct_url": "http://localhost:8000/api/mp4/stream/test_video.mp4",
    "download_url": "http://localhost:8000/api/mp4/download/test_video.mp4"
  }
}
```

### 4. Stream a file
**GET** `/api/mp4/stream/{filename}`

Streams an MP4 file with proper HTTP headers for video playback.

**Parameters:**
- `filename` (path parameter): Name of the MP4 file

**Headers:**
- `Content-Type`: video/mp4 (or actual mime type)
- `Content-Length`: File size in bytes
- `Accept-Ranges`: bytes
- `Cache-Control`: public

### 5. Download a file
**GET** `/api/mp4/download/{filename}`

Downloads an MP4 file with attachment headers.

**Parameters:**
- `filename` (path parameter): Name of the MP4 file

**Headers:**
- `Content-Type`: video/mp4
- `Content-Disposition`: attachment; filename={filename}
- `Content-Length`: File size in bytes

### 6. Upload a file
**POST** `/api/mp4/upload`

Uploads a new MP4 file to the `/public/apps` directory.

**Parameters (multipart/form-data):**
- `file` (required): The MP4 file to upload (max 100MB)
- `filename` (optional): Custom filename (alphanumeric, underscores, hyphens, dots)

**Response:**
```json
{
  "success": true,
  "message": "File uploaded successfully",
  "data": {
    "filename": "test_video_1740432089.mp4",
    "size": 36,
    "url": "http://localhost:8000/apps/test_video_1740432089.mp4",
    "api_url": "http://localhost:8000/api/mp4/file/test_video_1740432089.mp4"
  }
}
```

### 7. Delete a file
**DELETE** `/api/mp4/file/{filename}`

Deletes an MP4 file from the `/public/apps` directory.

**Parameters:**
- `filename` (path parameter): Name of the MP4 file to delete

**Response:**
```json
{
  "success": true,
  "message": "File deleted successfully",
  "filename": "test_video.mp4"
}
```

## Error Responses

All endpoints return standard error responses:

### 400 Bad Request
```json
{
  "success": false,
  "error": "File is not an MP4 file",
  "filename": "test.txt"
}
```

### 404 Not Found
```json
{
  "success": false,
  "error": "File not found",
  "filename": "nonexistent.mp4"
}
```

### 422 Validation Error
```json
{
  "success": false,
  "error": "Validation failed",
  "errors": {
    "file": ["The file field must be a file of type: mp4."]
  }
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "error": "Failed to list MP4 files",
  "message": "Error message details"
}
```

## Examples

### List all files
```bash
curl http://localhost:8000/api/mp4/files
```

### Get file info
```bash
curl http://localhost:8000/api/mp4/file/test_video.mp4
```

### Stream a file
```bash
curl http://localhost:8000/api/mp4/stream/test_video.mp4
```

### Download a file
```bash
curl -O http://localhost:8000/api/mp4/download/test_video.mp4
```

### Upload a file
```bash
curl -X POST -F "file=@/path/to/video.mp4" http://localhost:8000/api/mp4/upload
```

### Delete a file
```bash
curl -X DELETE http://localhost:8000/api/mp4/file/test_video.mp4
```

## Notes

1. The API only works with `.mp4` files
2. Maximum upload size is 100MB
3. Files are stored in `/var/www/html/public/apps/`
4. Files can also be accessed directly via `http://localhost:8000/apps/{filename}`
5. The API automatically generates filenames if not provided during upload
6. All file operations are logged and include proper error handling