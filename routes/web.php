<?php

use App\Http\Controllers\Admin\ActivityLogAdminController;
use App\Http\Controllers\Admin\AiAgentAdminController;
use App\Http\Controllers\Admin\AiArticleGeneratorAdminController;
use App\Http\Controllers\Admin\AiBuilderBusinessProfileAdminController;
use App\Http\Controllers\Admin\AiContentQuickGenerateController;
use App\Http\Controllers\Admin\AiConversationAdminController;
use App\Http\Controllers\Admin\AiProviderAdminController;
use App\Http\Controllers\Admin\ArticleAdminController;
use App\Http\Controllers\Admin\CaseStudyAdminController;
use App\Http\Controllers\Admin\ContactMessageAdminController;
use App\Http\Controllers\Admin\Crm\CrmContactController;
use App\Http\Controllers\Admin\Crm\CrmDashboardController;
use App\Http\Controllers\Admin\Crm\CrmFaqController;
use App\Http\Controllers\Admin\Crm\CrmProjectController;
use App\Http\Controllers\Admin\Crm\CrmReviewController;
use App\Http\Controllers\Admin\Crm\CrmSourceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqAdminController;
use App\Http\Controllers\Admin\JobPostingAdminController;
use App\Http\Controllers\Admin\LeadAdminController;
use App\Http\Controllers\Admin\LegalPageAdminController;
use App\Http\Controllers\Admin\HomepageAdminController;
use App\Http\Controllers\Admin\MarketingContentAdminController;
use App\Http\Controllers\Admin\MarketUpdateAdminController;
use App\Http\Controllers\Admin\MediaAdminController;
use App\Http\Controllers\Admin\NavItemAdminController;
use App\Http\Controllers\Admin\PortfolioItemAdminController;
use App\Http\Controllers\Admin\SecuritySettingsAdminController;
use App\Http\Controllers\Admin\SeoSettingAdminController;
use App\Http\Controllers\Admin\ServiceAdminController;
use App\Http\Controllers\Admin\SiteSettingAdminController;
use App\Http\Controllers\Admin\TestimonialAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Api\V1\Builder\BuilderAiController;
use App\Http\Controllers\Api\V1\Builder\BuilderPageController;
use App\Http\Controllers\Api\V1\Builder\BuilderSavedSectionController;
use App\Http\Controllers\Api\V1\Builder\BuilderTemplateController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\EndUserAuthController;
use App\Http\Controllers\Auth\MasterAuthController;
use App\Http\Controllers\Master\MasterDashboardController;
use App\Http\Controllers\Master\SecurityAccessControlController;
use App\Http\Controllers\Web\FilamentLegacyController;
use App\Http\Controllers\Web\PortalController;
use App\Http\Controllers\Web\AboutController;
use App\Http\Controllers\Web\ArticlePublicController;
use App\Http\Controllers\Web\BlogController;
use App\Http\Controllers\Web\CareerController;
use App\Http\Controllers\Web\CaseStudyPublicController;
use App\Http\Controllers\Web\FaqController;
use App\Http\Controllers\Web\GetStartedController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\LeadController;
use App\Http\Controllers\Web\LegalPageController;
use App\Http\Controllers\Web\MarketUpdatePublicController;
use App\Http\Controllers\Web\PageBuilderController;
use App\Http\Controllers\Web\PortfolioPublicController;
use App\Http\Controllers\Web\SaaSPlatformsController;
use App\Http\Controllers\Web\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::any('/filament', FilamentLegacyController::class)->name('filament.legacy');
Route::any('/filament/{path}', FilamentLegacyController::class)->where('path', '.*')->name('filament.legacy.sub');

Route::redirect('/resources/articles', '/articles', 301);
Route::redirect('/resources/case-studies', '/case-studies', 301);

Route::get('/about', [AboutController::class, 'show'])->name('about');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/saas-platforms', [SaaSPlatformsController::class, 'index'])->name('saas-platforms');
Route::get('/saas-platforms/{slug}', [SaaSPlatformsController::class, 'show'])->name('saas-platforms.show');
Route::get('/portfolio', [PortfolioPublicController::class, 'index'])->name('portfolio.index');
Route::get('/portfolio/{slug}', [PortfolioPublicController::class, 'show'])->where('slug', '[A-Za-z0-9\-]+')->name('portfolio.show');
Route::get('/case-studies', [CaseStudyPublicController::class, 'index'])->name('case-studies.index');
Route::get('/case-studies/category/{categorySlug}', [CaseStudyPublicController::class, 'category'])->name('case-studies.category');
Route::get('/case-studies/tag/{tagSlug}', [CaseStudyPublicController::class, 'tag'])->name('case-studies.tag');
Route::get('/case-studies/{slug}', [CaseStudyPublicController::class, 'show'])->name('case-studies.show');
Route::get('/market-updates', [MarketUpdatePublicController::class, 'index'])->name('market-updates.index');
Route::get('/market-updates/category/{categorySlug}', [MarketUpdatePublicController::class, 'category'])->name('market-updates.category');
Route::get('/market-updates/tag/{tagSlug}', [MarketUpdatePublicController::class, 'tag'])->name('market-updates.tag');
Route::get('/market-updates/{slug}', [MarketUpdatePublicController::class, 'show'])->name('market-updates.show');
Route::get('/articles', [ArticlePublicController::class, 'index'])->name('articles.index');
Route::get('/articles/category/{categorySlug}', [ArticlePublicController::class, 'category'])->name('articles.category');
Route::get('/articles/tag/{tagSlug}', [ArticlePublicController::class, 'tag'])->name('articles.tag');
Route::get('/articles/{slug}', [ArticlePublicController::class, 'show'])->name('articles.show');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/contact', fn () => redirect()->route('lead.create', [], 301))->name('contact');
Route::post('/contact', [LeadController::class, 'store'])
    ->middleware('throttle:lead-submit')
    ->name('contact.store');

Route::get('/lead', [LeadController::class, 'create'])->name('lead.create');

Route::post('/lead', [LeadController::class, 'store'])
    ->middleware('throttle:lead-submit')
    ->name('lead.store');

Route::get('/get-started', [GetStartedController::class, 'show'])->name('get-started');
Route::post('/get-started', [GetStartedController::class, 'store'])
    ->middleware('throttle:intake-minute')
    ->name('get-started.store');
Route::get('/careers', [CareerController::class, 'index'])->name('careers');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');
Route::get('/privacy-policy', [LegalPageController::class, 'show'])->defaults('slug', 'privacy-policy')->name('privacy');
Route::get('/terms-and-conditions', [LegalPageController::class, 'show'])->defaults('slug', 'terms-and-conditions')->name('terms');
Route::get('/legal/{slug}', [LegalPageController::class, 'show'])->name('legal.show');

Route::middleware('login.guest')->group(function (): void {
    Route::get('/login', [EndUserAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [EndUserAuthController::class, 'login'])->middleware('throttle:login-web')->name('login.submit');
});

Route::middleware(['login.guest', 'admin.ip'])->group(function (): void {
    Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])
        ->middleware('throttle:admin-login-web')
        ->name('admin.login.submit');
});

Route::middleware(['login.guest', 'master.ip'])->group(function (): void {
    Route::get('/master/login', [MasterAuthController::class, 'showLogin'])->name('master.login');
    Route::post('/master/login', [MasterAuthController::class, 'login'])
        ->middleware('throttle:master-login-web')
        ->name('master.login.submit');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [EndUserAuthController::class, 'logout'])->name('logout');
});

Route::get('/dashboard', fn () => redirect()->route('portal'))->middleware('auth')->name('dashboard');

Route::middleware(['auth', 'portal.user'])->group(function (): void {
    Route::get('/portal', [PortalController::class, 'index'])->name('portal');
});

Route::middleware(['admin.ip', 'auth', 'blade.admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/site-settings', [SiteSettingAdminController::class, 'edit'])->name('site-settings.edit');
    Route::put('/site-settings', [SiteSettingAdminController::class, 'update'])->name('site-settings.update');

    Route::get('/seo-settings', [SeoSettingAdminController::class, 'edit'])->name('seo-settings.edit');
    Route::put('/seo-settings', [SeoSettingAdminController::class, 'update'])->name('seo-settings.update');

    Route::get('/marketing-content', [MarketingContentAdminController::class, 'edit'])->name('marketing-content.edit');
    Route::put('/marketing-content', [MarketingContentAdminController::class, 'update'])->name('marketing-content.update');

    Route::get('/homepage', [HomepageAdminController::class, 'index'])->name('homepage.index');
    Route::patch('/homepage/seo', [HomepageAdminController::class, 'updateSeo'])->name('homepage.seo.update');
    Route::post('/homepage/sections/reorder', [HomepageAdminController::class, 'reorder'])->name('homepage.sections.reorder');
    Route::patch('/homepage/sections/{homepage_section}', [HomepageAdminController::class, 'updateSection'])->name('homepage.sections.update');
    Route::post('/homepage/sections/{homepage_section}/image', [HomepageAdminController::class, 'uploadSectionImage'])->name('homepage.sections.image');
    Route::post('/homepage/sections/{homepage_section}/items', [HomepageAdminController::class, 'storeItem'])->name('homepage.items.store');
    Route::patch('/homepage/items/{homepage_section_item}', [HomepageAdminController::class, 'updateItem'])->name('homepage.items.update');
    Route::delete('/homepage/items/{homepage_section_item}', [HomepageAdminController::class, 'destroyItem'])->name('homepage.items.destroy');

    Route::get('/navigation/{nav_menu}', [NavItemAdminController::class, 'index'])->name('navigation.index');
    Route::get('/navigation/{nav_menu}/create', [NavItemAdminController::class, 'create'])->name('navigation.create');
    Route::post('/navigation/{nav_menu}', [NavItemAdminController::class, 'store'])->name('navigation.store');
    Route::get('/navigation/{nav_menu}/{nav_item}/edit', [NavItemAdminController::class, 'edit'])->name('navigation.edit');
    Route::put('/navigation/{nav_menu}/{nav_item}', [NavItemAdminController::class, 'update'])->name('navigation.update');
    Route::delete('/navigation/{nav_menu}/{nav_item}', [NavItemAdminController::class, 'destroy'])->name('navigation.destroy');

    Route::resource('services', ServiceAdminController::class)->except(['show']);
    Route::resource('portfolio-items', PortfolioItemAdminController::class)->except(['show']);
    Route::resource('testimonials', TestimonialAdminController::class)->except(['show']);
    Route::resource('faqs', FaqAdminController::class)->except(['show']);
    Route::resource('articles', ArticleAdminController::class)->except(['show']);
    Route::get('/articles/{article}/preview', [ArticleAdminController::class, 'preview'])->name('articles.preview');
    Route::get('/ai-article-generator', [AiArticleGeneratorAdminController::class, 'index'])->name('ai-article-generator.index');
    Route::post('/ai-article-generator', [AiArticleGeneratorAdminController::class, 'store'])->name('ai-article-generator.store');
    Route::get('/case-studies/{caseStudy}/preview', [CaseStudyAdminController::class, 'preview'])->name('case-studies.preview');
    Route::resource('case-studies', CaseStudyAdminController::class)->except(['show']);
    Route::get('/market-updates/{marketUpdate}/preview', [MarketUpdateAdminController::class, 'preview'])->name('market-updates.preview');
    Route::resource('market-updates', MarketUpdateAdminController::class)->except(['show']);

    Route::post('/ai-content/quick/article', [AiContentQuickGenerateController::class, 'article'])->name('ai-content.quick.article');
    Route::post('/ai-content/quick/case-study', [AiContentQuickGenerateController::class, 'caseStudy'])->name('ai-content.quick.case-study');
    Route::post('/ai-content/quick/market-update', [AiContentQuickGenerateController::class, 'marketUpdate'])->name('ai-content.quick.market-update');

    Route::resource('legal-pages', LegalPageAdminController::class)->except(['show']);
    Route::resource('job-postings', JobPostingAdminController::class)->except(['show']);

    Route::get('/contact-messages', [ContactMessageAdminController::class, 'index'])->name('contact-messages.index');
    Route::get('/contact-messages/{contact_message}', [ContactMessageAdminController::class, 'show'])->name('contact-messages.show');
    Route::delete('/contact-messages/{contact_message}', [ContactMessageAdminController::class, 'destroy'])->name('contact-messages.destroy');
    Route::post('/contact-messages/{contact_message}/read', [ContactMessageAdminController::class, 'markRead'])->name('contact-messages.read');

    Route::get('/media', [MediaAdminController::class, 'index'])->name('media.index');
    Route::post('/media', [MediaAdminController::class, 'store'])->name('media.store');
    Route::delete('/media/{media_asset}', [MediaAdminController::class, 'destroy'])->name('media.destroy');

    Route::resource('ai-providers', AiProviderAdminController::class)->except(['show']);
    Route::resource('ai-agents', AiAgentAdminController::class)->except(['show']);
    Route::get('/ai-conversations', [AiConversationAdminController::class, 'index'])->name('ai-conversations.index');
    Route::get('/ai-conversations/{ai_conversation}', [AiConversationAdminController::class, 'show'])->name('ai-conversations.show');

    Route::get('/leads', [LeadAdminController::class, 'index'])->name('leads.index');
    Route::get('/leads/{lead}', [LeadAdminController::class, 'show'])->name('leads.show');
    Route::match(['put', 'patch'], '/leads/{lead}', [LeadAdminController::class, 'update'])->name('leads.update');
    Route::delete('/leads/{lead}', [LeadAdminController::class, 'destroy'])->name('leads.destroy');

    Route::prefix('crm')->name('crm.')->group(function (): void {
        Route::get('/', [CrmDashboardController::class, 'index'])->name('dashboard');

        Route::get('contacts', [CrmContactController::class, 'index'])->name('contacts.index');
        Route::get('contacts/create', [CrmContactController::class, 'create'])->name('contacts.create');
        Route::post('contacts', [CrmContactController::class, 'store'])->name('contacts.store');
        Route::get('contacts/{crm_contact}', [CrmContactController::class, 'show'])->name('contacts.show');
        Route::get('contacts/{crm_contact}/edit', [CrmContactController::class, 'edit'])->name('contacts.edit');
        Route::patch('contacts/{crm_contact}', [CrmContactController::class, 'update'])->name('contacts.update');
        Route::delete('contacts/{crm_contact}', [CrmContactController::class, 'destroy'])->name('contacts.destroy');
        Route::post('contacts/{crm_contact}/send-email', [CrmContactController::class, 'sendEmail'])->name('contacts.send-email');
        Route::post('contacts/{crm_contact}/notes', [CrmContactController::class, 'storeNote'])->name('contacts.notes.store');
        Route::patch('contacts/{crm_contact}/status', [CrmContactController::class, 'updateStatus'])->name('contacts.status');

        Route::get('sources', [CrmSourceController::class, 'index'])->name('sources.index');
        Route::post('sources', [CrmSourceController::class, 'store'])->name('sources.store');
        Route::patch('sources/{crm_source}', [CrmSourceController::class, 'update'])->name('sources.update');
        Route::delete('sources/{crm_source}', [CrmSourceController::class, 'destroy'])->name('sources.destroy');

        Route::get('projects', [CrmProjectController::class, 'index'])->name('projects.index');
        Route::get('projects/create', [CrmProjectController::class, 'create'])->name('projects.create');
        Route::post('projects', [CrmProjectController::class, 'store'])->name('projects.store');
        Route::get('projects/{crm_project}/edit', [CrmProjectController::class, 'edit'])->name('projects.edit');
        Route::patch('projects/{crm_project}', [CrmProjectController::class, 'update'])->name('projects.update');
        Route::delete('projects/{crm_project}', [CrmProjectController::class, 'destroy'])->name('projects.destroy');

        Route::get('reviews', [CrmReviewController::class, 'index'])->name('reviews.index');
        Route::post('reviews/{testimonial}/approve', [CrmReviewController::class, 'approve'])->name('reviews.approve');
        Route::post('reviews/{testimonial}/reject', [CrmReviewController::class, 'reject'])->name('reviews.reject');

        Route::get('faqs', [CrmFaqController::class, 'index'])->name('faqs.index');
        Route::post('faqs/{faq}/attach', [CrmFaqController::class, 'attach'])->name('faqs.attach');
        Route::post('faqs/{faq}/detach', [CrmFaqController::class, 'detach'])->name('faqs.detach');
    });

    Route::get('/security-settings', [SecuritySettingsAdminController::class, 'edit'])->name('security-settings.edit');
    Route::put('/security-settings', [SecuritySettingsAdminController::class, 'update'])->name('security-settings.update');

    Route::get('/activity-logs', [ActivityLogAdminController::class, 'index'])->name('activity-logs.index');

    Route::get('/users', [UserAdminController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserAdminController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}/roles', [UserAdminController::class, 'updateRoles'])->name('users.roles');

    Route::middleware('can:builder.access')->group(function (): void {
        Route::get('/ai-builder-context', [AiBuilderBusinessProfileAdminController::class, 'edit'])
            ->name('ai-builder-context.edit');
        Route::put('/ai-builder-context', [AiBuilderBusinessProfileAdminController::class, 'update'])
            ->name('ai-builder-context.update');
    });
});

Route::middleware(['master.ip', 'auth', 'master.panel'])->prefix('master')->name('master.')->group(function (): void {
    Route::post('/logout', [MasterAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [MasterDashboardController::class, 'index'])->name('dashboard');

    Route::middleware('can:security.manage')->group(function (): void {
        Route::get('/security/access-control', [SecurityAccessControlController::class, 'index'])
            ->name('security.access-control');
        Route::post('/security/access-control/ip-rules', [SecurityAccessControlController::class, 'store'])
            ->name('security.ip-rules.store');
        Route::patch('/security/access-control/ip-rules/{rule}', [SecurityAccessControlController::class, 'update'])
            ->name('security.ip-rules.update');
        Route::delete('/security/access-control/ip-rules/{rule}', [SecurityAccessControlController::class, 'destroy'])
            ->name('security.ip-rules.destroy');
    });
});

Route::middleware(['web', 'auth', 'builder.access'])->group(function (): void {
    Route::get('/builder/pages/{page}', [PageBuilderController::class, 'show'])
        ->whereNumber('page')
        ->name('builder.pages.show');

    Route::prefix('builder-api/v1')->group(function (): void {
        Route::get('/templates', [BuilderTemplateController::class, 'index']);
        Route::post('/pages/{page}/apply-template', [BuilderTemplateController::class, 'apply'])
            ->whereNumber('page');

        Route::get('/saved-sections', [BuilderSavedSectionController::class, 'index']);
        Route::post('/saved-sections', [BuilderSavedSectionController::class, 'store']);
        Route::delete('/saved-sections/{saved_section}', [BuilderSavedSectionController::class, 'destroy'])
            ->whereNumber('saved_section');

        Route::get('/pages/{page}', [BuilderPageController::class, 'show'])->whereNumber('page');
        Route::put('/pages/{page}/document', [BuilderPageController::class, 'updateDocument'])->whereNumber('page');
        Route::get('/pages/{page}/versions', [BuilderPageController::class, 'versions'])->whereNumber('page');
        Route::post('/pages/{page}/versions/{version}/restore', [BuilderPageController::class, 'restoreVersion'])
            ->whereNumber('page')
            ->whereNumber('version');
        Route::post('/pages/{page}/publish', [BuilderPageController::class, 'publish'])
            ->middleware('can:builder.publish')
            ->whereNumber('page');
        Route::post('/pages/{page}/archive', [BuilderPageController::class, 'archive'])->whereNumber('page');
        Route::post('/pages/{page}/unpublish', [BuilderPageController::class, 'unpublish'])->whereNumber('page');
        Route::get('/pages/{page}/export', [BuilderPageController::class, 'export'])->whereNumber('page');
        Route::post('/pages/{page}/import', [BuilderPageController::class, 'import'])->whereNumber('page');

        Route::post('/pages/{page}/ai/propose', [BuilderAiController::class, 'propose'])
            ->middleware(['can:builder.ai.use', 'throttle:builder-ai-minute'])
            ->whereNumber('page');
    });
});
