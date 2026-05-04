@extends('layouts.admin')

@section('title', 'Marketing content')

@section('content')
	<x-admin.page-header title="Marketing JSON">
		<x-slot:subtitle>
			Structured keys merge with defaults from <code class="rounded bg-zinc-100 px-1 text-xs">App\Support\MarketingContent</code>. Invalid JSON will be rejected.
		</x-slot:subtitle>
	</x-admin.page-header>

	<x-admin.card>
		<form method="post" action="{{ route('admin.marketing-content.update') }}" id="marketing-form" class="space-y-6">
			@csrf
			@method('PUT')
			<x-admin.textarea name="homepage_content_json" label="Homepage content (legacy fallback only)" rows="18" class="font-mono text-xs" required>{{ old('homepage_content_json', $homepageJson) }}</x-admin.textarea>
			<p class="mt-1 text-xs text-zinc-500">When homepage sections exist in the database, use <a href="{{ route('admin.homepage.index') }}" class="font-medium text-indigo-600 underline">Homepage</a> instead.</p>
			<x-admin.textarea name="about_content_json" label="About page content" rows="14" class="font-mono text-xs" required>{{ old('about_content_json', $aboutJson) }}</x-admin.textarea>
			<x-admin.textarea name="services_page_content_json" label="Services page content" rows="16" class="font-mono text-xs" required>{{ old('services_page_content_json', $servicesPageJson) }}</x-admin.textarea>
			<x-admin.textarea name="saas_page_content_json" label="SaaS Platforms page content" rows="18" class="font-mono text-xs" required>{{ old('saas_page_content_json', $saasPageJson) }}</x-admin.textarea>
			<x-admin.button variant="primary" type="submit">Save</x-admin.button>
		</form>
	</x-admin.card>
@endsection

@push('scripts')
	<script>
		$('#marketing-form').on('submit', function (e) {
			e.preventDefault();
			var $f = $(this);
			$.ajax({
				url: $f.attr('action'),
				type: 'POST',
				data: $f.serialize(),
				headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
				success: function (res) {
					adminToast(res.message || 'Saved.', 'success');
				},
				error: function (xhr) {
					var m = 'Invalid JSON or server error';
					if (xhr.responseJSON && xhr.responseJSON.message) m = xhr.responseJSON.message;
					adminToast(m, 'error');
				},
			});
		});
	</script>
@endpush
