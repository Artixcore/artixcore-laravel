@extends('layouts.app')

@section('meta_title', 'FAQ — '.($site->site_name ?? 'Artixcore'))

@section('content')
<section class="pt-8 pb-5">
	<div class="container">
		<h1 class="mb-4">Frequently asked questions</h1>
		<div class="accordion" id="faqAccordion">
			@foreach($faqs as $i => $faq)
				<div class="accordion-item">
					<h2 class="accordion-header" id="faq-h{{ $faq->id }}">
						<button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq-c{{ $faq->id }}">
							{{ $faq->question }}
						</button>
					</h2>
					<div id="faq-c{{ $faq->id }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
						<div class="accordion-body">{{ $faq->answer }}</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endsection
