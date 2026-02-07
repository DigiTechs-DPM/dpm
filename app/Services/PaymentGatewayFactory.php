<?php

// app/Services/Payments/PaymentGatewayFactory.php
namespace App\Services;

use App\Models\Brand;
use App\Models\AccountKey;
use App\Services\PayPalGateway;
use App\Services\StripeGateway;
use App\Services\PaymentGateway;
use Illuminate\Support\Facades\Log;

class PaymentGatewayFactory
{
    public function forProviderWithBrand(string $provider, Brand $brand): PaymentGateway
    {
        // 1. Try brand-specific keys
        $keys = AccountKey::where('brand_id', $brand->id)
            ->where('status', 'active')
            ->first();

        // 2. Fallback to super account if missing or incomplete
        if (!$keys || !$this->hasProviderKeys($keys, $provider)) {
            Log::warning("Using SUPER account keys for brand {$brand->id}");

            $keys = AccountKey::where('is_super_account', true)
                ->where('status', 'active')
                ->first();

            if (!$keys || !$this->hasProviderKeys($keys, $provider, true)) {
                throw new \Exception("No valid $provider keys found for brand or super account.");
            }
        }

        return $this->buildGateway($provider, $keys);
    }

    public function forProvider(string $provider): PaymentGateway
    {
        // Uses .env fallback if no DB key passed
        return match ($provider) {
            'stripe' => new StripeGateway(config('services.stripe.secret')),
            'paypal' => new PayPalGateway([
                'client_id'  => config('services.paypal.client_id'),
                'secret'     => config('services.paypal.secret'),
                'base'       => config('services.paypal.base_url', 'https://api.paypal.com'),
                'webhook_id' => config('services.paypal.webhook_id'),
            ]),
            default => throw new \Exception("Unsupported provider [$provider]"),
        };
    }

    protected function buildGateway(string $provider, AccountKey $keys): PaymentGateway
    {
        return match ($provider) {
            'stripe' => new StripeGateway($keys->stripe_secret_key ?? $keys->super_stripe_secret_key),
            'paypal' => new PayPalGateway([
                'client_id'  => $keys->paypal_client_id ?? NULL,
                'secret'     => $keys->paypal_secret ?? NULL,
                'base'       => $keys->paypal_base_url ?? 'https://api.paypal.com',
                'webhook_id' => $keys->paypal_webhook_id ?? null,
            ]),
            default => throw new \Exception("Unsupported provider [$provider]"),
        };
    }

    protected function hasProviderKeys(AccountKey $keys, string $provider, bool $isSuper = false): bool
    {
        return match ($provider) {
            'stripe' => $isSuper
                ? !empty($keys->super_stripe_secret_key)
                : !empty($keys->stripe_secret_key),
            'paypal' => $isSuper
                ? !empty($keys->super_paypal_client_id) && !empty($keys->super_paypal_secret)
                : !empty($keys->paypal_client_id) && !empty($keys->paypal_secret),
            default => false,
        };
    }
}


// class PaymentGatewayFactory
// {
//     public function __construct() {}

//     public function forProviderWithBrand(string $provider, Brand $brand): PaymentGateway
//     {
//         // Try brand-specific keys
//         $keys = AccountKey::where('brand_id', $brand->id)
//             ->where('status', 'active')
//             ->first();

//         // Fallback to global/super account keys
//         if (!$keys || !$this->hasProviderKeys($keys, $provider)) {
//             Log::warning("Using SUPER account keys for brand {$brand->id}");

//             $keys = AccountKey::where('is_super_account', true)
//                 ->where('status', 'active')
//                 ->first();

//             if (!$keys || !$this->hasProviderKeys($keys, $provider)) {
//                 throw new \Exception("No valid $provider keys found for brand or fallback account.");
//             }
//         }

//         return $this->buildGateway($provider, $keys);

//         // if (!$keys || !$this->hasProviderKeys($keys, $provider)) {
//         //     $envSecret = config("services.{$provider}.secret");
//         //     if (!$envSecret) {
//         //         throw new \Exception("No valid $provider keys found.");
//         //     }

//         //     Log::warning("Falling back to ENV keys for $provider.");
//         //     return $this->forProvider($provider);
//         // }
//     }

//     public function forProvider(string $provider): PaymentGateway
//     {
//         // Uses .env fallback if no DB key passed
//         return match ($provider) {
//             'stripe' => new StripeGateway(config('services.stripe.secret')),
//             'paypal' => new PayPalGateway([
//                 'client_id'  => config('services.paypal.client_id'),
//                 'secret'     => config('services.paypal.secret'),
//                 'base'       => config('services.paypal.base_url', 'https://api.paypal.com'),
//                 'webhook_id' => config('services.paypal.webhook_id'),
//             ]),
//             default => throw new \Exception("Unsupported provider [$provider]"),
//         };
//     }

//     protected function buildGateway(string $provider, AccountKey $keys): PaymentGateway
//     {
//         return match ($provider) {
//             'stripe' => new StripeGateway($keys->stripe_secret_key),
//             'paypal' => new PayPalGateway([
//                 'client_id'  => $keys->paypal_client_id,
//                 'secret'     => $keys->paypal_secret,
//                 'base'       => $keys->paypal_base_url ?? 'https://api.paypal.com',
//                 'webhook_id' => $keys->paypal_webhook_id,
//             ]),
//             default => throw new \Exception("Unsupported provider [$provider]"),
//         };
//     }

//     protected function hasProviderKeys(AccountKey $keys, string $provider): bool
//     {
//         return match ($provider) {
//             'stripe' => !empty($keys->stripe_secret_key),
//             'paypal' => !empty($keys->paypal_client_id) && !empty($keys->paypal_secret),
//             default => false,
//         };
//     }
// }


// class PaymentGatewayFactory
// {
//     public function __construct() {}
//     // public function __construct(
//     //     protected StripeGateway $stripe,
//     //     protected PayPalGateway $paypal,
//     // ) {}

//     // public function forProvider(string $provider): PaymentGateway
//     // {
//     //     return match ($provider) {
//     //         'paypal' => $this->paypal,
//     //         default  => $this->stripe,
//     //     };
//     // }

//     // public function forProviderWithBrand(string $provider, Brand $brand): PaymentGateway
//     // {
//     //     $keys = AccountKey::where('brand_id', $brand->id)
//     //         ->where('status', 'active')
//     //         ->first();

//     //     if (!$keys) {
//     //         throw new \Exception("No keys found for brand #{$brand->id}");
//     //     }
//     //     dd($keys);

//     //     return match ($provider) {
//     //         'stripe' => new StripeGateway($keys->stripe_secret_key),
//     //         'paypal' => new PayPalGateway([
//     //             'client_id'  => $keys->paypal_client_id,
//     //             'secret'     => $keys->paypal_secret,
//     //             'base'       => $keys->paypal_base_url ?? 'https://api.paypal.com',
//     //             'webhook_id' => $keys->paypal_webhook_id,
//     //         ]),
//     //         default => throw new \Exception("Unsupported provider [$provider]"),
//     //     };
//     // }

//     public function forProviderWithBrand(string $provider, Brand $brand): PaymentGateway
//     {
//         $keys = AccountKey::where('brand_id', $brand->id)
//             ->where('status', 'active')
//             ->first();

//         if (!$keys) {
//             throw new \Exception("No keys found for brand #{$brand->id}");
//         }

//         return match ($provider) {
//             'stripe' => new StripeGateway($keys->stripe_secret_key),
//             'paypal' => new PayPalGateway([
//                 'client_id'  => $keys->paypal_client_id,
//                 'secret'     => $keys->paypal_secret,
//                 'base'       => $keys->paypal_base_url ?? 'https://api.paypal.com',
//                 'webhook_id' => $keys->paypal_webhook_id,
//             ]),
//             default => throw new \Exception("Unsupported provider [$provider]"),
//         };
//     }

//     // Optional fallback
//     public function forProvider(string $provider): PaymentGateway
//     {
//         return match ($provider) {
//             'stripe' => new StripeGateway(config('services.stripe.secret')),
//             'paypal' => new PayPalGateway([
//                 'client_id' => config('services.paypal.client_id'),
//                 'secret'    => config('services.paypal.secret'),
//                 'base'      => config('services.paypal.base_url', 'https://api.paypal.com'),
//                 'webhook_id' => config('services.paypal.webhook_id'),
//             ]),
//         };
//     }
// }

// final class BrandPaymentConfig
// {
//     public function __construct(public Brand $brand) {}

//     public function stripe(): array
//     {
//         return [
//             'mode' => $this->brand->stripe_mode,
//             'pk'   => $this->brand->stripe_publishable_key,
//             'sk'   => $this->brand->stripe_secret_key,
//             'wh'   => $this->brand->stripe_webhook_secret,
//         ];
//     }

//     public function paypal(): array
//     {
//         $base = $this->brand->paypal_base
//             ?: ($this->brand->paypal_mode === 'live'
//                 ? 'https://api-m.paypal.com'
//                 : 'https://api-m.sandbox.paypal.com');

//         return [
//             'mode'        => $this->brand->paypal_mode,
//             'client_id'   => $this->brand->paypal_client_id,
//             'secret'      => $this->brand->paypal_client_secret,
//             'webhook_id'  => $this->brand->paypal_webhook_id,
//             'base'        => $base,
//         ];
//     }
// }
