@extends('layouts.admin')
@section('title', 'Generate with Ali 1.0')
@section('content')
	<x-admin.page-header title="Generate with Ali 1.0">
		<x-slot:actions>
			<x-admin.button variant="ghost" :href="route('admin.articles.index')">Articles</x-admin.button>
		</x-slot:actions>
	</x-admin.page-header>

	<x-admin.card>
		<p class="mb-6 text-sm text-zinc-600">
			Generates an editable draft with author <strong>Ali 1.0</strong>. Content is not auto-published. Latest-news style buckets produce evergreen analysis — verify facts before publishing.
		</p>
		<form method="post" action="{{ route('admin.ai-article-generator.store') }}" class="space-y-6">
			@csrf
			<x-admin.select name="article_type" label="Article type / bucket" required>
				<option value="latest_discovery">Latest discovery / innovation</option>
				<option value="today_trends">Today&apos;s trends / business</option>
				<option value="latest_tech">Latest technology / engineering</option>
				<option value="company_update">Company update</option>
				<option value="tutorial">Tutorial</option>
				<option value="insight">Insight</option>
			</x-admin.select>
			<x-admin.textarea name="topic" label="Topic or angle (optional)" rows="4" hint="Leave blank to let Ali choose a safe evergreen angle.">{{ old('topic') }}</x-admin.textarea>
			@if ($errors->any())
				<div class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-800">
					{{ $errors->first() }}
				</div>
			@endif
			<x-admin.button variant="primary" type="submit">Generate draft</x-admin.button>
		</form>
	</x-admin.card>
@endsection
