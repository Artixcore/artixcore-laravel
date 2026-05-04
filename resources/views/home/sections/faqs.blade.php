@php
	$s = $section ?? [];
	$items = $s['items'] ?? [];
@endphp
@if(count($items) > 0)
<section class="py-5">
	<div class="container">
		<h2 class="text-center mb-5">{{ $s['title'] ?? 'FAQ' }}</h2>
		@if(!empty($s['subtitle']))
			<p class="text-center text-muted mb-4">{{ $s['subtitle'] }}</p>
		@endif
		<div class="row justify-content-center">
			<div class="col-lg-9">
				<div class="accordion accordion-icon accordion-bg-light" id="homeFaq">
					@foreach($items as $i => $it)
						<div class="accordion-item mb-3">
							<h2 class="accordion-header" id="hq{{ $i }}">
								<button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#hc{{ $i }}" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}" aria-controls="hc{{ $i }}">
									{{ $it['question'] ?? '' }}
								</button>
							</h2>
							<div id="hc{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" aria-labelledby="hq{{ $i }}" data-bs-parent="#homeFaq">
								<div class="accordion-body">{!! nl2br(e($it['answer'] ?? '')) !!}</div>
							</div>
						</div>
					@endforeach
				</div>
				<div class="text-center mt-4">
					<a href="{{ route('faq') }}" class="btn btn-outline-primary btn-sm">View all FAQs</a>
				</div>
			</div>
		</div>
	</div>
</section>
@endif
