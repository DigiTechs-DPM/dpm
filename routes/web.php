<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ManagementController;

use App\Http\Controllers\Admin\LeadsController;
use App\Http\Controllers\Admin\ViewsController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminBrandsController;
use App\Http\Controllers\Admin\AdminSellerController;

use App\Http\Controllers\Seller\PagesController;
use App\Http\Controllers\Seller\RoughController;
use App\Http\Controllers\Seller\WebhookController;
use App\Http\Controllers\Seller\SellerAuthController;
use App\Http\Controllers\Seller\SellerDataController;
use App\Http\Controllers\Seller\SellerBrandController;
use App\Http\Controllers\Seller\SellerLeadsController;
use App\Http\Controllers\Seller\SellerOrderController;
use App\Http\Controllers\Seller\SellerExportController;
use App\Http\Controllers\Seller\PayPalPaymentController;
use App\Http\Controllers\Seller\SellerManagementController;

use App\Http\Controllers\API\Client\BrandConfigController;
use App\Http\Controllers\API\Client\BriefController as ClientBriefController;
use App\Http\Controllers\API\Client\AuthController as ClientAuthController;
use App\Http\Controllers\API\Client\LeadsController as ClientLeadsController;
use App\Http\Controllers\API\Client\PagesController as ClientPagesController;


// Routes start here
Route::prefix('/')->group(function () {
    Route::view('/', 'welcome-crm')->name('welcome.get');
    Route::view('/nexus', 'nexus')->name('index.get');
});


// // auth client auth
// Route::group(['prefix' => 'client', 'namespace' => 'Client'], function () {
//     Route::get('/login', [ClientAuthController::class, 'clientLoginPage'])->name('client.login.get');
//     Route::post('/login', [ClientAuthController::class, 'clientLoginPost'])->name('client.login.post');
//     Route::get('/forgot-password', [ClientAuthController::class, 'clientForgotPage'])->name('client.forgot.get');
//     Route::get('/reset/{token?}/password', [ClientAuthController::class, 'clientResetPage'])->name('client.reset.get');
//     Route::post('/forgot-password', [ClientAuthController::class, 'clientForgotPost'])->name('client.forgot.post');
//     Route::post('/reset-password', [ClientAuthController::class, 'clientResetPost'])->name('client.reset.post');
//     Route::get('/logout', [ClientAuthController::class, 'clientlogout'])->name('client.logout');

//     // brief with token
//     Route::get('/brief/{token?}', [ClientBriefController::class, 'showBriefForm'])->name('brief.show');
//     Route::post('/brief/{token?}', [ClientBriefController::class, 'submit'])->name('brief.submit');

//     // admin views
//     Route::group(['middleware' => 'client'], function () {
//         // client dashboard
//         Route::get('/dashboard', [ClientPagesController::class, 'clientIndex'])->name('client.index.get');
//         Route::get('/messages', [ClientPagesController::class, 'clientMessages'])->name('client.messages.get');
//         Route::get('/invoices', [ClientPagesController::class, 'clientInvoices'])->name('client.invoice.get');
//         Route::get('/invoice/{order?}/details', [ClientPagesController::class, 'clientInvoiceDetails'])->name('client.invoice.details');
//         Route::get('/profile', [ClientPagesController::class, 'clientProfile'])->name('client.profile.get');
//         Route::post('/profile-update', [ClientAuthController::class, 'updateProfile'])->name('auth.profile.update');

//         Route::get('/raise/{order?}/ticket', [ClientLeadsController::class, 'clientRaiseTicket'])->name('client.raise-ticket.get');
//         Route::get('/raised-tickets', [ClientLeadsController::class, 'clientTickets'])->name('client.raised-tickets.get');
//         Route::post('/raised-ticket', [ClientLeadsController::class, 'clientTicketStore'])->name('client.raised-tickets.post');

//         // brief from
//         Route::get('/briefs', [ClientBriefController::class, 'clientBriefs'])->name('client.brief.get');
//         Route::post('/brief-form', [ClientBriefController::class, 'clientBriefPost'])->name('client.brief-form.post');
//     });
// });

// // admin views -- toggle mode
// Route::get('/web-drop', [ManagementController::class, 'toggleSettingDown'])->name('site.down');
// Route::get('/web-pick', [ManagementController::class, 'toggleSettingUp'])->name('site.up');


// Route::group(['prefix' => 'admin'], function () {
//     //admin auth
//     Route::get('/login', [AdminAuthController::class, 'adminLoginPage'])->name('admin.login.get');
//     Route::post('/login', [AdminAuthController::class, 'adminLoginPost'])->name('admin.login.post');
//     Route::get('/forgot-password', [AdminAuthController::class, 'adminForgotPage'])->name('admin.forgot.get');
//     Route::get('/reset/{token?}/password', [AdminAuthController::class, 'adminResetPage'])->name('admin.reset.get');
//     Route::post('/forgot-password', [AdminAuthController::class, 'adminForgotPost'])->name('admin.forgot.post');
//     Route::post('/reset-password', [AdminAuthController::class, 'adminResetPost'])->name('admin.reset.post');
//     Route::get('/logout', [AdminAuthController::class, 'adminlogout'])->name('admin.logout');

//     Route::group(['middleware' => ['membersOnline', 'admin']], function () {
//         // acount keys
//         Route::get('/dashboard', [ViewsController::class, 'adminDashboard'])->name('admin.index.get');
//         Route::get('/account-keys', [ViewsController::class, 'adminAccountKeys'])->name('admin.account-keys.get');
//         Route::post('/account-keys', [ViewsController::class, 'accountKeyStore'])->name('admin.account-keys.post');
//         Route::post('/account-keys/{id?}/update', [ViewsController::class, 'accountKeysUpdate'])->name('admin.account-keys-update');

//         // clients
//         Route::get('/clients', [ViewsController::class, 'adminClients'])->name('admin.clients.get');
//         Route::get('/client/{id?}/briefs', [ManagementController::class, 'clientBriefs'])->name('admin.client-briefs.get');
//         Route::post('/client-delete', [ManagementController::class, 'deleteClient'])->name('admin.client.delete');
//         Route::post('/client-status', [ManagementController::class, 'updateClientStatus'])->name('admin.client.updateStatus');

//         // brands and payouts
//         Route::get('/brands', [AdminBrandsController::class, 'adminBrands'])->name('admin.brands.get');
//         Route::post('/brand-post', [AdminBrandsController::class, 'adminBrandPost'])->name('admin.brand.post');
//         Route::get('/brand-payments', [AdminBrandsController::class, 'adminBrandPayments'])->name('admin.brand-payments.get');
//         Route::get('/brand-payouts', [AdminBrandsController::class, 'adminBrandPayouts'])->name('admin.brand-payouts.get');

//         // domain scripts
//         Route::get('/domain-scripts', [BrandConfigController::class, 'adminDomainScripts'])->name('admin.domain-script.get');
//         Route::post('/domain-scripts', [BrandConfigController::class, 'domainScriptStore'])->name('admin.domain-scripts.post');
//         Route::put('/domain-scripts/{brand?}/update', [BrandConfigController::class, 'domainScriptUpdate'])->name('admin.domain-scripts-update');

//         // sellers tabs
//         Route::get('/sellers', [AdminSellerController::class, 'adminSellers'])->name('admin.sellers.get');
//         Route::post('/seller-post', [AdminSellerController::class, 'adminSellerPost'])->name('admin.seller.post');
//         Route::get('/seller/{id?}/performance', [AdminSellerController::class, 'adminSellerPerformance'])->name('admin.seller-performance.get');
//         Route::post('/seller-status', [AdminSellerController::class, 'sellerUpdateStatus'])->name('admin.seller.updateStatus');

//         // leads assign route & leads data
//         Route::get('/leads', [LeadsController::class, 'adminLeads'])->name('admin.leads.get');
//         Route::get('/lead/{id?}/details', [LeadsController::class, 'adminLeadDetails'])->name('admin.lead-details.get');
//         Route::get('/assigned-leads', [LeadsController::class, 'sellerAssignedLeads'])->name('admin.assigned-leads.get');
//         Route::match(['get', 'post'], '/lead-delete/{id?}', [ManagementController::class, 'deleteLeads'])->name('admin.leads.delete');
//         Route::post('/lead-assign', [AdminSellerController::class, 'assignLeadSeller'])->name('lead-assign.post');
//         Route::post('/admin/lead-views/clear', [LeadsController::class, 'clearLeadViewLogs'])
//             ->name('admin.lead.logs.clear');

//         // client renewd orders and renew oders
//         Route::get('/orders', [AdminOrderController::class, 'adminOrders'])->name('admin.orders.get');
//         Route::get('/renewed/{order?}/orders', [AdminOrderController::class, 'adminOrderRenewals'])->name('admin.renewed-orders.get');
//         // Route::get('/renewed/{q?}/orders', [AdminOrderController::class, 'adminClientRenewedOrders'])->name('admin.renewed-orders.get');
//         Route::get('/assigned-leads-orders', [AdminOrderController::class, 'adminPMOrders'])->name('admin.assigned-leads-orders.get');
//         Route::get('/payments', [AdminOrderController::class, 'adminPayments'])->name('admin.payments.get');

//         // tickets
//         Route::get('/order/{order?}/tickets', [ExportController::class, 'adminOrderTickets'])->name('admin.order-tickets.get');
//         Route::get('/order/ticket/{id?}/details', [ExportController::class, 'getTicketDetails'])->name('admin.tickets.details');
//         Route::match(['get', 'post'], '/ticket-delete/{id?}', [ExportController::class, 'deleteTickets'])->name('admin.tickets.delete');
//         Route::get('/export/{table?}', [ExportController::class, 'exportLeadsCsv'])->name('export.csv')->whereIn('table', ['leads', 'clients', 'orders', 'payments']);

//         // client, Seller and brand status
//         Route::post('/domain-delete', [ManagementController::class, 'deleteDomain'])->name('admin.domain.delete');
//         Route::post('/domain-status', [ManagementController::class, 'updateDomainStatus'])->name('admin.domain.updateStatus');
//     });
// });

// // seller routes
// Route::group([
//     'prefix' => 'seller'
// ], function () {
//     //admin auth
//     Route::get('/login', [SellerAuthController::class, 'sellerLoginPage'])->name('seller.login.get');
//     Route::post('/login', [SellerAuthController::class, 'sellerLoginPost'])->name('seller.login.post');
//     Route::get('/forgot-password', [SellerAuthController::class, 'sellerForgotPage'])->name('seller.forgot.get');
//     Route::get('/reset/{token?}/password', [SellerAuthController::class, 'sellerResetPage'])->name('seller.reset.get');
//     Route::post('/forgot-password', [SellerAuthController::class, 'sellerForgotPost'])->name('seller.forgot.post');
//     Route::post('/reset-password', [SellerAuthController::class, 'sellerResetPost'])->name('seller.reset.post');
//     Route::get('/logout', [SellerAuthController::class, 'sellerlogout'])->name('seller.logout');

//     Route::group(['middleware' => ['membersOnline', 'seller']], function () {
//         // update lead status
//         Route::get('/lead/{id?}/finish', [SellerOrderController::class, 'sellerLeadFinish'])->name('seller.lead.finish');
//         Route::post('/lead/update-status', [RoughController::class, 'updateAssignedLeadStatus'])->name('seller.assignment.update-status');

//         // acount keys
//         Route::get('/dashboard', [PagesController::class, 'sellerDashboard'])->name('seller.index.get');
//         // clients
//         Route::get('/clients', [PagesController::class, 'sellerClients'])->name('seller.clients.get');
//         Route::get('/client/{id?}/briefs', [ManagementController::class, 'clientBriefs'])->name('seller.client-briefs.get');
//         Route::post('/client-delete', [SellerManagementController::class, 'deleteClient'])->name('seller.client.delete');
//         Route::post('/client-status', [SellerManagementController::class, 'updateClientStatus'])->name('seller.client.updateStatus');

//         // brands and payouts
//         Route::get('/brands', [SellerBrandController::class, 'sellerBrands'])->name('seller.brands.get');
//         Route::post('/brand-post', [SellerBrandController::class, 'sellerBrandPost'])->name('seller.brand.post');
//         Route::get('/brand-payments', [SellerBrandController::class, 'sellerBrandPayments'])->name('seller.brand-payments.get');
//         Route::get('/brand-payouts', [SellerBrandController::class, 'sellerBrandPayouts'])->name('seller.brand-payouts.get');

//         // domain scripts
//         Route::get('/domain-scripts', [PagesController::class, 'sellerDomainScripts'])->name('seller.domain-script.get');
//         // Route::post('/domain-scripts', [BrandScriptController::class, 'domainScriptStore'])->name('seller.domain-scripts.post');
//         // Route::put('/domain-scripts/{brand?}/update', [BrandScriptController::class, 'domainScriptUpdate'])->name('seller.domain-scripts-update');

//         // sellers tabs
//         Route::get('/sellers', [SellerDataController::class, 'sellerSellers'])->name('seller.sellers.get');
//         Route::post('/seller-post', [SellerDataController::class, 'sellerSellerPost'])->name('seller.seller.post');
//         Route::get('/seller/{id?}/performance', [SellerDataController::class, 'sellerSellerPerformance'])->name('seller.seller-performance.get');
//         Route::get('/seller-leaderboard', [SellerDataController::class, 'sellerSellerLeaderboard'])->name('seller.seller-leaderboard.get');
//         Route::post('/seller/change-domain', [SellerDataController::class, 'changeDomain'])->name('seller.seller.changeDomain');
//         Route::post('/seller-status', [SellerDataController::class, 'sellerUpdateStatus'])->name('seller.seller.updateStatus');

//         // leads data
//         Route::get('/leads', [SellerLeadsController::class, 'sellerLeads'])->name('seller.leads.get');
//         Route::get('/lead/{id?}/details', [SellerLeadsController::class, 'sellerLeadDetails'])->name('seller.lead-details.get');
//         Route::get('/assigned-leads', [SellerLeadsController::class, 'sellerAssignedLeads'])->name('seller.assigned-leads.get');
//         Route::match(['get', 'post'], '/lead-delete/{id?}', [SellerManagementController::class, 'deleteLeads'])->name('seller.leads.delete');


//         // client renewd orders and renew oders
//         Route::get('/orders', [SellerOrderController::class, 'sellerOrders'])->name('seller.orders.get');
//         Route::get('/renewed/{order?}/orders', [SellerOrderController::class, 'sellerOrderRenewals'])->name('seller.renewed-orders.get');
//         // Route::get('/renewed/{order?}/orders', [SellerOrderController::class, 'sellerClientRenewedOrders'])->name('seller.renewed-orders.get');
//         Route::get('/assigned-leads-orders', [SellerOrderController::class, 'sellerPMOrders'])->name('seller.assigned-leads-orders.get');
//         Route::get('/payments', [SellerOrderController::class, 'sellerPayments'])->name('seller.payments.get');

//         // tickets
//         Route::get('/order/{order?}/tickets', [SellerExportController::class, 'sellerOrderTickets'])->name('seller.order-tickets.get');
//         Route::get('/order/ticket/{id?}/details', [SellerExportController::class, 'getTicketDetails'])->name('seller.tickets.details');
//         Route::match(['get', 'post'], '/ticket-delete/{id?}', [SellerExportController::class, 'deleteTickets'])->name('seller.tickets.delete');

//         // generate invoice
//         Route::get(
//             '/generate/{order?}/invoice',
//             [SellerExportController::class, 'generateInvoice']
//         )->whereNumber(['order'])->name('seller.order.generate-invoice');


//         // client, Seller and brand status
//         Route::post('/domain-delete', [SellerManagementController::class, 'deleteDomain'])->name('seller.domain.delete');
//         Route::post('/domain-status', [SellerManagementController::class, 'updateDomainStatus'])->name('seller.domain.updateStatus');
//     });
// });


// Route::get('/user-profile', [ProfileController::class, 'authProfile'])->name('auth.profile.get');
// Route::post('/profile-update', [ProfileController::class, 'updateProfile'])->name('auth.profile.update');
// // update statuses profile
// Route::post('/lead-update-status', [ManagementController::class, 'updateLeadStatus'])->name('lead.update-status');
// Route::post('/ticket-update-status', [ExportController::class, 'updateTicketStatus'])->name('ticket.update-status');
// // addd client account assceess
// Route::post('/client-account-access', [ManagementController::class, 'clientAccountAccess'])->name('client.account-access');

// // pyament linkt active inactive
// Route::post('/paylink-status', [ManagementController::class, 'changePaylinkStatus'])->name('change.paylink-status');
// // generate invoice
// Route::get(
//     '/generate/{order?}/invoice',
//     [ManagementController::class, 'generateInvoice']
// )->whereNumber(['order'])->name('order.generate-invoice');

// Route::prefix('brand')->group(function () {

//     // payment link creation
//     Route::get('/{brand?}/lead/{lead?}/generate-link/{order?}', [CheckoutController::class, 'generateLinkForm'])->whereNumber(['brand', 'lead', 'order'])->name('generate-link-form');
//     Route::post('/{brand?}/lead/{lead?}/generate-link', [CheckoutController::class, 'generatePayLink'])->name('generate-payment-link');

//     // generate link for renewal order
//     Route::get('/{brand?}/lead/{lead?}/renew-order/{order?}/', [CheckoutController::class, 'renewOrderLink'])->whereNumber(['brand', 'lead', 'order'])->name('renew-order-link');
// });

// Route::prefix('pay')->group(function () {

//     Route::get('/now/{token}', [WebhookController::class, 'showPaymentPage'])
//         ->name('paylinks.show');

//     Route::post('/now/{token}/checkout', [WebhookController::class, 'createCheckout'])
//         ->name('paylinks.checkout');

//     Route::get('/now/{token}/success', [WebhookController::class, 'checkoutSuccess'])
//         ->name('paylinks.success');

//     // NEW
//     Route::get('/now/{token}/cancel', [WebhookController::class, 'checkoutCancel'])
//         ->name('paylinks.cancel');

//     // NEW
//     Route::get('/now/{token}/error', [WebhookController::class, 'checkoutError'])
//         ->name('paylinks.error');
// });


// Route::get('/pay/paypal/{token?}/return', [PayPalPaymentController::class, 'paypalReturn'])->name('paypal.return');
// Route::get('/payments/{token}/success', [PayPalPaymentController::class, 'successPaid'])->name('payments.thanks');


// // Webhooks – separate endpoints (providers require fixed URLs)
// // Route::post('/webhooks/stripe',  [WebhookController::class, 'handleWebhook'])->defaults('provider', 'stripe')->name('stripe.webhook')->withoutMiddleware([\App\Http\Middleware\VerifyCSRFToken::class]);
// // Route::post('/webhooks/paypal',  [WebhookController::class, 'handleWebhook'])->defaults('provider', 'paypal')->name('paypal.webhook')->withoutMiddleware([\App\Http\Middleware\VerifyCSRFToken::class]);
