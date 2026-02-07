<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\Client\BriefController as ClientBriefController;
use App\Http\Controllers\API\Client\AuthController as ClientAuthController;
use App\Http\Controllers\API\Client\LeadsController as ClientLeadsController;
use App\Http\Controllers\API\Client\PagesController as ClientPagesController;

// auth client auth
Route::group(['prefix' => 'client', 'namespace' => 'Client'], function () {
    Route::get('/login', [ClientAuthController::class, 'clientLoginPage'])->name('client.login.get');
    Route::post('/login', [ClientAuthController::class, 'clientLoginPost'])->name('client.login.post');
    Route::get('/forgot-password', [ClientAuthController::class, 'clientForgotPage'])->name('client.forgot.get');
    Route::get('/reset/{token?}/password', [ClientAuthController::class, 'clientResetPage'])->name('client.reset.get');
    Route::post('/forgot-password', [ClientAuthController::class, 'clientForgotPost'])->name('client.forgot.post');
    Route::post('/reset-password', [ClientAuthController::class, 'clientResetPost'])->name('client.reset.post');
    Route::get('/logout', [ClientAuthController::class, 'clientlogout'])->name('client.logout');

    // brief with token
    Route::get('/brief/{token?}', [ClientBriefController::class, 'showBriefForm'])->name('brief.show');
    Route::post('/brief/{token?}', [ClientBriefController::class, 'submit'])->name('brief.submit');

    // admin views
    Route::group(['middleware' => 'client'], function () {
        // client dashboard
        Route::get('/dashboard', [ClientPagesController::class, 'clientIndex'])->name('client.index.get');
        Route::get('/messages', [ClientPagesController::class, 'clientMessages'])->name('client.messages.get');
        Route::get('/invoices', [ClientPagesController::class, 'clientInvoices'])->name('client.invoice.get');
        Route::get('/invoice/{order?}/details', [ClientPagesController::class, 'clientInvoiceDetails'])->name('client.invoice.details');
        Route::get('/profile', [ClientPagesController::class, 'clientProfile'])->name('client.profile.get');
        Route::post('/profile-update', [ClientAuthController::class, 'updateProfile'])->name('auth.profile.update');

        Route::get('/raise/{order?}/ticket', [ClientLeadsController::class, 'clientRaiseTicket'])->name('client.raise-ticket.get');
        Route::get('/raised-tickets', [ClientLeadsController::class, 'clientTickets'])->name('client.raised-tickets.get');
        Route::post('/raised-ticket', [ClientLeadsController::class, 'clientTicketStore'])->name('client.raised-tickets.post');

        // brief from
        Route::get('/briefs', [ClientBriefController::class, 'clientBriefs'])->name('client.brief.get');
        Route::post('/brief-form', [ClientBriefController::class, 'clientBriefPost'])->name('client.brief-form.post');
    });
});
