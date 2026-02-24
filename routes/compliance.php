<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Seller\WebhookController;
use App\Http\Controllers\Seller\PayPalPaymentController;


Route::get('/user-profile', [ProfileController::class, 'authProfile'])->name('auth.profile.get');
Route::post('/profile-update', [ProfileController::class, 'updateProfile'])->name('auth.profile.update');
// update statuses profile
Route::post('/lead-update-status', [ManagementController::class, 'updateLeadStatus'])->name('lead.update-status');
Route::post('/ticket-update-status', [ExportController::class, 'updateTicketStatus'])->name('ticket.update-status');
// addd client account assceess
Route::post('/client-account-access', [ManagementController::class, 'clientAccountAccess'])->name('client.account-access');

// pyament linkt active inactive
Route::post('/paylink-status', [ManagementController::class, 'changePaylinkStatus'])->name('change.paylink-status');
// generate invoice
Route::get(
    '/generate/{order?}/invoice',
    [ManagementController::class, 'generateInvoice']
)->whereNumber(['order'])->name('order.generate-invoice');

Route::prefix('brand')->group(function () {

    // payment link creation
    Route::get('/{brand?}/lead/{lead?}/generate-link/{order?}', [CheckoutController::class, 'generateLinkForm'])->whereNumber(['brand', 'lead', 'order'])->name('generate-link-form');
    Route::post('/{brand?}/lead/{lead?}/generate-link', [CheckoutController::class, 'generatePayLink'])->name('generate-payment-link');

    // generate link for renewal order
    Route::get('/{brand?}/lead/{lead?}/renew-order/{order?}/', [CheckoutController::class, 'renewOrderLink'])->whereNumber(['brand', 'lead', 'order'])->name('renew-order-link');
});

Route::prefix('pay')->group(function () {

    Route::get('/now/{token?}', [WebhookController::class, 'showPaymentPage'])
        ->name('paylinks.show');

    Route::post('/now/{token?}/checkout', [WebhookController::class, 'createCheckout'])
        ->name('paylinks.checkout');

    Route::get('/now/{token?}/success', [WebhookController::class, 'checkoutSuccess'])
        ->name('paylinks.success');

    // NEW
    Route::get('/now/{token?}/cancel', [WebhookController::class, 'checkoutCancel'])
        ->name('paylinks.cancel');

    // NEW
    Route::get('/now/{token?}/error', [WebhookController::class, 'checkoutError'])
        ->name('paylinks.error');
});


Route::get('/pay/paypal/{token?}/return', [PayPalPaymentController::class, 'paypalReturn'])->name('paypal.return');
Route::get('/payments/{token}/success', [PayPalPaymentController::class, 'successPaid'])->name('payments.thanks');


// Webhooks – separate endpoints (providers require fixed URLs)
// Route::post('/webhooks/stripe',  [WebhookController::class, 'handleWebhook'])->defaults('provider', 'stripe')->name('stripe.webhook')->withoutMiddleware([\App\Http\Middleware\VerifyCSRFToken::class]);
// Route::post('/webhooks/paypal',  [WebhookController::class, 'handleWebhook'])->defaults('provider', 'paypal')->name('paypal.webhook')->withoutMiddleware([\App\Http\Middleware\VerifyCSRFToken::class]);
