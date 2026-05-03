@php
    $navPrimary = request()->route('nav_menu');
@endphp
<aside
    id="admin-sidebar"
    class="fixed left-0 top-0 z-50 flex h-full w-64 flex-col border-r border-zinc-200/80 bg-white shadow-[var(--shadow-admin)] transition-transform duration-200 -translate-x-full md:static md:z-auto md:h-screen md:w-[var(--admin-sidebar-w)] md:translate-x-0 md:border-r md:shadow-none"
>
    <div class="flex h-14 items-center gap-2 border-b border-zinc-100 px-4 md:h-[3.5rem]">
        <span
            class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-xs font-bold text-white"
        >A</span>
        <span class="admin-sidebar-brand-text truncate text-sm font-semibold text-zinc-900">{{ $site->site_name ?? 'Artixcore' }}</span>
    </div>

    <nav class="flex flex-1 flex-col gap-6 overflow-y-auto px-3 py-4">
        <x-admin.sidebar-section title="Overview">
            <x-admin.sidebar-item
                :href="route('admin.dashboard')"
                icon="layout-dashboard"
                :active="request()->routeIs('admin.dashboard')"
            >Dashboard</x-admin.sidebar-item>
        </x-admin.sidebar-section>

        <x-admin.sidebar-section title="Content">
            <x-admin.sidebar-item
                :href="route('admin.services.index')"
                icon="squares-2x2"
                :active="request()->routeIs('admin.services.*')"
            >Services</x-admin.sidebar-item>
            <x-admin.sidebar-item
                :href="route('admin.testimonials.index')"
                icon="chat-bubble-left-right"
                :active="request()->routeIs('admin.testimonials.*')"
            >Testimonials</x-admin.sidebar-item>
            <x-admin.sidebar-item
                :href="route('admin.faqs.index')"
                icon="question-mark-circle"
                :active="request()->routeIs('admin.faqs.*')"
            >FAQ</x-admin.sidebar-item>
            <x-admin.sidebar-item
                :href="route('admin.articles.index')"
                icon="newspaper"
                :active="request()->routeIs('admin.articles.*') && !request()->routeIs('admin.ai-article-generator.*')"
            >Articles</x-admin.sidebar-item>
            @can('ai_articles.generate')
                <x-admin.sidebar-item
                    :href="route('admin.ai-article-generator.index')"
                    icon="bolt"
                    :active="request()->routeIs('admin.ai-article-generator.*')"
                >Ali 1.0 generator</x-admin.sidebar-item>
            @endcan
            <x-admin.sidebar-item
                :href="route('admin.case-studies.index')"
                icon="briefcase"
                :active="request()->routeIs('admin.case-studies.*')"
            >Case studies</x-admin.sidebar-item>
            @can('portfolio_items.view_any')
                <x-admin.sidebar-item
                    :href="route('admin.portfolio-items.index')"
                    icon="folder-open"
                    :active="request()->routeIs('admin.portfolio-items.*')"
                >Portfolio</x-admin.sidebar-item>
            @endcan
            @can('market_updates.view_any')
                <x-admin.sidebar-item
                    :href="route('admin.market-updates.index')"
                    icon="signal"
                    :active="request()->routeIs('admin.market-updates.*')"
                >Market updates</x-admin.sidebar-item>
            @endcan
            <x-admin.sidebar-item
                :href="route('admin.legal-pages.index')"
                icon="document-text"
                :active="request()->routeIs('admin.legal-pages.*')"
            >Legal pages</x-admin.sidebar-item>
            <x-admin.sidebar-item
                :href="route('admin.job-postings.index')"
                icon="user-group"
                :active="request()->routeIs('admin.job-postings.*')"
            >Job postings</x-admin.sidebar-item>
        </x-admin.sidebar-section>

        <x-admin.sidebar-section title="Inbox & media">
            <x-admin.sidebar-item
                :href="route('admin.contact-messages.index')"
                icon="inbox"
                :active="request()->routeIs('admin.contact-messages.*')"
            >Contact inbox</x-admin.sidebar-item>
            @can('crm.view')
                <x-admin.sidebar-item
                    :href="route('admin.crm.dashboard')"
                    icon="user-group"
                    :active="request()->routeIs('admin.crm.*')"
                >CRM</x-admin.sidebar-item>
            @endcan
            @can('viewAny', App\Models\Lead::class)
                <x-admin.sidebar-item
                    :href="route('admin.leads.index')"
                    icon="briefcase"
                    :active="request()->routeIs('admin.leads.*')"
                >Leads</x-admin.sidebar-item>
            @endcan
            <x-admin.sidebar-item
                :href="route('admin.media.index')"
                icon="photo"
                :active="request()->routeIs('admin.media.*')"
            >Media</x-admin.sidebar-item>
        </x-admin.sidebar-section>

        @php
            $u = auth()->user();
            $showAiNav =
                ($u?->can('viewAny', App\Models\AiProvider::class) ?? false)
                || ($u?->can('viewAny', App\Models\AiAgent::class) ?? false)
                || ($u?->can('viewAny', App\Models\AiConversation::class) ?? false)
                || ($u?->can('builder.access') ?? false);
            $showSecurityNav =
                ($u?->can('view', App\Models\PlatformSecuritySetting::instance()) ?? false)
                || ($u?->can('viewAny', App\Models\ActivityLog::class) ?? false)
                || ($u?->can('viewAny', App\Models\User::class) ?? false);
        @endphp
        @if ($showAiNav)
            <x-admin.sidebar-section title="AI &amp; automation">
                @can('viewAny', App\Models\AiProvider::class)
                    <x-admin.sidebar-item
                        :href="route('admin.ai-providers.index')"
                        icon="code-bracket"
                        :active="request()->routeIs('admin.ai-providers.*')"
                    >AI providers</x-admin.sidebar-item>
                @endcan
                @can('viewAny', App\Models\AiAgent::class)
                    <x-admin.sidebar-item
                        :href="route('admin.ai-agents.index')"
                        icon="user-group"
                        :active="request()->routeIs('admin.ai-agents.*')"
                    >AI agents</x-admin.sidebar-item>
                @endcan
                @can('viewAny', App\Models\AiConversation::class)
                    <x-admin.sidebar-item
                        :href="route('admin.ai-conversations.index')"
                        icon="chat-bubble-left-right"
                        :active="request()->routeIs('admin.ai-conversations.*')"
                    >Conversations</x-admin.sidebar-item>
                @endcan
                @can('builder.access')
                    <x-admin.sidebar-item
                        :href="route('admin.ai-builder-context.edit')"
                        icon="document-text"
                        :active="request()->routeIs('admin.ai-builder-context.*')"
                    >AI builder context</x-admin.sidebar-item>
                @endcan
            </x-admin.sidebar-section>
        @endif

        @if ($showSecurityNav)
            <x-admin.sidebar-section title="Security &amp; access">
                @can('view', App\Models\PlatformSecuritySetting::instance())
                    <x-admin.sidebar-item
                        :href="route('admin.security-settings.edit')"
                        icon="cog-6-tooth"
                        :active="request()->routeIs('admin.security-settings.*')"
                    >Security</x-admin.sidebar-item>
                @endcan
                @can('viewAny', App\Models\ActivityLog::class)
                    <x-admin.sidebar-item
                        :href="route('admin.activity-logs.index')"
                        icon="document-text"
                        :active="request()->routeIs('admin.activity-logs.*')"
                    >Audit log</x-admin.sidebar-item>
                @endcan
                @can('viewAny', App\Models\User::class)
                    <x-admin.sidebar-item
                        :href="route('admin.users.index')"
                        icon="user-circle"
                        :active="request()->routeIs('admin.users.*')"
                    >Users &amp; roles</x-admin.sidebar-item>
                @endcan
            </x-admin.sidebar-section>
        @endif

        <x-admin.sidebar-section title="Configuration">
            <x-admin.sidebar-item
                :href="route('admin.site-settings.edit')"
                icon="cog-6-tooth"
                :active="request()->routeIs('admin.site-settings.*')"
            >Site settings</x-admin.sidebar-item>
            <x-admin.sidebar-item
                :href="route('admin.seo-settings.edit')"
                icon="magnifying-glass"
                :active="request()->routeIs('admin.seo-settings.*')"
            >SEO settings</x-admin.sidebar-item>
            <x-admin.sidebar-item
                :href="route('admin.marketing-content.edit')"
                icon="code-bracket"
                :active="request()->routeIs('admin.marketing-content.*')"
            >Marketing JSON</x-admin.sidebar-item>
            <x-admin.sidebar-item
                :href="route('admin.navigation.index', ['nav_menu' => 'web_primary'])"
                icon="bars-3"
                :active="request()->routeIs('admin.navigation.*') && $navPrimary === 'web_primary'"
            >Navigation (header)</x-admin.sidebar-item>
            <x-admin.sidebar-item
                :href="route('admin.navigation.index', ['nav_menu' => 'footer'])"
                icon="bars-3-bottom-left"
                :active="request()->routeIs('admin.navigation.*') && $navPrimary === 'footer'"
            >Navigation (footer)</x-admin.sidebar-item>
        </x-admin.sidebar-section>
    </nav>

    <div class="mt-auto space-y-2 border-t border-zinc-100 p-3">
        <a
            href="{{ url('/') }}"
            target="_blank"
            rel="noopener noreferrer"
            class="admin-sidebar-footer-btn flex items-center justify-center gap-2 rounded-[10px] border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-50 md:justify-start"
        >
            <x-admin.icon name="arrow-top-right-on-square" class="size-4 shrink-0 text-zinc-500" />
            <span class="admin-sidebar-footer-text">View site</span>
        </a>
        @if (Route::has('master.dashboard') && auth()->user()?->hasRole('master_admin'))
            <p class="text-center text-[11px] text-zinc-400 md:text-left">
                <a href="{{ route('master.dashboard') }}" class="hover:text-zinc-600">Master admin</a>
            </p>
        @endif
    </div>
</aside>
