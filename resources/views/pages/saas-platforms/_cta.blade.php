@php $p = $saasPage; @endphp
<section class="position-relative z-index-2 py-5 mb-n5">
	<div class="container position-relative">
		<div class="bg-primary rounded position-relative overflow-hidden p-4 p-sm-5" data-aos="zoom-in">
			<div class="row g-4 align-items-center">
				<div class="col-lg-7">
					<h3 class="text-white mb-2">{{ $p['cta_title'] ?? 'Ready to talk?' }}</h3>
					<p class="text-white mb-0 opacity-75">{{ $p['cta_body'] ?? '' }}</p>
				</div>
				<div class="col-lg-5 text-lg-end">
					<div class="d-flex flex-wrap gap-2 justify-content-lg-end">
						@if(!empty($p['cta_primary_label']))
							<a href="{{ url($p['cta_primary_url'] ?? '/lead') }}" class="btn btn-dark mb-0">{{ $p['cta_primary_label'] }}</a>
						@endif
						@if(!empty($p['cta_secondary_label']))
							<a href="{{ url($p['cta_secondary_url'] ?? '/lead') }}" class="btn btn-outline-light mb-0">{{ $p['cta_secondary_label'] }}</a>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
