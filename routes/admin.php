<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ManagementController;
use App\Http\Controllers\Admin\LeadsController;
use App\Http\Controllers\Admin\ViewsController;

use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminBrandsController;
use App\Http\Controllers\Admin\AdminSellerController;
use App\Http\Controllers\Admin\UpworkController;
use App\Http\Controllers\API\Client\BrandConfigController;

Route::group(['prefix' => 'admin'], function () {
    //admin auth
    Route::get('/login', [AdminAuthController::class, 'adminLoginPage'])->name('admin.login.get');
    Route::post('/login', [AdminAuthController::class, 'adminLoginPost'])->name('admin.login.post');
    Route::get('/forgot-password', [AdminAuthController::class, 'adminForgotPage'])->name('admin.forgot.get');
    Route::get('/reset/{token?}/password', [AdminAuthController::class, 'adminResetPage'])->name('admin.reset.get');
    Route::post('/forgot-password', [AdminAuthController::class, 'adminForgotPost'])->name('admin.forgot.post');
    Route::post('/reset-password', [AdminAuthController::class, 'adminResetPost'])->name('admin.reset.post');
    Route::get('/logout', [AdminAuthController::class, 'adminlogout'])->name('admin.logout');

    Route::group(['middleware' => ['membersOnline', 'admin']], function () {
        // acount keys
        Route::get('/dashboard', [ViewsController::class, 'adminDashboard'])->name('admin.index.get');
        Route::get('/account-keys', [ViewsController::class, 'adminAccountKeys'])->name('admin.account-keys.get');
        Route::post('/account-keys', [ViewsController::class, 'accountKeyStore'])->name('admin.account-keys.post');
        Route::post('/account-keys/{id?}/update', [ViewsController::class, 'accountKeysUpdate'])->name('admin.account-keys-update');

        // clients
        Route::get('/clients', [ViewsController::class, 'adminClients'])->name('admin.clients.get');
        Route::get('/client/{id?}/briefs', [ManagementController::class, 'clientBriefs'])->name('admin.client-briefs.get');
        Route::post('/client-delete', [ManagementController::class, 'deleteClient'])->name('admin.client.delete');
        Route::post('/client-status', [ManagementController::class, 'updateClientStatus'])->name('admin.client.updateStatus');

        // brands and payouts
        Route::get('/brands', [AdminBrandsController::class, 'adminBrands'])->name('admin.brands.get');
        Route::post('/brand-post', [AdminBrandsController::class, 'adminBrandPost'])->name('admin.brand.post');
        Route::get('/brand-payments', [AdminBrandsController::class, 'adminBrandPayments'])->name('admin.brand-payments.get');
        Route::get('/brand-payouts', [AdminBrandsController::class, 'adminBrandPayouts'])->name('admin.brand-payouts.get');

        // domain scripts
        Route::get('/domain-scripts', [BrandConfigController::class, 'adminDomainScripts'])->name('admin.domain-script.get');
        Route::post('/domain-scripts', [BrandConfigController::class, 'domainScriptStore'])->name('admin.domain-scripts.post');
        Route::put('/domain-scripts/{brand?}/update', [BrandConfigController::class, 'domainScriptUpdate'])->name('admin.domain-scripts-update');

        // sellers tabs
        Route::get('/sellers', [AdminSellerController::class, 'adminSellers'])->name('admin.sellers.get');
        Route::post('/seller-post', [AdminSellerController::class, 'adminSellerPost'])->name('admin.seller.post');
        Route::get('/seller/{id?}/performance', [AdminSellerController::class, 'adminSellerPerformance'])->name('admin.seller-performance.get');
        Route::post('/seller-status', [AdminSellerController::class, 'sellerUpdateStatus'])->name('admin.seller.updateStatus');

        // leads assign route & leads data
        Route::get('/leads', [LeadsController::class, 'adminLeads'])->name('admin.leads.get');
        Route::get('/lead/{id?}/details', [LeadsController::class, 'adminLeadDetails'])->name('admin.lead-details.get');
        Route::get('/assigned-leads', [LeadsController::class, 'sellerAssignedLeads'])->name('admin.assigned-leads.get');
        Route::match(['get', 'post'], '/lead-delete/{id?}', [ManagementController::class, 'deleteLeads'])->name('admin.leads.delete');
        Route::post('/lead-assign', [AdminSellerController::class, 'assignLeadSeller'])->name('lead-assign.post');
        Route::post('/admin/lead-views/clear', [LeadsController::class, 'clearLeadViewLogs'])
            ->name('admin.lead.logs.clear');

        // client renewd orders and renew oders
        Route::get('/orders', [AdminOrderController::class, 'adminOrders'])->name('admin.orders.get');
        Route::get('/renewed/{order?}/orders', [AdminOrderController::class, 'adminOrderRenewals'])->name('admin.renewed-orders.get');
        // Route::get('/renewed/{q?}/orders', [AdminOrderController::class, 'adminClientRenewedOrders'])->name('admin.renewed-orders.get');
        Route::get('/assigned-leads-orders', [AdminOrderController::class, 'adminPMOrders'])->name('admin.assigned-leads-orders.get');
        Route::get('/payments', [AdminOrderController::class, 'adminPayments'])->name('admin.payments.get');

        // tickets
        Route::get('/order/{order?}/tickets', [ExportController::class, 'adminOrderTickets'])->name('admin.order-tickets.get');
        Route::get('/order/ticket/{id?}/details', [ExportController::class, 'getTicketDetails'])->name('admin.tickets.details');
        Route::match(['get', 'post'], '/ticket-delete/{id?}', [ExportController::class, 'deleteTickets'])->name('admin.tickets.delete');
        Route::get('/export/{table?}', [ExportController::class, 'exportLeadsCsv'])->name('export.csv')->whereIn('table', ['leads', 'clients', 'orders', 'payments']);

        // client, Seller and brand status
        Route::post('/domain-delete', [ManagementController::class, 'deleteDomain'])->name('admin.domain.delete');
        Route::post('/domain-status', [ManagementController::class, 'updateDomainStatus'])->name('admin.domain.updateStatus');


        // form creation
        Route::get('/create-briefs', [ViewsController::class, 'createBriefForms'])->name('admin.create-briefs.get');
        Route::post('/admin/forms/store', [ViewsController::class, 'store'])
            ->name('admin.forms.store');

        // Admin preview (protected)
        Route::get('/admin/forms/{id}/preview', [ViewsController::class, 'preview'])
            ->name('admin.forms.preview');

        // Client public form
        Route::get('/form/{id}', [ViewsController::class, 'show'])
            ->name('forms.show');

        // Client submit
        Route::post('/form/{id}', [ViewsController::class, 'submit'])
            ->name('forms.submit');


        // Upwork client orders and renew oders
        Route::get('/upwork-clients', [UpworkController::class, 'upworkClients'])->name('admin.upwork-clients.get');
        Route::get('/upwork-orders', [UpworkController::class, 'upworkOrders'])->name('admin.upwork-orders.get');
        Route::get('/upwork-payments', [UpworkController::class, 'upworkPayments'])->name('admin.upwork-payments.get');
    });
});
