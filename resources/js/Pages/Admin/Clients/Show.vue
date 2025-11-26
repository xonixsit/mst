<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">{{ client.user?.first_name && client.user?.last_name 
              ? `${client.user.first_name} ${client.user.last_name}`.trim() 
              : 'Unknown Client' }}</h1>
          <p class="mt-1 text-sm text-gray-600">Client ID: {{ client.id }} • Registered: {{ formatDate(client.created_at) }}</p>
        </div>
        <div class="flex space-x-2">
          <button
            @click="router.visit(`/admin/clients/${client.id}`)"
            class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded text-sm flex items-center"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            View
          </button>
          <button
            @click="router.visit(`/admin/clients/${client.id}/edit`)"
            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm flex items-center"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit
          </button>
          <button
            @click="viewDocuments"
            class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm flex items-center"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Docs
          </button>
          <button
            @click="viewInvoices"
            class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-2 rounded text-sm flex items-center"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            Invoice
          </button>
          <button
            @click="showDeleteModal = true"
            class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm flex items-center"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            Delete
          </button>
        </div>
      </div>
    </template>

    <div class="admin-client-show">
    <!-- Status and Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 rounded-lg" :class="getStatusBgClass(client.status)">
            <svg class="w-6 h-6" :class="getStatusTextClass(client.status)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Status</p>
            <p class="text-lg font-bold text-gray-900 capitalize">{{ client.status }}</p>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 bg-blue-100 rounded-lg">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Projects</p>
            <p class="text-lg font-bold text-gray-900">{{ client.projects?.length || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 bg-green-100 rounded-lg">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Assets</p>
            <p class="text-lg font-bold text-gray-900">{{ client.assets?.length || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 bg-red-100 rounded-lg">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Expenses</p>
            <p class="text-lg font-bold text-gray-900">{{ client.expenses?.length || 0 }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Client Information Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Personal Information -->
      <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
        </div>
        <div class="p-6">
          <dl class="grid grid-cols-1 gap-4">
            <div>
              <dt class="text-sm font-medium text-gray-500">Full Name</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ client.user?.first_name && client.user?.last_name 
                  ? `${client.user.first_name} ${client.user.middle_name || ''} ${client.user.last_name}`.replace(/\s+/g, ' ').trim() 
                  : 'Unknown Client' }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Email</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ client.user?.email || 'No email' }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Phone</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ client.phone }}</dd>
            </div>
            <div v-if="client.mobile_number">
              <dt class="text-sm font-medium text-gray-500">Mobile</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ client.mobile_number }}</dd>
            </div>
            <div v-if="client.date_of_birth">
              <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ formatDate(client.date_of_birth) }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Marital Status</dt>
              <dd class="mt-1 text-sm text-gray-900 capitalize">{{ client.marital_status }}</dd>
            </div>
            <div v-if="client.occupation">
              <dt class="text-sm font-medium text-gray-500">Occupation</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ client.occupation }}</dd>
            </div>
            <div v-if="client.visa_status">
              <dt class="text-sm font-medium text-gray-500">Visa Status</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ client.visa_status }}</dd>
            </div>
          </dl>
        </div>
      </div>

      <!-- Address Information -->
      <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Address Information</h3>
        </div>
        <div class="p-6">
          <dl class="grid grid-cols-1 gap-4">
            <div>
              <dt class="text-sm font-medium text-gray-500">Street Address</dt>
              <dd class="mt-1 text-sm text-gray-900">
                {{ client.street_no }}{{ client.apartment_no ? ', ' + client.apartment_no : '' }}
              </dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">City</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ client.city }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">State</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ client.state }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Zip Code</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ client.zip_code }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Country</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ client.country }}</dd>
            </div>
          </dl>
        </div>
      </div>
    </div>

    <!-- Spouse Information -->
    <div v-if="client.spouse" class="mt-6 bg-white rounded-lg shadow">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Spouse Information</h3>
      </div>
      <div class="p-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <dt class="text-sm font-medium text-gray-500">Name</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ client.spouse.first_name }} {{ client.spouse.last_name }}</dd>
          </div>
          <div v-if="client.spouse.email">
            <dt class="text-sm font-medium text-gray-500">Email</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ client.spouse.email }}</dd>
          </div>
          <div v-if="client.spouse.phone">
            <dt class="text-sm font-medium text-gray-500">Phone</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ client.spouse.phone }}</dd>
          </div>
          <div v-if="client.spouse.occupation">
            <dt class="text-sm font-medium text-gray-500">Occupation</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ client.spouse.occupation }}</dd>
          </div>
        </dl>
      </div>
    </div>

    <!-- Projects Section -->
    <div v-if="client.projects && client.projects.length > 0" class="mt-6 bg-white rounded-lg shadow">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Projects</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="project in client.projects" :key="project.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ project.project_name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ project.project_type }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="getProjectStatusClass(project.status)">
                  {{ project.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ project.due_date ? formatDate(project.due_date) : 'N/A' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Assets Section -->
    <div v-if="client.assets && client.assets.length > 0" class="mt-6 bg-white rounded-lg shadow">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Assets</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business %</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="asset in client.assets" :key="asset.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ asset.asset_name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatDate(asset.date_of_purchase) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ formatCurrency(asset.cost_of_acquisition) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ asset.percentage_used_in_business }}%</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Expenses Section -->
    <div v-if="client.expenses && client.expenses.length > 0" class="mt-6 bg-white rounded-lg shadow">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Expenses</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Particulars</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax Payer</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="expense in client.expenses" :key="expense.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 capitalize">{{ expense.category }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ expense.particulars }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ formatCurrency(expense.amount) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ expense.tax_payer }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Financial Summary Section -->
    <div class="mt-6 bg-white rounded-lg shadow">
      <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-medium text-gray-900">Financial Summary</h3>
          <button
            @click="loadFinancialSummary"
            class="text-blue-600 hover:text-blue-800 text-sm"
          >
            Refresh
          </button>
        </div>
      </div>
      <div class="p-6">
        <div v-if="financialSummary" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-blue-50 rounded-lg p-4">
            <div class="flex items-center">
              <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-blue-600">Total Assets</p>
                <p class="text-lg font-bold text-blue-900">${{ formatCurrency(financialSummary.total_asset_value || 0) }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-green-50 rounded-lg p-4">
            <div class="flex items-center">
              <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-green-600">Business Assets</p>
                <p class="text-lg font-bold text-green-900">${{ formatCurrency(financialSummary.total_business_asset_value || 0) }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-red-50 rounded-lg p-4">
            <div class="flex items-center">
              <div class="p-2 bg-red-100 rounded-lg">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-red-600">Total Expenses</p>
                <p class="text-lg font-bold text-red-900">${{ formatCurrency(financialSummary.total_expense_amount || 0) }}</p>
              </div>
            </div>
          </div>
          
          <div class="bg-purple-50 rounded-lg p-4">
            <div class="flex items-center">
              <div class="p-2 bg-purple-100 rounded-lg">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-purple-600">Net Worth</p>
                <p class="text-lg font-bold text-purple-900">
                  ${{ formatCurrency((financialSummary.total_asset_value || 0) - (financialSummary.total_expense_amount || 0)) }}
                </p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Expense Categories -->
        <div v-if="financialSummary && financialSummary.expense_categories && financialSummary.expense_categories.length > 0" class="mt-6">
          <h4 class="text-md font-medium text-gray-900 mb-3">Expense Categories</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div
              v-for="category in financialSummary.expense_categories"
              :key="category.category"
              class="flex justify-between items-center p-3 bg-gray-50 rounded-lg"
            >
              <div>
                <p class="text-sm font-medium text-gray-900 capitalize">{{ category.category }}</p>
                <p class="text-xs text-gray-600">{{ category.count }} items</p>
              </div>
              <p class="text-sm font-bold text-gray-900">${{ formatCurrency(category.total_amount) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Document Summary Section -->
    <div class="mt-6 bg-white rounded-lg shadow">
      <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-medium text-gray-900">Document Summary</h3>
          <div class="flex space-x-2">
            <button
              @click="loadDocumentSummary"
              class="text-blue-600 hover:text-blue-800 text-sm"
            >
              Refresh
            </button>
            <button
              @click="viewDocuments"
              class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm"
            >
              View All Documents
            </button>
          </div>
        </div>
      </div>
      <div class="p-6">
        <div v-if="documentSummary" class="space-y-6">
          <!-- Document Statistics -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
              <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-blue-600">Total Documents</p>
                  <p class="text-lg font-bold text-blue-900">{{ documentSummary.total_documents }}</p>
                </div>
              </div>
            </div>
            
            <div class="bg-yellow-50 rounded-lg p-4">
              <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                  <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-yellow-600">Pending Review</p>
                  <p class="text-lg font-bold text-yellow-900">{{ documentSummary.documents_by_status?.pending || 0 }}</p>
                </div>
              </div>
            </div>
            
            <div class="bg-green-50 rounded-lg p-4">
              <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-green-600">Approved</p>
                  <p class="text-lg font-bold text-green-900">{{ documentSummary.documents_by_status?.approved || 0 }}</p>
                </div>
              </div>
            </div>
            
            <div class="bg-purple-50 rounded-lg p-4">
              <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                  <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-purple-600">Completion</p>
                  <p class="text-lg font-bold text-purple-900">{{ Math.round(documentSummary.document_completion_percentage || 0) }}%</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Missing Documents Alert -->
          <div v-if="documentSummary.missing_document_types && documentSummary.missing_document_types.length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
              </div>
              <div class="ml-3">
                <h4 class="text-sm font-medium text-red-800">Missing Required Documents</h4>
                <div class="mt-2 text-sm text-red-700">
                  <p>The following document types are recommended for this client:</p>
                  <ul class="list-disc list-inside mt-1">
                    <li v-for="type in documentSummary.missing_document_types" :key="type" class="capitalize">
                      {{ type.replace('_', ' ') }}
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Documents -->
          <div v-if="documentSummary.recent_documents && documentSummary.recent_documents.length > 0">
            <h4 class="text-md font-medium text-gray-900 mb-3">Recent Documents</h4>
            <div class="bg-gray-50 rounded-lg overflow-hidden">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Document</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Uploaded</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="document in documentSummary.recent_documents" :key="document.id">
                    <td class="px-3 py-2 text-sm text-gray-900">{{ document.name }}</td>
                    <td class="px-3 py-2 text-sm text-gray-900 capitalize">{{ document.document_type.replace('_', ' ') }}</td>
                    <td class="px-3 py-2 text-sm">
                      <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="getDocumentStatusClass(document.status)">
                        {{ document.status }}
                      </span>
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-900">{{ formatDate(document.created_at) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        
        <div v-else class="text-center py-8">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <p class="mt-2 text-sm text-gray-600">No document summary available</p>
        </div>
      </div>
    </div>

    <!-- Interaction History Section -->
    <div class="mt-6 bg-white rounded-lg shadow">
      <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-medium text-gray-900">Interaction History</h3>
          <button
            @click="loadInteractionHistory"
            class="text-blue-600 hover:text-blue-800 text-sm"
          >
            Refresh
          </button>
        </div>
      </div>
      <div class="p-6">
        <div v-if="interactionHistory && interactionHistory.length > 0" class="space-y-4 max-h-96 overflow-y-auto">
          <div
            v-for="interaction in interactionHistory"
            :key="interaction.id"
            class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg"
          >
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-900">{{ interaction.description }}</p>
                <p class="text-xs text-gray-500">{{ formatDateTime(interaction.created_at) }}</p>
              </div>
              <p class="text-xs text-gray-600 mt-1">by {{ interaction.user }}</p>
              <div v-if="interaction.changes && Object.keys(interaction.changes).length > 0" class="mt-2">
                <p class="text-xs text-gray-600 font-medium">Changes:</p>
                <div class="mt-1 space-y-1">
                  <div
                    v-for="(change, field) in interaction.changes"
                    :key="field"
                    class="text-xs text-gray-600"
                  >
                    <span class="font-medium">{{ field }}:</span>
                    <span class="text-red-600">{{ change.old || 'null' }}</span>
                    →
                    <span class="text-green-600">{{ change.new || 'null' }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-8">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <p class="mt-2 text-sm text-gray-600">No interaction history available</p>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mt-2">Delete Client</h3>
          <div class="mt-2 px-7 py-3">
            <p class="text-sm text-gray-500">
              Are you sure you want to delete {{ client.user?.first_name && client.user?.last_name 
                  ? `${client.user.first_name} ${client.user.last_name}`.trim() 
                  : 'this client' }}? 
              This action cannot be undone and will permanently remove all client data, documents, and history.
            </p>
          </div>
          <div class="flex justify-center space-x-3 mt-4">
            <button
              @click="showDeleteModal = false"
              class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400"
            >
              Cancel
            </button>
            <button
              @click="deleteClient"
              :disabled="deleting"
              class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 disabled:opacity-50"
            >
              {{ deleting ? 'Deleting...' : 'Delete Client' }}
            </button>
          </div>
        </div>
      </div>
    </div>
    </div>
  </AppLayout>
</template>

<script>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  name: 'AdminClientShow',
  components: {
    AppLayout
  },
  props: {
    client: Object
  },
  setup(props) {
    const financialSummary = ref(null)
    const interactionHistory = ref([])
    const loading = ref(false)
    const showDeleteModal = ref(false)
    const deleting = ref(false)
    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
      })
    }

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(amount)
    }

    const getStatusBgClass = (status) => {
      const classes = {
        active: 'bg-green-100',
        inactive: 'bg-yellow-100',
        archived: 'bg-red-100'
      }
      return classes[status] || 'bg-gray-100'
    }

    const getStatusTextClass = (status) => {
      const classes = {
        active: 'text-green-600',
        inactive: 'text-yellow-600',
        archived: 'text-red-600'
      }
      return classes[status] || 'text-gray-600'
    }

    const getProjectStatusClass = (status) => {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        in_progress: 'bg-blue-100 text-blue-800',
        completed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    }

    const getDocumentStatusClass = (status) => {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    }

    const formatDateTime = (date) => {
      return new Date(date).toLocaleString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const loadFinancialSummary = async () => {
      try {
        loading.value = true
        const response = await fetch(`/admin/clients/${props.client.id}/financial-summary`)
        const data = await response.json()
        financialSummary.value = data.financial_summary
      } catch (error) {
        console.error('Failed to load financial summary:', error)
      } finally {
        loading.value = false
      }
    }

    const loadInteractionHistory = async () => {
      try {
        loading.value = true
        const response = await fetch(`/admin/clients/${props.client.id}/history`)
        const data = await response.json()
        interactionHistory.value = data.history
      } catch (error) {
        console.error('Failed to load interaction history:', error)
      } finally {
        loading.value = false
      }
    }

    const documentSummary = ref(null)
    
    const loadDocumentSummary = async () => {
      try {
        loading.value = true
        const response = await fetch(`/admin/clients/${props.client.id}/document-summary`)
        const data = await response.json()
        documentSummary.value = data.document_summary
      } catch (error) {
        console.error('Failed to load document summary:', error)
      } finally {
        loading.value = false
      }
    }

    const exportClient = () => {
      const params = new URLSearchParams({
        client_ids: [props.client.id],
        format: 'pdf'
      })
      
      window.open(`/admin/clients/export?${params}`, '_blank')
    }

    const viewDocuments = () => {
      router.visit(`/admin/clients/${props.client.user_id}/documents`)
    }

    const viewInvoices = () => {
      router.visit(`/admin/clients/${props.client.user_id}/invoices`)
    }

    const deleteClient = () => {
      deleting.value = true
      
      router.delete(`/admin/clients/${props.client.id}`, {
        onSuccess: () => {
          router.get('/admin/clients')
        },
        onFinish: () => {
          deleting.value = false
          showDeleteModal.value = false
        }
      })
    }

    // Load data on component mount
    onMounted(() => {
      loadFinancialSummary()
      loadInteractionHistory()
      loadDocumentSummary()
    })

    return {
      financialSummary,
      interactionHistory,
      documentSummary,
      loading,
      showDeleteModal,
      deleting,
      formatDate,
      formatDateTime,
      formatCurrency,
      getStatusBgClass,
      getStatusTextClass,
      getProjectStatusClass,
      getDocumentStatusClass,
      loadFinancialSummary,
      loadInteractionHistory,
      loadDocumentSummary,
      exportClient,
      viewDocuments,
      viewInvoices,
      deleteClient
    }
  }
}
</script>

<style scoped>
.admin-client-show {
  @apply p-6 bg-gray-50 min-h-screen;
}
</style>