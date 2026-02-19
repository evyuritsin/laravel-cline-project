<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Meta Description -->
    <meta name="description" content="@yield('description', 'A modern Laravel application')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />

    <!-- Styles -->
    <style>
        /* CSS Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* CSS Variables */
        :root {
            --color-primary: #3b82f6;
            --color-secondary: #10b981;
            --color-dark: #1f2937;
            --color-light: #f9fafb;
            --color-text: #374151;
            --color-border: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius: 0.5rem;
            --transition: all 0.3s ease;
        }

        /* Base Styles */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            line-height: 1.6;
            color: var(--color-text);
            background-color: var(--color-light);
            min-height: 100vh;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 600;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        h1 { font-size: 2.5rem; }
        h2 { font-size: 2rem; }
        h3 { font-size: 1.5rem; }

        p {
            margin-bottom: 1rem;
        }

        /* Links */
        a {
            color: var(--color-primary);
            text-decoration: none;
            transition: var(--transition);
        }

        a:hover {
            color: #2563eb;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: var(--color-primary);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
        }

        .btn:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background-color: var(--color-secondary);
        }

        .btn-secondary:hover {
            background-color: #0da271;
        }

        /* Cards */
        .card {
            background-color: white;
            border-radius: var(--radius);
            padding: 2rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        /* Grid */
        .grid {
            display: grid;
            gap: 2rem;
        }

        @media (min-width: 768px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Navigation */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            background-color: white;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--color-text);
            font-weight: 500;
        }

        .nav-links a:hover {
            color: var(--color-primary);
        }

        /* Hero Section */
        .hero {
            padding: 4rem 0;
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin-bottom: 3rem;
        }

        /* Footer */
        footer {
            background-color: var(--color-dark);
            color: white;
            padding: 3rem 0;
            margin-top: 4rem;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 2rem;
        }

        /* Utility Classes */
        .text-center { text-align: center; }
        .mt-1 { margin-top: 0.5rem; }
        .mt-2 { margin-top: 1rem; }
        .mt-3 { margin-top: 1.5rem; }
        .mt-4 { margin-top: 2rem; }
        .mb-1 { margin-bottom: 0.5rem; }
        .mb-2 { margin-bottom: 1rem; }
        .mb-3 { margin-bottom: 1.5rem; }
        .mb-4 { margin-bottom: 2rem; }
        .p-4 { padding: 2rem; }
        .rounded { border-radius: var(--radius); }
        .shadow { box-shadow: var(--shadow); }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-links {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .footer-content {
                flex-direction: column;
            }
            
            h1 { font-size: 2rem; }
            h2 { font-size: 1.75rem; }
        }
    </style>

    <!-- Scripts -->
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="{{ url('/') }}" class="text-xl font-bold">
                    {{ config('app.name', 'Laravel') }}
                </a>
                
                <ul class="nav-links">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><a href="{{ url('/about') }}">About</a></li>
                    <li><a href="{{ url('/contact') }}">Contact</a></li>
                    <!-- Authentication links removed since this is an API project -->
                    <!-- To add authentication, install Laravel Breeze or Jetstream -->
                </ul>
            </nav>
        </div>
    </header>

    <!-- Page Content -->
    <main>
        @yield('content')
        
        <!-- Default Content if no yield -->
        @hasSection('content')
        @else
        <section class="hero">
            <div class="container">
                <h1>Welcome to {{ config('app.name', 'Laravel') }}</h1>
                <p class="mt-2">A modern Laravel application with HTML5 boilerplate</p>
                <div class="mt-4">
                    <a href="{{ url('/about') }}" class="btn">Learn More</a>
                    <a href="{{ url('/contact') }}" class="btn btn-secondary ml-2">Get Started</a>
                </div>
            </div>
        </section>

        <div class="container mt-8">
            <h2 class="text-center">Features</h2>
            <div class="grid mt-4">
                <div class="card">
                    <h3>Modern Stack</h3>
                    <p>Built with Laravel, Tailwind CSS, and modern JavaScript frameworks.</p>
                </div>
                
                <div class="card">
                    <h3>Responsive Design</h3>
                    <p>Fully responsive layout that works on all devices and screen sizes.</p>
                </div>
                
                <div class="card">
                    <h3>Clean Code</h3>
                    <p>Well-structured, maintainable code following best practices.</p>
                </div>
            </div>
        </div>
        @endif
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div>
                    <h3>{{ config('app.name', 'Laravel') }}</h3>
                    <p class="mt-1">A modern web application framework.</p>
                </div>
                
                <div>
                    <h3>Quick Links</h3>
                    <ul class="nav-links">
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li><a href="{{ url('/about') }}">About</a></li>
                        <li><a href="{{ url('/contact') }}">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3>Resources</h3>
                    <ul class="nav-links">
                        <li><a href="https://laravel.com/docs" target="_blank">Documentation</a></li>
                        <li><a href="https://laracasts.com" target="_blank">Laracasts</a></li>
                        <li><a href="https://laravel-news.com" target="_blank">Laravel News</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="text-center mt-8 pt-4 border-t border-gray-700">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // CSRF token for AJAX requests
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
        
        // Mobile menu toggle (placeholder)
        console.log('Laravel boilerplate loaded');
    </script>
    
    @stack('scripts')
</body>
</html>