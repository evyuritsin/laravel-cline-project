# HTML Templates for Laravel Project

This Laravel project now includes comprehensive HTML5 boilerplate templates for web development.

## Available Templates

### 1. Standalone HTML Template (`/template`)
**File:** `public/template.html`
**URL:** http://localhost:8000/template

A complete, self-contained HTML5 boilerplate with:
- Modern HTML5 structure with semantic elements
- Responsive design with CSS Grid and Flexbox
- CSS custom properties (variables) for easy theming
- Font Awesome icons and Google Fonts integration
- SEO-friendly meta tags and Open Graph markup
- Mobile-friendly navigation
- Smooth scrolling JavaScript
- Clean, organized CSS with utility classes

### 2. Laravel Blade Template (`/boilerplate`)
**File:** `resources/views/boilerplate.blade.php`
**URL:** http://localhost:8000/boilerplate

A Laravel-specific Blade template with:
- Laravel CSRF token protection
- Blade template inheritance support (`@yield`, `@section`, `@stack`)
- Laravel helpers (`{{ url() }}`, `{{ config() }}`, `{{ csrf_token() }}`)
- Responsive design with CSS variables
- Modern layout with navigation, hero section, and footer
- Integration-ready for Laravel authentication (commented out)

### 3. HTML Demo Page (`/html-demo`)
**URL:** http://localhost:8000/html-demo

A simple demo page that showcases the available templates and their features.

## Template Features

### Common Features (Both Templates)
- ✅ Modern HTML5 structure
- ✅ Fully responsive design
- ✅ CSS custom properties (variables)
- ✅ Mobile-first approach
- ✅ Clean, semantic markup
- ✅ SEO-friendly meta tags
- ✅ Accessible navigation
- ✅ Cross-browser compatible

### Standalone Template Specific
- ✅ Self-contained (no external dependencies needed)
- ✅ Font Awesome 6.4.0 icons
- ✅ Google Fonts (Inter)
- ✅ Complete with hero section, features, about, contact
- ✅ Smooth scrolling JavaScript
- ✅ Download link for the HTML file

### Blade Template Specific
- ✅ Laravel CSRF protection
- ✅ Blade template inheritance
- ✅ Laravel configuration integration
- ✅ Ready for Laravel authentication system
- ✅ Dynamic year in copyright
- ✅ Stackable styles/scripts sections

## Usage Instructions

### Using the Standalone Template
1. Access directly at `/template` URL
2. Or open `public/template.html` in browser
3. Customize colors by modifying CSS variables in `:root` selector
4. Replace content in the HTML structure
5. Add your own CSS/JS as needed

### Using the Blade Template
1. Access at `/boilerplate` URL
2. Use as a layout template:
   ```blade
   @extends('boilerplate')
   
   @section('title', 'My Page Title')
   
   @section('content')
       <h1>My Custom Content</h1>
       <p>This content will be placed in the main area.</p>
   @endsection
   ```
3. Customize by editing `resources/views/boilerplate.blade.php`
4. Add authentication by installing Laravel Breeze/Jetstream

### Creating New Pages
1. **For simple HTML:** Copy `public/template.html` and modify
2. **For Laravel views:** Create new Blade files in `resources/views/`
3. **Add routes** in `routes/web.php`:
   ```php
   Route::get('/new-page', function () {
       return view('new-page'); // resources/views/new-page.blade.php
   });
   ```

## Customization

### Changing Colors
Modify the CSS variables in the `<style>` section:

```css
:root {
    --primary-color: #3b82f6;     /* Blue */
    --secondary-color: #10b981;   /* Green */
    --dark-color: #1f2937;        /* Dark gray */
    --light-color: #f9fafb;       /* Light gray */
    --text-color: #374151;        /* Text color */
}
```

### Adding Pages
1. Duplicate the template structure
2. Update navigation links
3. Add new content sections
4. Create corresponding routes

### Adding JavaScript
For the standalone template, add scripts before `</body>`:
```html
<script>
    // Your custom JavaScript
</script>
```

For the Blade template, use `@stack('scripts')`:
```blade
@push('scripts')
    <script>
        // Your custom JavaScript
    </script>
@endpush
```

## File Structure

```
/var/www/html/
├── public/
│   ├── template.html          # Standalone HTML template
│   └── index.php             # Laravel entry point
├── resources/views/
│   └── boilerplate.blade.php # Laravel Blade template
├── routes/web.php            # Web routes (updated)
└── HTML_TEMPLATES_README.md  # This file
```

## Development Server

The Laravel development server is running at:
- **URL:** http://localhost:8000
- **API Endpoint:** `/` (returns JSON)
- **HTML Templates:** `/template`, `/boilerplate`, `/html-demo`

To restart the server:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## Next Steps

1. **Customize templates** for your specific project needs
2. **Add Laravel authentication** if needed (install Breeze/Jetstream)
3. **Separate CSS/JS** into external files for production
4. **Add database integration** for dynamic content
5. **Implement Laravel controllers** for more complex pages

## Notes

- The standalone template is great for quick prototypes or static pages
- The Blade template is ideal for Laravel applications with dynamic content
- Both templates are fully responsive and mobile-friendly
- All code is well-commented for easy customization