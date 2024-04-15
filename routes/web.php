<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AccountManagerProfileController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InternalNewsController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadFrontController;
use App\Http\Controllers\LeadUploadsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PaymentSubmissionController;
use App\Http\Controllers\SaleOrderController;
use App\Http\Controllers\UserClientController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Profile Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | Menu Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/news', function () {
        return Inertia::render('Components/News');
    })->name('news');

    Route::get('/data/announcements', [AnnouncementController::class, 'getAnnouncements']);
    Route::get('/data/account-manager-profiles', [AccountManagerProfileController::class, 'getAccountManagerProfiles']);
    Route::get('/data/applications', [ApplicationController::class, 'getApplications']);
    Route::get('/data/documents', [DocumentController::class, 'getDocuments']);
    Route::get('/data/internal-news', [InternalNewsController::class, 'getInternalNews']);
    Route::get('/data/leads', [LeadController::class, 'getLeads']);
    Route::get('/data/leads/duplicates', [LeadController::class, 'getDuplicatedLeads']);
    Route::get('/data/leads/categories', [LeadController::class, 'getCategories']);
    Route::get('/data/lead-fronts/categories', [LeadFrontController::class, 'getLeadFrontCategories']);
    Route::get('/data/lead-fronts', [LeadFrontController::class, 'getAllLeadFronts']);
    Route::get('/data/lead-uploads', [LeadUploadsController::class, 'getLeadUploads']);
    Route::get('/data/notifications', [NotificationController::class, 'getNotifications']);
    Route::get('/data/orders', [OrderController::class, 'getOrders']);
    Route::get('/data/orders/categories', [OrderController::class, 'getCategories']);
    Route::get('/data/payment-methods', [PaymentMethodController::class, 'getPaymentMethods']);
    Route::get('/data/payment-submissions', [PaymentSubmissionController::class, 'getPaymentSubmissions']);
    Route::get('/data/sale-orders', [SaleOrderController::class, 'getSaleOrders']);
    Route::get('/data/users-clients', [UserClientController::class, 'getUsersClients']);
    Route::get('/data/users-clients/categories', [UserClientController::class, 'getCategories']);

    // Route to function that clear session error messages: used by back function in composables
    Route::post('/clear-session-messages', [LeadController::class, 'clearSessionMessages']);

});

Route::middleware(['auth', 'verified'])->prefix('crm')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | CRM Routes
    |--------------------------------------------------------------------------
    |*/

    /*
    |--------------------------------------------------------------------------
    | Account Manager Profiles Routes
    */
        Route::get('/account-manager-profiles', function () {
            return Inertia::render('CRM/AccountManagerProfiles/Index');
        })->name('crm.account-manager-profiles');

    /*
    |--------------------------------------------------------------------------
    | Announcements Routes
    */
        Route::get('/announcements', function () {
            return Inertia::render('CRM/Announcements/Index');
        })->name('crm.announcements');
    
    /*
    |--------------------------------------------------------------------------
    | Applications Routes
    */
        Route::get('/applications', function () {
            return Inertia::render('CRM/Applications/Index');
        })->name('crm.applications');
    
    /*
    |--------------------------------------------------------------------------
    | Documents Routes
    */
        Route::get('/documents', function () {
            return Inertia::render('CRM/Documents/Index');
        })->name('crm.documents');
    
    /*
    |--------------------------------------------------------------------------
    | Internal News Routes
    */
        Route::get('/internal-news', function () {
            return Inertia::render('CRM/InternalNews/Index');
        })->name('crm.internal-news');
    
    /*
    |--------------------------------------------------------------------------
    | Lead Fronts Routes
    */
    Route::get('/lead-fronts/export/{selectedRowsData}', [LeadFrontController::class, 'exportToExcel'])->name('lead-fronts.export');
    Route::get('/lead-fronts/leads-list', [LeadFrontController::class, 'getLeadList'])->name('lead-fronts.getLeadList');
    Route::get('/lead-fronts/lead/{id}', [LeadFrontController::class, 'getLeadDetails'])->name('lead-fronts.getLeadDetails');
    Route::get('/lead-fronts/{id}/lead', [LeadFrontController::class, 'getLead'])->name('lead-fronts.getLead');
    Route::get('/lead-fronts/{id}/lead-front-log-entries', [LeadFrontController::class, 'getLeadFrontLogEntries'])->name('lead-fronts.getLeadFrontLogEntries');
    // Route::get('/lead-fronts/{id}/lead-front-changelogs', [LeadFrontController::class, 'getLeadFrontChangelogs'])->name('lead-fronts.getLeadFrontChangelogs');
    Route::get('/lead-fronts/count', [LeadFrontController::class, 'getTotalLeadFrontCount'])->name('lead-fronts.getTotalLeadFrontCount');
    Route::resource('/lead-fronts', LeadFrontController::class);
    
    /*
    |--------------------------------------------------------------------------
    | Lead Uploads Routes
    */
        Route::get('/lead-uploads', function () {
            return Inertia::render('CRM/LeadUploads/Index');
        })->name('crm.lead-uploads');
        
    /*
    |--------------------------------------------------------------------------
    | Leads Routes
    */
    Route::get('/leads/lead-appointment-labels', [LeadController::class, 'getAllLeadAppointmentLabels'])->name('leads.getAllLeadAppointmentLabels');
    Route::get('/leads/lead-contact-outcomes', [LeadController::class, 'getAllLeadContactOutcomes'])->name('leads.getAllLeadContactOutcomes');
    Route::get('/leads/lead-stages', [LeadController::class, 'getAllLeadStages'])->name('leads.getAllLeadStages');
    Route::post('/leads/import', [LeadController::class, 'importExcel'])->name('leads.import');
    Route::get('/leads/export/{selectedRowsData}', [LeadController::class, 'exportToExcel'])->name('leads.export');
    Route::get('/leads/{id}/lead-front', [LeadController::class, 'getLeadFront'])->name('leads.getLeadFront');
    Route::get('/leads/{id}/lead-notes', [LeadController::class, 'getLeadNotes'])->name('leads.getLeadNotes');
    Route::get('/leads/{id}/lead-log-entries', [LeadController::class, 'getLeadLogEntries'])->name('leads.getLeadLogEntries');
    // Route::get('/leads/{id}/lead-changelogs', [LeadController::class, 'getLeadChangelogs'])->name('leads.getLeadChangelogs');
    Route::get('/leads/{id}/lead-notes-changelogs', [LeadController::class, 'getLeadNotesAndChangelogs'])->name('leads.getLeadNotesAndChangelogs');
    Route::get('/leads/count', [LeadController::class, 'getTotalLeadCount'])->name('leads.getTotalLeadCount');
    Route::delete('/leads/{id}/lead-front', [LeadController::class, 'deleteLeadFront'])->name('leads.deleteLeadFront');
    Route::delete('/leads/{id}/lead-note', [LeadController::class, 'deleteLeadNote'])->name('leads.deleteLeadNote');
    Route::delete('/leads/{id}/lead-duplicate', [LeadController::class, 'destroyDuplicate'])->name('leads.destroyDuplicate');
    Route::resource('/leads', LeadController::class);

    /*
    |--------------------------------------------------------------------------
    | Notifications Routes
    */
        Route::get('/notifications', function () {
            return Inertia::render('CRM/Notifications/Index');
        })->name('crm.notifications');

    /*
    |--------------------------------------------------------------------------
    | Orders Routes
    */
        Route::get('/orders/export/{selectedRowsData}', [OrderController::class, 'exportToExcel'])->name('orders.export');
        Route::get('/orders/generate-trade-id', [OrderController::class, 'generateTradeId'])->name('orders.generateTradeId');
        Route::get('/orders/{id}/order-log-entries', [OrderController::class, 'getOrderLogEntries'])->name('orders.getOrderLogEntries');
        // Route::get('/orders/{id}/orders-changelogs', [OrderController::class, 'getOrderChangelogs'])->name('orders.getOrderChangelogs');
        Route::get('/orders/count', [OrderController::class, 'getTotalOrderCount'])->name('orders.getTotalOrderCount');
        Route::put('/orders/{id}/destroy', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::resource('/orders', OrderController::class)->except([
            'destroy'
        ]);
        
    /*
    |--------------------------------------------------------------------------
    | Payment Methods Routes
    */
        Route::get('/payment-methods', function () {
            return Inertia::render('CRM/PaymentMethods/Index');
        })->name('crm.payment-methods');
        
    /*
    |--------------------------------------------------------------------------
    | Payment Submissions Routes
    */
        Route::get('/payment-submissions/count', [PaymentSubmissionController::class, 'getTotalPaymentSubmissionCount'])->name('payment-submissions.getTotalPaymentSubmissionCount');
        Route::resource('/payment-submissions', PaymentSubmissionController::class);

    /*
    |--------------------------------------------------------------------------
    | Private Message Routes
    */
        Route::get('/private-messages', function () {
            return Inertia::render('CRM/PrivateMessages/Index');
        })->name('crm.private-messages');

    /*
    |--------------------------------------------------------------------------
    | Sale Orders Routes
    */
        Route::get('/sale-orders/count', [SaleOrderController::class, 'getTotalSaleOrderCount'])->name('sale-orders.getTotalSaleOrderCount');
        Route::resource('/sale-orders', SaleOrderController::class);

    /*
    |--------------------------------------------------------------------------
    | Users / Clients Routes
    */
        Route::get('/users-clients/user-list', [UserClientController::class, 'getUserList'])->name('users-clients.getUserList');
        Route::get('/users-clients/account-managers', [UserClientController::class, 'getAccountManagers'])->name('users-clients.getAccountManagers');
        Route::get('/users-clients/{id}/orders', [UserClientController::class, 'getUserOrders'])->name('users-clients.getUserOrders');
        Route::get('/users-clients/sites', [UserClientController::class, 'getAllSites'])->name('users-clients.getAllSites');
        Route::get('/users-clients/export/{selectedRowsData}', [UserClientController::class, 'exportToExcel'])->name('users-clients.export');
        Route::get('/users-clients/generate-account-id', [UserClientController::class, 'generateAccountId'])->name('users-clients.generateAccountId');
        Route::get('/users-clients/{id}/users-clients-entries', [UserClientController::class, 'getUserLogEntries'])->name('users-clients.getUserLogEntries');
        // Route::get('/users-clients/{id}/users-clients-changelogs', [UserClientController::class, 'getUserChangelogs'])->name('users-clients.getUserChangelogs');
        Route::resource('/users-clients', UserClientController::class);
});

Route::middleware(['auth', 'verified'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Resources Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/resources', function () {
        return Inertia::render('Resources/Index');
    })->name('resources.index');
    
    /*
    |--------------------------------------------------------------------------
    | News Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/news', function () {
        return Inertia::render('News/Index');
    })->name('news.index');
    
    /*
    |--------------------------------------------------------------------------
    | Statistics Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/statistics', function () {
        return Inertia::render('Statistics/Index');
    })->name('statistics.index');
    
    /*
    |--------------------------------------------------------------------------
    | Performance Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/performance', function () {
        return Inertia::render('Performance/Index');
    })->name('performance.index');
    
    /*
    |--------------------------------------------------------------------------
    | Board Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/board', function () {
        return Inertia::render('Board/Index');
    })->name('board.index');
    
    /*
    |--------------------------------------------------------------------------
    | Logs Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/logs', function () {
        return Inertia::render('Logs/Index');
    })->name('logs.index');
    
    /*
    |--------------------------------------------------------------------------
    | Instant Message Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/instant-message', function () {
        return Inertia::render('InstantMessage/Index');
    })->name('instant-message.index');

});

Route::get('/components/buttons', function () {
    return Inertia::render('Components/Buttons');
})->middleware(['auth', 'verified'])->name('components.buttons');

require __DIR__ . '/auth.php';
