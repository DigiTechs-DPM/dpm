<?php

use App\Http\Controllers\API\BrandingController;

use App\Http\Controllers\API\Client\ApiAuthController as ClientAuthController;
use App\Http\Controllers\API\Client\ApiDataController;
use App\Http\Controllers\API\Client\BrandConfigController;
use App\Http\Controllers\Api\Client\ClientTicketController;
use App\Http\Controllers\API\Client\LeadsController as ClientLeadsController;
use App\Http\Controllers\Compliances\RefundWebhookController;

use App\Http\Controllers\Seller\PayPalPaymentController;
use App\Http\Controllers\Seller\StripePaymentController;
use App\Http\Controllers\Seller\WebhookController;
use App\Http\Controllers\Upwork\DisputeController;
use Illuminate\Support\Facades\Route;


Route::post('/crm-lead-post', [BrandingController::class, 'storeLead'])
    ->name('crm.leads.post');

Route::post('/crm-order-post', [BrandingController::class, 'directOrder'])
    ->name('crm.order.post');

// routes/api.php
Route::get('/brand-config', [BrandConfigController::class, 'show']);

// script for each domain
Route::get('/lead-script/{host?}.js', [BrandConfigController::class, 'showScript'])
    ->where('host', '.*')
    ->name('lead.script');


// auth client auth
Route::get('/login', [ClientAuthController::class, 'clientLoginPage'])->name('client.login.get');
Route::post('/login', [ClientAuthController::class, 'clientLoginPost'])->name('client.login.post');
Route::get('/forgot-password', [ClientAuthController::class, 'clientForgotPage'])->name('client.forgot.get');
Route::get('/reset/{token?}/password', [ClientAuthController::class, 'clientResetPage'])->name('client.reset.get');
Route::post('/forgot-password', [ClientAuthController::class, 'clientForgotPost'])->name('client.forgot.post');
Route::post('/reset-password', [ClientAuthController::class, 'clientResetPost'])->name('client.reset.post');
Route::get('/logout', [ClientAuthController::class, 'clientlogout'])->name('client.logout')->middleware('auth:sanctum');

// client dashboard
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/client/briefs', [ApiDataController::class, 'clientBriefs']);
    Route::get('/client/invoices', [ApiDataController::class, 'clientInvoices']);
    Route::get('/client/invoices/{order?}', [ApiDataController::class, 'clientInvoiceDetails']);
    Route::get('/client/profile', [ApiDataController::class, 'clientProfile']);
    Route::post('/client/profile-update', [ApiDataController::class, 'updateProfile'])->name('auth.profile.update');

    // tickets
    // Route::get('/tickets', [ClientTicketController::class, 'index']); // Get all tickets
    // Route::post('/tickets', [ClientTicketController::class, 'store']); // Submit new ticket

    Route::get('/raised-tickets', [ClientTicketController::class, 'index'])->name('client.raised-tickets.get');
    Route::post('/raised-tickets', [ClientTicketController::class, 'store'])->name('client.raised-tickets.post');
});


Route::post('/lead-post', [ClientLeadsController::class, 'submitLead'])->name('lead.post');
Route::post('/post-lead', [ClientLeadsController::class, 'storeLead'])->name('post.lead')->middleware('throttle:60,1'); // tune per brand/ip

// webhooks / suucceeded
// Route::post('/webhooks/stripe',  [WebhookController::class, 'handleWebhook'])->defaults('provider', 'stripe')->name('stripe.webhook');
// Route::post('/webhooks/paypal',  [WebhookController::class, 'handleWebhook'])->defaults('provider', 'paypal')->name('paypal.webhook');


Route::prefix('/webhooks')->group(function () {

    // webhooks / suucceeded
    Route::post('/stripe',  [WebhookController::class, 'handleWebhook'])->defaults('provider', 'stripe')->name('stripe.webhook');
    Route::post('/paypal',  [WebhookController::class, 'handleWebhook'])->defaults('provider', 'paypal')->name('paypal.webhook');

    // Stripe
    Route::post('/stripe/refund', [RefundWebhookController::class, 'stripeRefundHandle'])
        ->name('stripe.refund.webhook');

    Route::post('/stripe/dispute', [RefundWebhookController::class, 'stripeDisputeHandle'])
        ->name('stripe.dispute.webhook');

    // PayPal
    Route::post('/paypal/refund', [RefundWebhookController::class, 'paypalRefundHandle'])
        ->name('paypal.refund.webhook');

    Route::post('/paypal/dispute', [RefundWebhookController::class, 'paypalDisputeHandle'])
        ->name('paypal.dispute.webhook');

    // Upwork Stripe Disputes
    Route::post('/upwork-stripe/refund', [DisputeController::class, 'stripeRefundHandle'])
        ->name('stripe.refund.webhook');
    Route::post('/upwork-stripe/dispute', [DisputeController::class, 'stripeDisputeHandle'])
        ->name('stripe.dispute.webhook');

});
