@php
	$links = [
		['route' => 'admin.crm.dashboard', 'label' => 'Dashboard', 'perm' => 'crm.view'],
		['route' => 'admin.crm.contacts.index', 'label' => 'Contacts', 'perm' => 'crm.view'],
		['route' => 'admin.crm.projects.index', 'label' => 'Projects', 'perm' => 'crm.view'],
		['route' => 'admin.crm.sources.index', 'label' => 'Sources', 'perm' => 'crm.view'],
		['route' => 'admin.crm.reviews.index', 'label' => 'Reviews', 'perm' => 'testimonials.view_any'],
		['route' => 'admin.crm.faqs.index', 'label' => 'FAQs', 'perm' => 'faqs.view_any'],
	];
@endphp
<nav class="mb-6 flex flex-wrap gap-2 border-b border-zinc-200 pb-3 text-sm">
	@foreach ($links as $link)
		@can($link['perm'])
			<a
				href="{{ route($link['route']) }}"
				class="rounded-lg px-3 py-1.5 font-medium transition {{ request()->routeIs($link['route']) || ($link['route'] === 'admin.crm.contacts.index' && request()->routeIs('admin.crm.contacts.*')) || ($link['route'] === 'admin.crm.projects.index' && request()->routeIs('admin.crm.projects.*')) ? 'bg-indigo-600 text-white' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200' }}"
			>{{ $link['label'] }}</a>
		@endcan
	@endforeach
</nav>
