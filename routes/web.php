<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerUserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectItemController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\RequestReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IsAdmin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cesty pro zákaznické uživatele v rámci firmy
    Route::get('/customers/{customer}/users', [CustomerUserController::class, 'customerUsers'])->name('customers.users');

    // Cesty pro projekty v rámci zákazníka
    Route::get('/customers/{customer}/projects', [ProjectController::class, 'customerProjects'])->name('customers.projects');

    // Cesty pro projektové položky v rámci projektu
    Route::get('/projects/{project}/items', [ProjectItemController::class, 'projectItems'])->name('projects.items');

    // Cesty pro požadavky v rámci projektové položky
    Route::get('/project-items/{projectItem}/requests', [RequestController::class, 'projectItemRequests'])->name('project_items.requests');

    // Cesty pro seznam požadavků - přístupné pro všechny přihlášené
    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');

    // Admin routes - chráněné přímou instancí IsAdmin middleware
    Route::middleware(IsAdmin::class)->group(function () {
        // CRUD pro firmy
        Route::resource('customers', CustomerController::class);

        // CRUD pro projekty
        Route::resource('projects', ProjectController::class);

        // Cesty pro zákaznické uživatele vyžadující admin práva
        // DŮLEŽITÉ: create musí být před {customerUser} cestami
        Route::get('/customer-users/create', [CustomerUserController::class, 'create'])->name('customer_users.create');
        Route::post('/customer-users', [CustomerUserController::class, 'store'])->name('customer_users.store');
        Route::get('/customer-users/{customerUser}/edit', [CustomerUserController::class, 'edit'])->name('customer_users.edit');
        Route::put('/customer-users/{customerUser}', [CustomerUserController::class, 'update'])->name('customer_users.update');
        Route::delete('/customer-users/{customerUser}', [CustomerUserController::class, 'destroy'])->name('customer_users.destroy');

        // Cesty pro projektové položky vyžadující admin práva
        // DŮLEŽITÉ: specifické cesty musí být před cestami s parametry
        Route::get('/project-items/create', [ProjectItemController::class, 'create'])->name('project_items.create');
        Route::post('/project-items', [ProjectItemController::class, 'store'])->name('project_items.store');

        // Přiřazení uživatelů k projektovým položkám
        Route::post('/project-items/{projectItem}/assign-user', [ProjectItemController::class, 'assignUser'])->name('project_items.assign.user');
        Route::delete('/project-items/{projectItem}/users/{user}', [ProjectItemController::class, 'removeUser'])->name('project_items.remove.user');

        // Přiřazení zákaznických uživatelů k projektovým položkám
        Route::post('/project-items/{projectItem}/assign-customer-user', [ProjectItemController::class, 'assignCustomerUser'])->name('project_items.assign.customer-user');
        Route::delete('/project-items/{projectItem}/customer-users/{customerUser}', [ProjectItemController::class, 'removeCustomerUser'])->name('project_items.remove.customer-user');

        // Editace a mazání projektových položek - musí být po specifických cestách
        Route::get('/project-items/{projectItem}/edit', [ProjectItemController::class, 'edit'])->name('project_items.edit');
        Route::put('/project-items/{projectItem}', [ProjectItemController::class, 'update'])->name('project_items.update');
        Route::delete('/project-items/{projectItem}', [ProjectItemController::class, 'destroy'])->name('project_items.destroy');

        // Cesty pro požadavky vyžadující admin práva
        // DŮLEŽITÉ: specifické cesty musí být před cestami s parametry
        Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
        Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
        Route::get('/requests/{request}/edit', [RequestController::class, 'edit'])->name('requests.edit');
        Route::put('/requests/{request}', [RequestController::class, 'update'])->name('requests.update');

        // Cesty pro reporty práce - všechny operace jsou omezeny na adminy
        Route::get('/request-reports/create', [RequestReportController::class, 'create'])->name('request-reports.create');
        Route::post('/request-reports', [RequestReportController::class, 'store'])->name('request-reports.store');
        Route::get('/request-reports/{requestReport}', [RequestReportController::class, 'show'])->name('request-reports.show');
        Route::get('/request-reports/{requestReport}/edit', [RequestReportController::class, 'edit'])->name('request-reports.edit');
        Route::put('/request-reports/{requestReport}', [RequestReportController::class, 'update'])->name('request-reports.update');
        Route::delete('/request-reports/{requestReport}', [RequestReportController::class, 'destroy'])->name('request-reports.destroy');
    });

    // Cesty pro zákaznické uživatele dostupné pro všechny přihlášené
    Route::get('/customer-users', [CustomerUserController::class, 'index'])->name('customer_users.index');
    Route::get('/customer-users/{customerUser}', [CustomerUserController::class, 'show'])->name('customer_users.show');

    // Přidání zprávy k požadavku a změna stavu
    Route::post('/requests/{request}/messages', [RequestController::class, 'addMessage'])->name('requests.add-message');
    Route::patch('/requests/{request}/state', [RequestController::class, 'changeState'])->name('requests.change-state');

    // Cesty pro seznam a detail projektových položek - přístupné pro všechny přihlášené
    // DŮLEŽITÉ: tyto cesty musí být až na konci, aby nezachytily 'create' a další specifické cesty
    Route::get('/project-items', [ProjectItemController::class, 'index'])->name('project_items.index');
    Route::get('/project-items/{projectItem}', [ProjectItemController::class, 'show'])->name('project_items.show');

    // DŮLEŽITÉ: tato cesta musí být úplně na konci, aby nezachytila cestu '/requests/create'
    Route::get('/requests/{request}', [RequestController::class, 'show'])->name('requests.show');
});





Route::prefix('customer')->group(function () {
    // Guest routes (pro nepřihlášené)
    Route::middleware('guest:customer')->group(function () {
        Route::get('login', [App\Http\Controllers\Customer\Auth\CustomerAuthController::class, 'create'])->name('customer.login');
        Route::post('login', [App\Http\Controllers\Customer\Auth\CustomerAuthController::class, 'store']);
    });

    // Autentizované routes (pro přihlášené zákazníky)
    Route::middleware(['auth:customer', 'auth.customer'])->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Customer\DashboardController::class, 'index'])->name('customer.dashboard');
        Route::post('logout', [App\Http\Controllers\Customer\Auth\CustomerAuthController::class, 'destroy'])->name('customer.logout');

        // Profil
        Route::get('profile', [App\Http\Controllers\Customer\ProfileController::class, 'edit'])->name('customer.profile');
        Route::patch('profile', [App\Http\Controllers\Customer\ProfileController::class, 'update'])->name('customer.profile.update');
        Route::delete('profile', [App\Http\Controllers\Customer\ProfileController::class, 'destroy'])->name('customer.profile.destroy');

        // Požadavky
        Route::get('requests', [App\Http\Controllers\Customer\RequestController::class, 'index'])->name('customer.requests.index');
        Route::get('requests/create/{id_projectitem?}', [App\Http\Controllers\Customer\RequestController::class, 'create'])->name('customer.requests.create');
        Route::post('requests', [App\Http\Controllers\Customer\RequestController::class, 'store'])->name('customer.requests.store');
        Route::get('requests/{request}', [App\Http\Controllers\Customer\RequestController::class, 'show'])->name('customer.requests.show');
        Route::post('requests/{request}/messages', [App\Http\Controllers\Customer\RequestController::class, 'addMessage'])->name('customer.requests.add-message');
        Route::patch('requests/{request}/confirm-resolution', [App\Http\Controllers\Customer\RequestController::class, 'confirmResolution'])->name('customer.requests.confirm-resolution');

        // Projekty
        Route::get('projects', [App\Http\Controllers\Customer\ProjectController::class, 'index'])->name('customer.projects.index');
        Route::get('projects/{project}', [App\Http\Controllers\Customer\ProjectController::class, 'show'])->name('customer.projects.show');

        Route::post('requests/{id}/messages', [App\Http\Controllers\Customer\RequestController::class, 'addMessage'])
        ->name('customer.requests.add-message');

        Route::get('project-items/{projectItem}', [App\Http\Controllers\Customer\ProjectItemController::class, 'show'])
        ->name('customer.project_items.show')
        ->middleware(['auth:customer', 'auth.customer']);
    });
});


require __DIR__.'/auth.php';

