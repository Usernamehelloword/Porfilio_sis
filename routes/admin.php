<?php

use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AppearanceController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DesignerController;
use App\Http\Controllers\Admin\HomepageController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    // ------------------------------------------------------------ guest
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.attempt');
    });

    Route::post('logout', [AuthController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');

    // ------------------------------------------------------------ authenticated
    Route::middleware(['auth', 'active.admin'])->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Projects
        Route::post('projects/reorder', [ProjectController::class, 'reorder'])->name('projects.reorder');
        Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::post('projects/{project}/duplicate', [ProjectController::class, 'duplicate'])->name('projects.duplicate');
        Route::post('projects/{project}/toggle-publish', [ProjectController::class, 'togglePublish'])->name('projects.toggle-publish');
        Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

        // Project gallery / drawings
        Route::post('projects/{project}/images', [ProjectController::class, 'uploadImages'])->name('projects.images.store');
        Route::post('projects/{project}/images/reorder', [ProjectController::class, 'reorderImages'])->name('projects.images.reorder');
        Route::put('projects/{project}/images/{image}', [ProjectController::class, 'updateImage'])->name('projects.images.update');
        Route::post('projects/{project}/images/{image}/cover', [ProjectController::class, 'setCover'])->name('projects.images.cover');
        Route::delete('projects/{project}/images/{image}', [ProjectController::class, 'destroyImage'])->name('projects.images.destroy');

        // Categories
        Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);
        Route::post('categories/{category}/toggle-publish', [CategoryController::class, 'togglePublish'])->name('categories.toggle-publish');

        // Designers
        Route::post('designers/reorder', [DesignerController::class, 'reorder'])->name('designers.reorder');
        Route::resource('designers', DesignerController::class)->except(['show']);
        Route::post('designers/{designer}/toggle-publish', [DesignerController::class, 'togglePublish'])->name('designers.toggle-publish');

        // Services
        Route::post('services/reorder', [ServiceController::class, 'reorder'])->name('services.reorder');
        Route::resource('services', ServiceController::class)->except(['show']);
        Route::post('services/{service}/toggle-publish', [ServiceController::class, 'togglePublish'])->name('services.toggle-publish');

        // Journal
        Route::resource('journal', ArticleController::class)->except(['show'])->parameters([
            'journal' => 'article',
        ]);
        Route::post('journal/{article}/toggle-publish', [ArticleController::class, 'togglePublish'])->name('journal.toggle-publish');

        // Media
        Route::get('media', [MediaController::class, 'index'])->name('media.index');
        Route::post('media', [MediaController::class, 'store'])->name('media.store');
        Route::post('media/{item}/replace', [MediaController::class, 'replace'])->name('media.replace');
        Route::delete('media/{item}', [MediaController::class, 'destroy'])->name('media.destroy');

        // Messages
        Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [MessageController::class, 'show'])->name('messages.show');
        Route::post('messages/{message}/read', [MessageController::class, 'markRead'])->name('messages.read');
        Route::post('messages/{message}/replied', [MessageController::class, 'markReplied'])->name('messages.replied');
        Route::post('messages/{message}/archive', [MessageController::class, 'archive'])->name('messages.archive');
        Route::delete('messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

        // Homepage
        Route::get('homepage', [HomepageController::class, 'index'])->name('homepage');
        Route::post('homepage/hero', [HomepageController::class, 'updateHero'])->name('homepage.hero');
        Route::post('homepage/featured', [HomepageController::class, 'addFeatured'])->name('homepage.featured.add');
        Route::post('homepage/featured/reorder', [HomepageController::class, 'reorderFeatured'])->name('homepage.featured.reorder');
        Route::post('homepage/featured/{project}/remove', [HomepageController::class, 'removeFeatured'])->name('homepage.featured.remove');
        Route::post('homepage/featured/{project}/toggle-publish', [HomepageController::class, 'togglePublish'])->name('homepage.featured.toggle-publish');

        // About page
        Route::get('about', [AboutController::class, 'index'])->name('about');
        Route::post('about', [AboutController::class, 'update'])->name('about.update');

        // Appearance & settings (super admin + editor only)
        Route::middleware('role:super_admin,editor')->group(function () {
            Route::get('appearance', [AppearanceController::class, 'index'])->name('appearance');
            Route::post('appearance', [AppearanceController::class, 'update'])->name('appearance.update');
            Route::get('settings', [SettingController::class, 'index'])->name('settings');
            Route::post('settings/general', [SettingController::class, 'updateGeneral'])->name('settings.general');
            Route::post('settings/seo', [SettingController::class, 'updateSeo'])->name('settings.seo');
        });

        // Admin users (super admin only)
        Route::middleware('role:super_admin')->group(function () {
            Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
            Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
            Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
            Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        });
    });
});
