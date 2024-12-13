<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // Ensures the application is behind a proxy like Cloudflare/Load Balancers.
        \Illuminate\Http\Middleware\TrustProxies::class,
        
        // Handles requests that require maintenance mode.
        \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
        
        // Validates incoming POST data size.
        \Illuminate\Http\Middleware\ValidatePostSize::class,
        
        // Automatically trims extra whitespace from user inputs.
        \App\Http\Middleware\TrimStrings::class,
        
        // Converts empty string inputs to `null`.
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // Ensures CSRF tokens are verified for web forms.
            \App\Http\Middleware\VerifyCsrfToken::class,
            // Allows easy access to the user object for web routes.
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        ],

        'api' => [
            // Limits the rate of API requests.
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            
            // Ensures incoming JSON requests are properly formatted.
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        // Ensures authenticated users only access protected routes.
        'auth' => \App\Http\Middleware\Authenticate::class,

        // Redirects guests (unauthenticated users) if trying to access specific pages.
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        
        // Used to throttle or restrict login attempts.
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        
        // Allows binding route parameters to models automatically.
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        
        // Ensures users are authenticated via Sanctum tokens.
        'auth:sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ];
}
