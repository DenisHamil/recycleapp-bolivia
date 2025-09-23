<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\RegisterDonorController;
use App\Http\Controllers\Auth\RegisterCollectorController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\RewardController;
use App\Http\Controllers\Admin\MapController;
use App\Http\Controllers\Donor\DashboardController;
use App\Http\Controllers\Donor\DonorProfileController;
use App\Http\Controllers\Donor\DonorPointsController;
use App\Http\Controllers\Donor\DonationController;
use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Controllers\Collector\CollectorDonationMapController;
use App\Http\Controllers\Collector\CollectorProfileController;
use App\Http\Controllers\Proposals\RecyclerProposalController;
use App\Http\Controllers\Admin\RewardCategoryController;
use App\Http\Controllers\Admin\CommerceController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\RankingController; // ✅

Route::get('/', fn() => view('landing'))->name('landing');

// Registro
Route::get('/registro', fn() => view('auth.choose-register'))->name('register.choose');
Route::get('/registro/donador', fn() => view('auth.register.donor'))->name('register.donor');
Route::post('/register/donor', [RegisterDonorController::class, 'store'])->name('register.donor.store');

Route::get('/registro/recolector', [RegisterCollectorController::class, 'showForm'])->name('register.collector');
Route::post('/register/collector', [RegisterCollectorController::class, 'store'])->name('register.collector.store');

// Ubicación
Route::get('/locations/departments', [LocationController::class, 'getDepartments']);
Route::get('/locations/provinces', [LocationController::class, 'getProvinces']);
Route::get('/locations/municipalities', [LocationController::class, 'getMunicipalities']);

// Login / Logout
Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
})->name('logout');

// 🔒 Dashboard Donador
Route::prefix('donor')->group(function () {
    Route::get('/dashboard', function () {
        if (!Auth::check() || Auth::user()->role !== 'donor') {
            return redirect()->route('login');
        }
        return app(DashboardController::class)->index();
    })->name('donor.dashboard');

    Route::get('/donaciones/crear', function () {
        if (!Auth::check() || Auth::user()->role !== 'donor') {
            return redirect()->route('login');
        }
        return app(DonationController::class)->create();
    })->name('donor.donations.create');
    Route::post('/donaciones', [DonationController::class, 'store'])->name('donor.donations.store');

    Route::get('/tienda', [DonorPointsController::class, 'show'])
        ->middleware(['auth'])
        ->name('donor.store');

    Route::post('/rewards/{reward}/redeem', [DonorPointsController::class, 'redeem'])
        ->middleware(['auth'])
        ->name('donor.rewards.redeem');

    Route::get('/perfil', [DonorProfileController::class, 'edit'])->name('donor.profile.edit');
    Route::post('/perfil', [DonorProfileController::class, 'update'])->name('donor.profile.update');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('donor.notifications.index');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('donor.notifications.show');

    Route::post('/proposals/{id}/accept', [RecyclerProposalController::class, 'accept'])->name('donor.proposals.accept');

    Route::get('/ranking', function () {
        if (!Auth::check() || Auth::user()->role !== 'donor') {
            return redirect()->route('login');
        }
        return app(RankingController::class)->donor(request());
    })->name('donor.ranking');

    // 📜 HISTORIAL DONADOR
    Route::get('/historial', function () {
        if (!Auth::check() || Auth::user()->role !== 'donor') {
            return redirect()->route('login');
        }
        return app(HistoryController::class)->index(request());
    })->name('donor.history');

    // Cancelar donación (con nota)
    Route::post('/donaciones/{id}/cancelar', function ($id) {
        if (!Auth::check() || Auth::user()->role !== 'donor') {
            return redirect()->route('login');
        }
        return app(HistoryController::class)->donorCancel(request(), $id);
    })->name('donor.donations.cancel');

    // ⭐ Valorar recolector (usa RankingController)
    Route::post('/ratings', function () {
        if (!Auth::check() || Auth::user()->role !== 'donor') {
            return redirect()->route('login');
        }
        return app(RankingController::class)->storeByDonor(request());
    })->name('donor.ratings.store');
});

// 🔒 Dashboard Recolector
Route::prefix('collector')->group(function () {
    Route::get('/dashboard', function () {
        if (!Auth::check() || Auth::user()->role !== 'collector') {
            return redirect()->route('login');
        }
        return view('collector.dashboard');
    })->name('collector.dashboard');

    Route::get('/donaciones/mapa', [CollectorDonationMapController::class, 'index'])->name('collector.donations.map');
    Route::get('/donaciones/mapa/datos', [CollectorDonationMapController::class, 'mapData'])->name('collector.donations.map.data');
    Route::get('/donaciones', fn() => redirect()->route('collector.donations.map'))->name('collector.donations');

    Route::get('/perfil', [CollectorProfileController::class, 'edit'])->name('collector.profile.edit');
    Route::post('/perfil', [CollectorProfileController::class, 'update'])->name('collector.profile.update');

    Route::get('/notificaciones', [NotificationController::class, 'index'])->name('collector.notifications.index');
    Route::get('/notificaciones/{id}', [NotificationController::class, 'show'])->name('collector.notifications.show');

    Route::post('/proposals/store', [RecyclerProposalController::class, 'store'])->name('collector.proposals.store');
    Route::post('/proposals/{id}/complete', [RecyclerProposalController::class, 'complete'])->name('collector.proposals.complete');

    Route::get('/ranking', function () {
        if (!Auth::check() || Auth::user()->role !== 'collector') {
            return redirect()->route('login');
        }
        return app(RankingController::class)->collector(request());
    })->name('collector.ranking');

    // 📜 HISTORIAL RECOLECTOR
    Route::get('/historial', function () {
        if (!Auth::check() || Auth::user()->role !== 'collector') {
            return redirect()->route('login');
        }
        return app(HistoryController::class)->index(request());
    })->name('collector.history');

    // Cancelar recolección (con nota) — incluye alias adicional para evitar error
    Route::post('/donaciones/{id}/cancelar', function ($id) {
        if (!Auth::check() || Auth::user()->role !== 'collector') {
            return redirect()->route('login');
        }
        return app(HistoryController::class)->collectorCancel(request(), $id);
    })->name('collector.donations.cancel');

    // Alias adicional para evitar error por nombre no encontrado
    Route::post('/donaciones/{id}/cancelar', function ($id) {
        if (!Auth::check() || Auth::user()->role !== 'collector') {
            return redirect()->route('login');
        }
        return app(HistoryController::class)->collectorCancel(request(), $id);
    })->name('collector.history.cancel');

    // ⭐ Valorar donador (usa RankingController)
    Route::post('/ratings', function () {
        if (!Auth::check() || Auth::user()->role !== 'collector') {
            return redirect()->route('login');
        }
        return app(RankingController::class)->storeByCollector(request());
    })->name('collector.ratings.store');
});


// 🔒 Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {

        if (app()->environment('local')) {
            return view('admin.dashboard');
        }

        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals');
    Route::post('/approvals/{collector}/accept', [ApprovalController::class, 'accept'])->name('approvals.accept');
    Route::post('/approvals/{collector}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.delete');

    Route::post('/subcategories', [SubcategoryController::class, 'store'])->name('subcategories.store');
    Route::delete('/subcategories/{id}', [SubcategoryController::class, 'destroy'])->name('subcategories.delete');

    Route::get('/map', [MapController::class, 'index'])->name('map');
    Route::get('/reports', fn() => view('admin.modules.reports'))->name('reports');
    Route::get('/feedback', fn() => view('admin.modules.feedback'))->name('feedback');

    Route::resource('/rewards', RewardController::class)->names('rewards');

    // 📜 Historial de recompensas (archivadas)
    Route::get('/rewards/history', [RewardController::class, 'history'])->name('rewards.history');
    Route::patch('/rewards/{id}/restore', [RewardController::class, 'restore'])->name('rewards.restore');

    Route::resource('/reward-categories', RewardCategoryController::class)
        ->names('reward-categories')
        ->parameters(['reward-categories' => 'reward_category']);

    Route::resource('/commerces', CommerceController::class)
        ->names('commerces');

    // 📬 Notificaciones (Admin)
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::get('/notifications/{id}', [NotificationController::class, 'show'])
        ->name('notifications.show');

    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.mark-as-read');

    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])
        ->name('notifications.mark-all-read');

    Route::patch('/notifications/{id}/status', [NotificationController::class, 'updateStatus'])
        ->name('notifications.update-status');
});
