<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ManagementController;
use App\Http\Controllers\Seller\PagesController;
use App\Http\Controllers\Seller\RoughController;
use App\Http\Controllers\Seller\SellerAuthController;
use App\Http\Controllers\Seller\SellerDataController;
use App\Http\Controllers\Seller\SellerBrandController;
use App\Http\Controllers\Seller\SellerLeadsController;
use App\Http\Controllers\Seller\SellerOrderController;
use App\Http\Controllers\Seller\SellerExportController;
use App\Http\Controllers\Seller\SellerManagementController;

// seller routes
Route::group([
    'prefix' => 'seller'
], function () {
    //admin auth
    Route::get('/login', [SellerAuthController::class, 'sellerLoginPage'])->name('seller.login.get');
    Route::post('/login', [SellerAuthController::class, 'sellerLoginPost'])->name('seller.login.post');
    Route::get('/forgot-password', [SellerAuthController::class, 'sellerForgotPage'])->name('seller.forgot.get');
    Route::get('/reset/{token?}/password', [SellerAuthController::class, 'sellerResetPage'])->name('seller.reset.get');
    Route::post('/forgot-password', [SellerAuthController::class, 'sellerForgotPost'])->name('seller.forgot.post');
    Route::post('/reset-password', [SellerAuthController::class, 'sellerResetPost'])->name('seller.reset.post');
    Route::get('/logout', [SellerAuthController::class, 'sellerlogout'])->name('seller.logout');

    Route::group(['middleware' => ['membersOnline', 'seller']], function () {
        // update lead status
        Route::get('/lead/{id?}/finish', [SellerOrderController::class, 'sellerLeadFinish'])->name('seller.lead.finish');
        Route::post('/lead/update-status', [RoughController::class, 'updateAssignedLeadStatus'])->name('seller.assignment.update-status');

        // acount keys
        Route::get('/dashboard', [PagesController::class, 'sellerDashboard'])->name('seller.index.get');
        // clients
        Route::get('/clients', [PagesController::class, 'sellerClients'])->name('seller.clients.get');
        Route::get('/client/{id?}/briefs', [ManagementController::class, 'clientBriefs'])->name('seller.client-briefs.get');
        Route::post('/client-delete', [SellerManagementController::class, 'deleteClient'])->name('seller.client.delete');
        Route::post('/client-status', [SellerManagementController::class, 'updateClientStatus'])->name('seller.client.updateStatus');

        // brands and payouts
        Route::get('/brands', [SellerBrandController::class, 'sellerBrands'])->name('seller.brands.get');
        Route::post('/brand-post', [SellerBrandController::class, 'sellerBrandPost'])->name('seller.brand.post');
        Route::get('/brand-payments', [SellerBrandController::class, 'sellerBrandPayments'])->name('seller.brand-payments.get');
        Route::get('/brand-payouts', [SellerBrandController::class, 'sellerBrandPayouts'])->name('seller.brand-payouts.get');

        // domain scripts
        Route::get('/domain-scripts', [PagesController::class, 'sellerDomainScripts'])->name('seller.domain-script.get');
        // Route::post('/domain-scripts', [BrandScriptController::class, 'domainScriptStore'])->name('seller.domain-scripts.post');
        // Route::put('/domain-scripts/{brand?}/update', [BrandScriptController::class, 'domainScriptUpdate'])->name('seller.domain-scripts-update');

        // sellers tabs
        Route::get('/sellers', [SellerDataController::class, 'sellerSellers'])->name('seller.sellers.get');
        Route::post('/seller-post', [SellerDataController::class, 'sellerSellerPost'])->name('seller.seller.post');
        Route::get('/seller/{id?}/performance', [SellerDataController::class, 'sellerSellerPerformance'])->name('seller.seller-performance.get');
        Route::get('/seller-leaderboard', [SellerDataController::class, 'sellerSellerLeaderboard'])->name('seller.seller-leaderboard.get');
        Route::post('/seller/change-domain', [SellerDataController::class, 'changeDomain'])->name('seller.seller.changeDomain');
        Route::post('/seller-status', [SellerDataController::class, 'sellerUpdateStatus'])->name('seller.seller.updateStatus');

        // leads data
        Route::get('/leads', [SellerLeadsController::class, 'sellerLeads'])->name('seller.leads.get');
        Route::get('/lead/{id?}/details', [SellerLeadsController::class, 'sellerLeadDetails'])->name('seller.lead-details.get');
        Route::get('/assigned-leads', [SellerLeadsController::class, 'sellerAssignedLeads'])->name('seller.assigned-leads.get');
        Route::match(['get', 'post'], '/lead-delete/{id?}', [SellerManagementController::class, 'deleteLeads'])->name('seller.leads.delete');


        // client renewd orders and renew oders
        Route::get('/orders', [SellerOrderController::class, 'sellerOrders'])->name('seller.orders.get');
        Route::get('/renewed/{order?}/orders', [SellerOrderController::class, 'sellerOrderRenewals'])->name('seller.renewed-orders.get');
        // Route::get('/renewed/{order?}/orders', [SellerOrderController::class, 'sellerClientRenewedOrders'])->name('seller.renewed-orders.get');
        Route::get('/assigned-leads-orders', [SellerOrderController::class, 'sellerPMOrders'])->name('seller.assigned-leads-orders.get');
        Route::get('/payments', [SellerOrderController::class, 'sellerPayments'])->name('seller.payments.get');

        // tickets
        Route::get('/order/{order?}/tickets', [SellerExportController::class, 'sellerOrderTickets'])->name('seller.order-tickets.get');
        Route::get('/order/ticket/{id?}/details', [SellerExportController::class, 'getTicketDetails'])->name('seller.tickets.details');
        Route::match(['get', 'post'], '/ticket-delete/{id?}', [SellerExportController::class, 'deleteTickets'])->name('seller.tickets.delete');

        // generate invoice
        Route::get(
            '/generate/{order?}/invoice',
            [SellerExportController::class, 'generateInvoice']
        )->whereNumber(['order'])->name('seller.order.generate-invoice');


        // client, Seller and brand status
        Route::post('/domain-delete', [SellerManagementController::class, 'deleteDomain'])->name('seller.domain.delete');
        Route::post('/domain-status', [SellerManagementController::class, 'updateDomainStatus'])->name('seller.domain.updateStatus');
    });
});
