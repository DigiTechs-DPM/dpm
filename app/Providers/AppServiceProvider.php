<?php

namespace App\Providers;

use App\Services\PayPalGateway;
use App\Services\StripeGateway;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Services\PaymentGatewayFactory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            StripeGateway::class,
            fn() =>
            new StripeGateway(
                config('services.stripe.secret'),
                route('stripe.webhook') // not used in client, but handy
            )
        );

        $this->app->bind(PayPalGateway::class, function () {
            return new PayPalGateway([
                'client_id'   => config('services.paypal.client_id'),
                'secret'      => config('services.paypal.secret'),
                'base'        => config('services.paypal.base_url', 'https://api.paypal.com'),
                'webhook_id'  => config('services.paypal.webhook_id'),
            ]);
        });

        $this->app->singleton(PaymentGatewayFactory::class);
    }
    // app/Providers/AuthServiceProvider.php
    protected $policies = [
        \App\Models\Lead::class => \App\Policies\LeadPolicy::class,
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        // Share the authenticated admin (if available) with all views
        View::composer('*', function ($view) {
            $view->with('authAdmin', Auth::guard('admin')->user());
        });

        // dynamic route prfix changed
        // $url = app('url');
        // $url->macro('route', function ($name, $parameters = [], $absolute = true) use ($url) {
        //     $guardPrefix = auth('seller')->check() ? 'seller' : 'admin';

        //     if (!str_starts_with($name, 'admin.') && !str_starts_with($name, 'seller.')) {
        //         $name = "{$guardPrefix}.{$name}";
        //     }

        //     return $url->to(route($name, $parameters, $absolute));
        // });
    }
}
