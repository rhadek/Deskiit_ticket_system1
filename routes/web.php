<?php

use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProjectItemController;
use App\Http\Controllers\CustomerUserController;
use App\Http\Controllers\RequestOfferController;
use App\Http\Controllers\RequestReportController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\ProjectPasswordController;





Route::middleware(['auth:web,customer'])->group(function () {
    Route::post('/media', [MediaController::class, 'store'])->name('media.store');

    Route::get('/media/{media}/download', [MediaController::class, 'download'])->name('media.download');
    Route::get('/media/{media}/show', [MediaController::class, 'show'])->name('media.show');

    Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
});


Route::prefix('api')->group(function () {
    Route::get('/requests/{id}/name', [App\Http\Controllers\Api\TimeTrackerController::class, 'getRequestName']);
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [UserDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/customers/{customer}/users', [CustomerUserController::class, 'customerUsers'])->name('customers.users');

    Route::get('/customers/{customer}/projects', [ProjectController::class, 'customerProjects'])->name('customers.projects');

    Route::get('/projects/{project}/items', [ProjectItemController::class, 'projectItems'])->name('projects.items');

    Route::get('/project-items/{projectItem}/requests', function(App\Models\ProjectItem $projectItem) {
        return redirect()->route('requests.create', ['id_projectitem' => $projectItem->id]);
    })->name('project_items.requests');

    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');

    Route::get('/request-reports/create', [RequestReportController::class, 'create'])->name('request-reports.create');
    Route::post('/request-reports', [RequestReportController::class, 'store'])->name('request-reports.store');
    Route::get('/request-reports/{requestReport}', [RequestReportController::class, 'show'])->name('request-reports.show');
    Route::get('/request-reports/{requestReport}/edit', [RequestReportController::class, 'edit'])->name('request-reports.edit');
    Route::put('/request-reports/{requestReport}', [RequestReportController::class, 'update'])->name('request-reports.update');
    Route::delete('/request-reports/{requestReport}', [RequestReportController::class, 'destroy'])->name('request-reports.destroy');
    Route::middleware(IsAdmin::class)->group(function () {
        Route::resource('customers', CustomerController::class)->except(['show']);

        Route::resource('projects', ProjectController::class)->except(['show']);
    });
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

    Route::middleware(IsAdmin::class)->group(function () {

        Route::get('/customer-users/create', [CustomerUserController::class, 'create'])->name('customer_users.create');
        Route::post('/customer-users', [CustomerUserController::class, 'store'])->name('customer_users.store');
        Route::get('/customer-users/{customerUser}/edit', [CustomerUserController::class, 'edit'])->name('customer_users.edit');
        Route::put('/customer-users/{customerUser}', [CustomerUserController::class, 'update'])->name('customer_users.update');
        Route::delete('/customer-users/{customerUser}', [CustomerUserController::class, 'destroy'])->name('customer_users.destroy');


        Route::get('/project-items/create', [ProjectItemController::class, 'create'])->name('project_items.create');
        Route::post('/project-items', [ProjectItemController::class, 'store'])->name('project_items.store');

        Route::post('/project-items/{projectItem}/assign-user', [ProjectItemController::class, 'assignUser'])->name('project_items.assign.user');
        Route::delete('/project-items/{projectItem}/users/{user}', [ProjectItemController::class, 'removeUser'])->name('project_items.remove.user');

        Route::post('/project-items/{projectItem}/assign-customer-user', [ProjectItemController::class, 'assignCustomerUser'])->name('project_items.assign.customer-user');
        Route::delete('/project-items/{projectItem}/customer-users/{customerUser}', [ProjectItemController::class, 'removeCustomerUser'])->name('project_items.remove.customer-user');

        Route::get('/project-passwords/create', [ProjectPasswordController::class, 'create'])->name('project_passwords.create');
        Route::post('/project-passwords', [ProjectPasswordController::class, 'store'])->name('project_passwords.store');
        Route::get('/project-passwords/{projectPassword}/edit', [ProjectPasswordController::class, 'edit'])->name('project_passwords.edit');
        Route::put('/project-passwords/{projectPassword}', [ProjectPasswordController::class, 'update'])->name('project_passwords.update');
        Route::delete('/project-passwords/{projectPassword}', [ProjectPasswordController::class, 'destroy'])->name('project_passwords.destroy');

        Route::get('/project-priorities/create/{projectId}', [App\Http\Controllers\ProjectPriorityController::class, 'create'])->name('project_priorities.create');
        Route::post('/project-priorities', [App\Http\Controllers\ProjectPriorityController::class, 'store'])->name('project_priorities.store');
        Route::get('/project-priorities/{projectPriority}/edit', [App\Http\Controllers\ProjectPriorityController::class, 'edit'])->name('project_priorities.edit');
        Route::put('/project-priorities/{projectPriority}', [App\Http\Controllers\ProjectPriorityController::class, 'update'])->name('project_priorities.update');
        Route::delete('/project-priorities/{projectPriority}', [App\Http\Controllers\ProjectPriorityController::class, 'destroy'])->name('project_priorities.destroy');
        Route::get('/project-priorities/{projectPriority}', [App\Http\Controllers\ProjectPriorityController::class, 'show'])->name('project_priorities.show');
        Route::get('/project-priorities/getProjectPrioritiesByProjectId/{id}', [App\Http\Controllers\ProjectPriorityController::class, 'getProjectPrioritiesByProjectId'])->name('project_priorities.getProjectPrioritiesByProjectId');
        Route::get('/project-priorities', [App\Http\Controllers\ProjectPriorityController::class, 'index'])->name('project_priorities.index');

        Route::get('request-offers/create', [RequestOfferController::class, 'create'])->name('offers.create');
        Route::post('request-offers', [RequestOfferController::class, 'store'])->name('offers.store');

        Route::get('request-offers/{requestOffer}/edit', [RequestOfferController::class, 'edit'])->name('offers.edit');
        Route::put('request-offers/{requestOffer}', [RequestOfferController::class, 'update'])->name('offers.update');
        Route::delete('request-offers/{requestOffer}', [RequestOfferController::class, 'destroy'])->name('offers.destroy');
        Route::get('request-offers/{requestOffer}/preview', [RequestOfferController::class, 'preview'])->name('offers.preview');
        Route::get('request-offers/{requestOffer}/download', [RequestOfferController::class, 'download'])->name('offers.download');


        Route::get('/project-items/{projectItem}/edit', [ProjectItemController::class, 'edit'])->name('project_items.edit');
        Route::put('/project-items/{projectItem}', [ProjectItemController::class, 'update'])->name('project_items.update');
        Route::delete('/project-items/{projectItem}', [ProjectItemController::class, 'destroy'])->name('project_items.destroy');

        Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
        Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
        Route::get('/requests/{request}/edit', [RequestController::class, 'edit'])->name('requests.edit');
        Route::put('/requests/{request}', [RequestController::class, 'update'])->name('requests.update');

    });

    Route::get('/customer-users', [CustomerUserController::class, 'index'])->name('customer_users.index');
    Route::get('/customer-users/{customerUser}', [CustomerUserController::class, 'show'])->name('customer_users.show');

    Route::post('/requests/{request}/messages', [RequestController::class, 'addMessage'])->name('requests.add-message');
    Route::patch('/requests/{request}/state', [RequestController::class, 'changeState'])->name('requests.change-state');

    Route::get('/project-items', [ProjectItemController::class, 'index'])->name('project_items.index');
    Route::get('/project-items/{projectItem}', [ProjectItemController::class, 'show'])->name('project_items.show');

    Route::get('/requests/{request}', [RequestController::class, 'show'])->name('requests.show');
});

Route::prefix('customer')->group(function () {

    Route::middleware('guest:customer')->group(function () {
        Route::get('login', [App\Http\Controllers\Customer\Auth\CustomerAuthController::class, 'create'])->name('customer.login');
        Route::post('login', [App\Http\Controllers\Customer\Auth\CustomerAuthController::class, 'store']);
    });

    Route::middleware(['auth:customer', 'auth.customer'])->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Customer\DashboardController::class, 'index'])->name('customer.dashboard');
        Route::post('logout', [App\Http\Controllers\Customer\Auth\CustomerAuthController::class, 'destroy'])->name('customer.logout');

        Route::get('profile', [App\Http\Controllers\Customer\ProfileController::class, 'edit'])->name('customer.profile');
        Route::patch('profile', [App\Http\Controllers\Customer\ProfileController::class, 'update'])->name('customer.profile.update');
        Route::delete('profile', [App\Http\Controllers\Customer\ProfileController::class, 'destroy'])->name('customer.profile.destroy');

        Route::get('requests/create/{id_projectitem?}', [App\Http\Controllers\Customer\RequestController::class, 'create'])->name('customer.requests.create');
        Route::post('requests', [App\Http\Controllers\Customer\RequestController::class, 'store'])->name('customer.requests.store');
        Route::post('requests/{id}/messages', [App\Http\Controllers\Customer\RequestController::class, 'addMessage'])->name('customer.requests.add-message');

        Route::post('requests/{request}/confirm-resolution', [App\Http\Controllers\Customer\RequestController::class, 'confirmResolution'])
            ->name('customer.requests.confirm-resolution');

        Route::get('requests', [App\Http\Controllers\Customer\RequestController::class, 'index'])->name('customer.requests.index');
        Route::get('requests/{request}', [App\Http\Controllers\Customer\RequestController::class, 'show'])->name('customer.requests.show');

        Route::group(['middleware' => function ($request, $next) {
            if (Auth::guard('customer')->user()->kind != 3) {
                abort(403, 'Nemáte oprávnění pro přístup k této funkci.');
            }
            return $next($request);
        }], function () {
            Route::get('projects/create', [App\Http\Controllers\Customer\ProjectController::class, 'create'])
                ->name('customer.projects.create');
            Route::post('projects', [App\Http\Controllers\Customer\ProjectController::class, 'store'])
                ->name('customer.projects.store');
            Route::get('projects/{project}/edit', [App\Http\Controllers\Customer\ProjectController::class, 'edit'])
                ->name('customer.projects.edit');
            Route::put('projects/{project}', [App\Http\Controllers\Customer\ProjectController::class, 'update'])
                ->name('customer.projects.update');
            Route::delete('projects/{project}', [App\Http\Controllers\Customer\ProjectController::class, 'destroy'])
                ->name('customer.projects.destroy');

            Route::get('project-items/create', [App\Http\Controllers\Customer\ProjectItemController::class, 'create'])
                ->name('customer.project_items.create');
            Route::post('project-items', [App\Http\Controllers\Customer\ProjectItemController::class, 'store'])
                ->name('customer.project_items.store');
            Route::get('project-items/{projectItem}/edit', [App\Http\Controllers\Customer\ProjectItemController::class, 'edit'])
                ->name('customer.project_items.edit');
            Route::put('project-items/{projectItem}', [App\Http\Controllers\Customer\ProjectItemController::class, 'update'])
                ->name('customer.project_items.update');
            Route::delete('project-items/{projectItem}', [App\Http\Controllers\Customer\ProjectItemController::class, 'destroy'])
                ->name('customer.project_items.destroy');

            Route::get('project-items/{projectItem}/assign-users', [App\Http\Controllers\Customer\ProjectItemController::class, 'assignUsers'])
                ->name('customer.project_items.assign_users');
            Route::post('project-items/{projectItem}/assign-users', [App\Http\Controllers\Customer\ProjectItemController::class, 'storeAssignments'])
                ->name('customer.project_items.store_assignments');
        });

        Route::get('projects', [App\Http\Controllers\Customer\ProjectController::class, 'index'])
            ->name('customer.projects.index');
        Route::get('projects/{project}', [App\Http\Controllers\Customer\ProjectController::class, 'show'])
            ->name('customer.projects.show');

        Route::get('project-items/{projectItem}', [App\Http\Controllers\Customer\ProjectItemController::class, 'show'])
            ->name('customer.project_items.show');
    });
});

require __DIR__ . '/auth.php';
