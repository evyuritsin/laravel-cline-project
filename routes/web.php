<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'Laravel Cline API',
        'version' => '1.0.0',
        'documentation' => '/api/health',
    ]);
});

// HTML Template Routes
Route::get('/template', function () {
    // Serve the standalone HTML template file
    return response()->file(public_path('template.html'));
});

Route::get('/boilerplate', function () {
    // Render the Blade template
    return view('boilerplate');
});

Route::get('/html-demo', function () {
    // Simple HTML demo page
    return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <title>HTML Demo</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 2rem; }
        .demo-box { background: #f0f0f0; padding: 2rem; border-radius: 8px; margin: 2rem 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #3b82f6; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>HTML Template Demo</h1>
    <p>This Laravel project now includes HTML templates:</p>
    
    <div class="demo-box">
        <h2>Available Templates:</h2>
        <ul>
            <li><a href="/template" class="btn">Standalone HTML Template</a> - Complete HTML5 boilerplate with CSS/JS</li>
            <li><a href="/boilerplate" class="btn">Laravel Blade Template</a> - Blade template with Laravel integration</li>
        </ul>
    </div>
    
    <div class="demo-box">
        <h2>Template Features:</h2>
        <ul>
            <li>✅ Modern HTML5 structure</li>
            <li>✅ Responsive design</li>
            <li>✅ CSS custom properties (variables)</li>
            <li>✅ SEO-friendly meta tags</li>
            <li>✅ Laravel CSRF protection (Blade version)</li>
            <li>✅ Mobile-friendly navigation</li>
            <li>✅ Clean, semantic markup</li>
        </ul>
    </div>
</body>
</html>
HTML;
});
