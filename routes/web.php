<?php

use App\Http\Controllers\Admin\ArticleAdminController;
use App\Http\Controllers\Admin\CaseStudyAdminController;
use App\Http\Controllers\Admin\ContactMessageAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqAdminController;
use App\Http\Controllers\Admin\JobPostingAdminController;
use App\Http\Controllers\Admin\LegalPageAdminController;
use App\Http\Controllers\Admin\MarketingContentAdminController;
use App\Http\Controllers\Admin\MediaAdminController;
use App\Http\Controllers\Admin\ServiceAdminController;
use App\Http\Controllers\Admin\SiteSettingAdminController;
use App\Http\Controllers\Admin\TestimonialAdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\AboutController;
use App\Http\Controllers\Web\BlogController;
use App\Http\Controllers\Web\CareerController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\FaqController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\LegalPageController;
use App\Http\Controllers\Web\PortfolioController;
use App\Http\Controllers\Web\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', [AboutController::class, 'show'])->name('about');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/portfolio/{slug}', [PortfolioController::class, 'show'])->name('portfolio.show');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('contact.store');
Route::get('/careers', [CareerController::class, 'index'])->name('careers');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');
Route::get('/privacy-policy', [LegalPageController::class, 'show'])->defaults('slug', 'privacy-policy')->name('privacy');
Route::get('/terms-and-conditions', [LegalPageController::class, 'show'])->defaults('slug', 'terms-and-conditions')->name('terms');
Route::get('/legal/{slug}', [LegalPageController::class, 'show'])->name('legal.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware(['auth', 'blade.admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/site-settings', [SiteSettingAdminController::class, 'edit'])->name('site-settings.edit');
    Route::put('/site-settings', [SiteSettingAdminController::class, 'update'])->name('site-settings.update');

    Route::get('/marketing-content', [MarketingContentAdminController::class, 'edit'])->name('marketing-content.edit');
    Route::put('/marketing-content', [MarketingContentAdminController::class, 'update'])->name('marketing-content.update');

    Route::resource('services', ServiceAdminController::class)->except(['show']);
    Route::resource('testimonials', TestimonialAdminController::class)->except(['show']);
    Route::resource('faqs', FaqAdminController::class)->except(['show']);
    Route::resource('articles', ArticleAdminController::class)->except(['show']);
    Route::resource('case-studies', CaseStudyAdminController::class)->except(['show']);
    Route::resource('legal-pages', LegalPageAdminController::class)->except(['show']);
    Route::resource('job-postings', JobPostingAdminController::class)->except(['show']);

    Route::get('/contact-messages', [ContactMessageAdminController::class, 'index'])->name('contact-messages.index');
    Route::get('/contact-messages/{contact_message}', [ContactMessageAdminController::class, 'show'])->name('contact-messages.show');
    Route::delete('/contact-messages/{contact_message}', [ContactMessageAdminController::class, 'destroy'])->name('contact-messages.destroy');
    Route::post('/contact-messages/{contact_message}/read', [ContactMessageAdminController::class, 'markRead'])->name('contact-messages.read');

    Route::get('/media', [MediaAdminController::class, 'index'])->name('media.index');
    Route::post('/media', [MediaAdminController::class, 'store'])->name('media.store');
    Route::delete('/media/{media_asset}', [MediaAdminController::class, 'destroy'])->name('media.destroy');
});
