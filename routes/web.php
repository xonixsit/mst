<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
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

// Legal pages (accessible to everyone)
Route::get('/legal/terms', function () {
    return inertia('Legal/Terms');
})->name('legal.terms');

Route::get('/legal/privacy', function () {
    return inertia('Legal/Privacy');
})->name('legal.privacy');

Route::get('/legal/disclaimer', function () {
    return inertia('Legal/Disclaimer');
})->name('legal.disclaimer');

// Redirect root to admin login
Route::get('/', function () {
    return redirect('/admin/login');
});

// Global login routes
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::post('/login', function () {
    return redirect('/admin/login');
});

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
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
    
    Route::post('/logout', [LoginController::class, 'logout'])->middleware(['auth', 'auth.session'])->name('logout');
});

// Client Authentication Routes
Route::prefix('client')->name('client.')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
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
    
    Route::post('/logout', [LoginController::class, 'logout'])->middleware(['auth', 'auth.session'])->name('logout');
});

// Admin routes
Route::middleware(['auth', 'auth.session', 'session.timeout', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin dashboard
    Route::get('/dashboard', function () {
        // Current month stats
        $currentClients = \App\Models\Client::count();
        $currentRevenue = \App\Models\Invoice::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total_amount');
        $currentReturns = \App\Models\Document::where('document_type', 'tax_returns')
            ->where('status', 'approved')
            ->count();
        $currentEfficiency = round((\App\Models\Document::where('status', 'approved')->count() / 
            max(\App\Models\Document::count(), 1)) * 100, 1);

        // Previous month stats for comparison
        $lastMonth = now()->subMonth();
        $previousClients = \App\Models\Client::whereDate('created_at', '<', now()->startOfMonth())->count();
        $previousRevenue = \App\Models\Invoice::where('status', 'paid')
            ->whereMonth('paid_at', $lastMonth->month)
            ->whereYear('paid_at', $lastMonth->year)
            ->sum('total_amount');
        
        // Previous season returns (3 months ago)
        $previousSeasonReturns = \App\Models\Document::where('document_type', 'tax_returns')
            ->where('status', 'approved')
            ->whereDate('created_at', '<', now()->subMonths(3))
            ->count();
        
        // Previous efficiency (last month)
        $previousEfficiency = round((\App\Models\Document::where('status', 'approved')
            ->whereDate('created_at', '<', now()->startOfMonth())->count() / 
            max(\App\Models\Document::whereDate('created_at', '<', now()->startOfMonth())->count(), 1)) * 100, 1);

        // Calculate percentage changes
        $clientsChange = $previousClients > 0 ? round((($currentClients - $previousClients) / $previousClients) * 100, 1) : 0;
        $revenueChange = $previousRevenue > 0 ? round((($currentRevenue - $previousRevenue) / $previousRevenue) * 100, 1) : 0;
        $returnsChange = $previousSeasonReturns > 0 ? round((($currentReturns - $previousSeasonReturns) / $previousSeasonReturns) * 100, 1) : 0;
        $efficiencyChange = $previousEfficiency > 0 ? round($currentEfficiency - $previousEfficiency, 1) : 0;

        // Client status distribution (based on actual data)
        $activeReturns = \App\Models\Document::where('document_type', 'tax_returns')
            ->where('status', 'pending')
            ->distinct('client_id')
            ->count();
        $inReview = \App\Models\Document::where('status', 'under_review')
            ->distinct('client_id')
            ->count();
        $completed = \App\Models\Document::where('document_type', 'tax_returns')
            ->where('status', 'approved')
            ->distinct('client_id')
            ->count();
        $pendingDocs = \App\Models\Client::whereDoesntHave('documents')
            ->orWhereHas('documents', function($query) {
                $query->where('status', 'rejected');
            })
            ->count();
        $newClients = \App\Models\Client::whereDate('created_at', '>=', now()->subDays(30))->count();

        $stats = [
            'totalClients' => $currentClients,
            'monthlyRevenue' => $currentRevenue,
            'returnsFiled' => $currentReturns,
            'efficiencyScore' => $currentEfficiency,
            'changes' => [
                'clients' => $clientsChange,
                'revenue' => $revenueChange,
                'returns' => $returnsChange,
                'efficiency' => $efficiencyChange
            ],
            'clientStatus' => [
                'activeReturns' => $activeReturns,
                'inReview' => $inReview,
                'completed' => $completed,
                'pendingDocs' => $pendingDocs,
                'newClients' => $newClients
            ]
        ];

        return inertia('Admin/Dashboard', [
            'stats' => $stats
        ]);
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
        
        // Split the name into first_name and last_name
        $nameParts = explode(' ', trim($validated['name']), 2);
        $updateData = [
            'first_name' => $nameParts[0],
            'last_name' => isset($nameParts[1]) ? $nameParts[1] : '',
            'email' => $validated['email']
        ];
        
        auth()->user()->update($updateData);
        
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
    Route::get('/documents/{document}/download', [App\Http\Controllers\Admin\DocumentController::class, 'download'])->name('documents.download');
    Route::patch('/documents/{document}/status', [App\Http\Controllers\Admin\DocumentController::class, 'updateStatus'])->name('documents.update-status');
    Route::delete('/documents/{document}', [App\Http\Controllers\Admin\DocumentController::class, 'destroy'])->name('documents.destroy');
    
    // Admin invoice management routes
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');
    Route::post('invoices/{invoice}/send-email', [InvoiceController::class, 'sendEmail'])->name('invoices.send-email');
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');

    // Admin tax professional management routes
    Route::resource('tax-professionals', App\Http\Controllers\Admin\TaxProfessionalController::class)->names('tax-professionals')->parameters(['tax-professionals' => 'taxProfessional']);
    Route::post('tax-professionals/{taxProfessional}/restore', [App\Http\Controllers\Admin\TaxProfessionalController::class, 'restore'])->name('tax-professionals.restore');

    // Admin reports routes
    Route::get('reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/stats', [App\Http\Controllers\Admin\ReportController::class, 'stats'])->name('reports.stats');
    Route::get('reports/client-summary', [App\Http\Controllers\Admin\ReportController::class, 'clientSummary'])->name('reports.client-summary');
    Route::get('reports/financial', [App\Http\Controllers\Admin\ReportController::class, 'financial'])->name('reports.financial');
    Route::get('reports/document-activity', [App\Http\Controllers\Admin\ReportController::class, 'documentActivity'])->name('reports.document-activity');
    Route::get('reports/communication', [App\Http\Controllers\Admin\ReportController::class, 'communication'])->name('reports.communication');
    
    // Secure client-specific routes (using route parameters instead of query strings)
    Route::get('clients/{userId}/documents', [App\Http\Controllers\Admin\DocumentController::class, 'clientDocuments'])->name('clients.documents');
    Route::get('clients/{userId}/invoices', [InvoiceController::class, 'clientInvoices'])->name('clients.invoices');
    Route::get('clients/{userId}/invoices/create', [InvoiceController::class, 'createForClient'])->name('clients.invoices.create');
    
    // Admin message management routes
    Route::get('messages', [App\Http\Controllers\Admin\MessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{message}', [App\Http\Controllers\Admin\MessageController::class, 'show'])->name('messages.show');
    Route::post('messages', [App\Http\Controllers\Admin\MessageController::class, 'store'])->name('messages.store');
    Route::post('messages/{message}/reply', [App\Http\Controllers\Admin\MessageController::class, 'reply'])->name('messages.reply');
    Route::post('messages/{message}/mark-read', [App\Http\Controllers\Admin\MessageController::class, 'markAsRead'])->name('messages.mark-read');
    Route::post('messages/bulk-action', [App\Http\Controllers\Admin\MessageController::class, 'bulkAction'])->name('messages.bulk-action');
    Route::delete('messages/{message}', [App\Http\Controllers\Admin\MessageController::class, 'destroy'])->name('messages.destroy');
    
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
        $user = auth()->user();
        $client = $user->client;
        
        return inertia('Client/Profile', [
            'clientData' => $client ? [
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $client->phone,
            ] : [
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => null,
            ],
            'communicationPreferences' => $client ? [
                'email_notifications_enabled' => $client->email_notifications_enabled ?? true,
                'sms_notifications_enabled' => $client->sms_notifications_enabled ?? false,
                'preferred_communication_method' => $client->preferred_communication_method ?? 'email',
                'communication_preferences' => $client->communication_preferences ?? [
                    'email_notifications' => true,
                    'document_notifications' => true,
                    'invoice_notifications' => true,
                    'reminder_notifications' => true,
                    'notification_frequency' => 'immediate'
                ]
            ] : []
        ]);
    })->name('profile');

    Route::put('/profile', function (Request $request) {
        $user = auth()->user();
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
        ]);

        // Update user name fields
        $user->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    })->name('profile.update');

    
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
    
    // Notification management routes
    Route::get('/notifications', [App\Http\Controllers\Client\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-read', [App\Http\Controllers\Client\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\Client\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [App\Http\Controllers\Client\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [App\Http\Controllers\Client\NotificationController::class, 'destroyAll'])->name('notifications.destroy-all');
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