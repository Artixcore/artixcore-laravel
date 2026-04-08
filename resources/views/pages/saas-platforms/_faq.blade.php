@php $p = $saasPage; @endphp
@if(!empty($p['show_faq']) && $faqs->isNotEmpty())
<section class="py-5 bg-light">
	<div class="container">
		<h2 class="text-center mb-4 mb-sm-5" data-aos="fade-up">{{ $p['faq_section_title'] ?? 'FAQ' }}</h2>
		<div class="row justify-content-center">
			<div class="col-lg-10">
				<div class="accordion" id="saasFaqAccordion">
					@foreach($faqs as $i => $faq)
						<div class="accordion-item" data-aos="fade-up" data-aos-delay="{{ min($i * 40, 200) }}">
							<h2 class="accordion-header" id="saas-faq-h{{ $faq->id }}">
								<button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#saas-faq-c{{ $faq->id }}">
									{{ $faq->question }}
								</button>
							</h2>
							<div id="saas-faq-c{{ $faq->id }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#saasFaqAccordion">
								<div class="accordion-body">{{ $faq->answer }}</div>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
</section>
@endif
