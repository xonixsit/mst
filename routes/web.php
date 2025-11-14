<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// Test route
Route::get('/test', function () {
    return 'Test route works!';
});

// Redirect root to admin login
Route::get('/', function () {
    return redirect('/admin/login');
});

// Global login route
Route::get('/login', function () {
    return redirect('/admin/login');
});

Route::post('/login', [LoginController::class, 'login'])->name('login');

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Temporarily remove guest middleware to test
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::middleware('guest')->group(function () {
        Route::post('/login', [LoginController::class, 'login']);
        Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [RegisterController::class, 'register']);
        Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
        
        // Email verification routes
        Route::get('/email/verify', [App\Http\Controllers\Auth\VerificationController::class, 'show'])->name('verification.notice');
        Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])->name('verification.verify');
        Route::post('/email/verification-notification', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])->name('verification.send');
    });
    
    Route::post('/logout', [LoginController::class, 'logout'])->middleware(['auth', 'auth.session'])->name('logout');
});

// Client Authentication Routes
Route::prefix('client')->name('client.')->group(function () {
    // Temporarily remove guest middleware to test
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::middleware('guest')->group(function () {
        Route::post('/login', [LoginController::class, 'login']);
        Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [RegisterController::class, 'register']);
        Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
        
        // Email verification routes
        Route::get('/email/verify', [App\Http\Controllers\Auth\VerificationController::class, 'show'])->name('verification.notice');
        Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])->name('verification.verify');
        Route::post('/email/verification-notification', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])->name('verification.send');
    });
    
    Route::post('/logout', [LoginController::class, 'logout'])->middleware(['auth', 'auth.session'])->name('logout');
});

// Admin routes
Route::middleware(['auth', 'auth.session', 'session.timeout', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin dashboard
    Route::get('/dashboard', function () {
        return inertia('Admin/Dashboard');
    })->name('dashboard');
    
    // Admin settings and notifications
    Route::get('/settings', function () {
        return inertia('Settings');
    })->name('settings');
    
    Route::post('/settings', function () {
        return back()->with('success', 'Settings saved successfully.');
    });
    
    Route::get('/notifications', function () {
        return inertia('Notifications');
    })->name('notifications');
    
    Route::patch('/notifications/{id}/toggle-read', function ($id) {
        return back();
    });
    
    Route::post('/notifications/mark-all-read', function () {
        return back()->with('success', 'All notifications marked as read.');
    });
    
    Route::delete('/notifications/clear-all', function () {
        return back()->with('success', 'All notifications cleared.');
    });
    
    // Admin profile
    Route::get('/profile', function () {
        return inertia('Admin/Profile', [
            'stats' => [
                'totalClients' => \App\Models\Client::count(),
                'activeClients' => \App\Models\Client::where('status', 'active')->count(),
                'completedReturns' => 0 // Add logic for completed returns
            ]
        ]);
    })->name('profile');
    
    Route::patch('/profile', function () {
        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20'
        ]);
        
        auth()->user()->update($validated);
        
        return back()->with('success', 'Profile updated successfully.');
    });
    
    Route::put('/password', function () {
        $validated = request()->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed'
        ]);
        
        auth()->user()->update([
            'password' => bcrypt($validated['password'])
        ]);
        
        return back()->with('success', 'Password updated successfully.');
    });
    
    // Client management routes
    Route::resource('clients', ClientController::class);
    Route::post('clients/bulk-operation', [ClientController::class, 'bulkOperation'])->name('clients.bulk-operation');
    Route::get('clients/export', [ClientController::class, 'export'])->name('clients.export');
    Route::get('clients/stats', [ClientController::class, 'getStats'])->name('clients.stats');
    
    // Client status and assignment management
    Route::patch('clients/{client}/status', [ClientController::class, 'updateStatus'])->name('clients.update-status');
    Route::patch('clients/{client}/assign', [ClientController::class, 'assignUser'])->name('clients.assign-user');
    
    // Client audit and history
    Route::get('clients/{client}/audit-logs', [ClientController::class, 'auditLogs'])->name('clients.audit-logs');
    Route::get('clients/{client}/history', [ClientController::class, 'interactionHistory'])->name('clients.history');
    Route::get('clients/{client}/financial-summary', [ClientController::class, 'financialSummary'])->name('clients.financial-summary');
    Route::get('clients/{client}/document-summary', [DocumentController::class, 'clientDocumentSummary'])->name('clients.document-summary');
    Route::get('clients/{client}/communication-summary', [ClientController::class, 'communicationSummary'])->name('clients.communication-summary');
    Route::patch('clients/{client}/communication-preferences', [ClientController::class, 'updateCommunicationPreferences'])->name('clients.update-communication-preferences');
    
    // Additional client-related routes
    Route::get('clients/{client}/tax-docs', [ClientController::class, 'taxDocs'])->name('clients.tax-docs');
    Route::get('clients/{client}/invoices', [ClientController::class, 'invoices'])->name('clients.invoices');
    
    // Client archival routes
    Route::patch('clients/{client}/archive', [ClientController::class, 'archive'])->name('clients.archive');
    Route::patch('clients/{client}/restore', [ClientController::class, 'restore'])->name('clients.restore');
    Route::get('clients-archived', [ClientController::class, 'archived'])->name('clients.archived');
    
    // Client retention policy routes
    Route::get('clients/retention-stats', [ClientController::class, 'retentionStats'])->name('clients.retention-stats');
    Route::post('clients/execute-retention-policy', [ClientController::class, 'executeRetentionPolicy'])->name('clients.execute-retention-policy');
    
    // CSV import routes
    Route::get('clients/import-csv', [ClientController::class, 'importCsv'])->name('clients.import-csv');
    Route::post('clients/process-csv-import', [ClientController::class, 'processCsvImport'])->name('clients.process-csv-import');
    Route::get('clients/download-csv-template', [ClientController::class, 'downloadCsvTemplate'])->name('clients.download-csv-template');
    Route::post('clients/csv-import-stats', [ClientController::class, 'getCsvImportStats'])->name('clients.csv-import-stats');
    
    // Comprehensive export routes
    Route::get('clients/export-interface', [ClientController::class, 'exportInterface'])->name('clients.export-interface');
    Route::post('clients/process-comprehensive-export', [ClientController::class, 'processComprehensiveExport'])->name('clients.process-comprehensive-export');
    Route::post('clients/export-stats', [ClientController::class, 'getExportStats'])->name('clients.export-stats');
    Route::post('clients/schedule-automated-export', [ClientController::class, 'scheduleAutomatedExport'])->name('clients.schedule-automated-export');
    
    // Admin document management routes
    Route::get('/documents', [App\Http\Controllers\Admin\DocumentController::class, 'index'])->name('documents');
    Route::get('/documents/{document}', [App\Http\Controllers\Admin\DocumentController::class, 'show'])->name('documents.show');
    Route::patch('/documents/{document}/status', [App\Http\Controllers\Admin\DocumentController::class, 'updateStatus'])->name('documents.update-status');
    Route::delete('/documents/{document}', [App\Http\Controllers\Admin\DocumentController::class, 'destroy'])->name('documents.destroy');
    
    // Admin invoice management routes
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');
    Route::post('invoices/{invoice}/send-email', [InvoiceController::class, 'sendEmail'])->name('invoices.send-email');
    
    // Admin audit management routes
    Route::get('audit', [App\Http\Controllers\Admin\AuditController::class, 'index'])->name('audit.index');
    Route::get('audit/{auditLog}', [App\Http\Controllers\Admin\AuditController::class, 'show'])->name('audit.show');
    Route::get('audit-export', [App\Http\Controllers\Admin\AuditController::class, 'export'])->name('audit.export');
    Route::get('audit-compliance-report', [App\Http\Controllers\Admin\AuditController::class, 'complianceReport'])->name('audit.compliance-report');
    Route::post('audit-cleanup', [App\Http\Controllers\Admin\AuditController::class, 'cleanup'])->name('audit.cleanup');
    
    // Admin data archival and retention routes
    Route::get('data-archival', [App\Http\Controllers\Admin\DataArchivalController::class, 'index'])->name('data-archival.index');
    Route::get('data-archival/archived-clients', [App\Http\Controllers\Admin\DataArchivalController::class, 'archivedClients'])->name('data-archival.archived-clients');
    Route::get('data-archival/retention-policy', [App\Http\Controllers\Admin\DataArchivalController::class, 'retentionPolicy'])->name('data-archival.retention-policy');
    Route::post('data-archival/archive-inactive-clients', [App\Http\Controllers\Admin\DataArchivalController::class, 'archiveInactiveClients'])->name('data-archival.archive-inactive-clients');
    Route::post('data-archival/restore-client/{client}', [App\Http\Controllers\Admin\DataArchivalController::class, 'restoreClient'])->name('data-archival.restore-client');
    Route::post('data-archival/permanently-delete-archived-data', [App\Http\Controllers\Admin\DataArchivalController::class, 'permanentlyDeleteArchivedData'])->name('data-archival.permanently-delete-archived-data');
    Route::post('data-archival/update-retention-policy', [App\Http\Controllers\Admin\DataArchivalController::class, 'updateRetentionPolicy'])->name('data-archival.update-retention-policy');
    Route::post('data-archival/execute-retention-policy', [App\Http\Controllers\Admin\DataArchivalController::class, 'executeRetentionPolicy'])->name('data-archival.execute-retention-policy');
});

// Client routes
Route::middleware(['auth', 'auth.session', 'session.timeout', 'client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', function () {
        return inertia('Client/Dashboard');
    })->name('dashboard');
    
    // Client settings and notifications
    Route::get('/settings', function () {
        return inertia('Settings');
    })->name('settings');
    
    Route::post('/settings', function () {
        return back()->with('success', 'Settings saved successfully.');
    });
    
    Route::get('/notifications', function () {
        return inertia('Notifications');
    })->name('notifications');
    
    Route::patch('/notifications/{id}/toggle-read', function ($id) {
        return back();
    });
    
    Route::post('/notifications/mark-all-read', function () {
        return back()->with('success', 'All notifications marked as read.');
    });
    
    Route::delete('/notifications/clear-all', function () {
        return back()->with('success', 'All notifications cleared.');
    });
    
    Route::get('/profile', function () {
        return inertia('Client/Profile');
    })->name('profile');
    
    Route::patch('/profile', function () {
        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20'
        ]);
        
        auth()->user()->update($validated);
        
        return back()->with('success', 'Profile updated successfully.');
    });
    
    Route::put('/password', function () {
        $validated = request()->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed'
        ]);
        
        auth()->user()->update([
            'password' => bcrypt($validated['password'])
        ]);
        
        return back()->with('success', 'Password updated successfully.');
    });
    
    // Document management routes
    Route::get('/documents', [App\Http\Controllers\Client\DocumentController::class, 'index'])->name('documents');
    Route::post('/documents', [App\Http\Controllers\Client\DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}/download', [App\Http\Controllers\Client\DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [App\Http\Controllers\Client\DocumentController::class, 'destroy'])->name('documents.destroy');
    
    // Message management routes
    Route::get('/messages', [App\Http\Controllers\Client\MessageController::class, 'index'])->name('messages');
    Route::post('/messages', [App\Http\Controllers\Client\MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [App\Http\Controllers\Client\MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{message}/reply', [App\Http\Controllers\Client\MessageController::class, 'reply'])->name('messages.reply');
    Route::patch('/messages/{message}/read', [App\Http\Controllers\Client\MessageController::class, 'markAsRead'])->name('messages.read');
    
    // Client information management routes
    Route::get('/information', [App\Http\Controllers\Client\InformationController::class, 'index'])->name('information');
    Route::post('/information', [App\Http\Controllers\Client\InformationController::class, 'store'])->name('information.store');
    Route::post('/information/auto-save', [App\Http\Controllers\Client\InformationController::class, 'autoSave'])->name('information.auto-save');
    Route::get('/information/export', [App\Http\Controllers\Client\InformationController::class, 'export'])->name('information.export');
    Route::post('/information/request-review', [App\Http\Controllers\Client\InformationController::class, 'requestReview'])->name('information.request-review');
    Route::post('/information/mark-section-complete', [App\Http\Controllers\Client\InformationController::class, 'markSectionComplete'])->name('information.mark-section-complete');
    Route::get('/information/review-status', [App\Http\Controllers\Client\InformationController::class, 'getReviewStatus'])->name('information.review-status');
    
    // Communication preferences routes
    Route::patch('/communication-preferences', [App\Http\Controllers\Client\CommunicationController::class, 'updatePreferences'])->name('communication.update-preferences');
    Route::get('/communication-preferences', [App\Http\Controllers\Client\CommunicationController::class, 'getPreferences'])->name('communication.get-preferences');
    Route::get('/communication-history', [App\Http\Controllers\Client\CommunicationController::class, 'getHistory'])->name('communication.history');
    
    // Tax returns routes
    Route::get('/tax-returns', function () {
        return inertia('Client/TaxReturns');
    })->name('tax-returns');
    
    // Invoice routes
    Route::get('/invoices', [App\Http\Controllers\Client\InvoiceController::class, 'index'])->name('invoices');
    Route::get('/invoices/{invoice}', [App\Http\Controllers\Client\InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/download', [App\Http\Controllers\Client\InvoiceController::class, 'download'])->name('invoices.download');

});