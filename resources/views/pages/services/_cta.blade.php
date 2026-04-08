@php $sp = $servicesPage; @endphp
<section class="position-relative z-index-2 py-5 mb-n5">
	<div class="container position-relative">
		<div class="bg-primary rounded position-relative overflow-hidden p-4 p-sm-5" data-aos="zoom-in">
			<div class="row g-4 align-items-center">
				<div class="col-lg-8">
					<h3 class="text-white mb-2">{{ $sp['cta_title'] ?? 'Ready to talk?' }}</h3>
					<p class="text-white mb-0 opacity-75">{{ $sp['cta_body'] ?? '' }}</p>
				</div>
				<div class="col-lg-4 text-lg-end">
					<a href="{{ url($sp['cta_button_url'] ?? '/contact') }}" class="btn btn-dark mb-0">{{ $sp['cta_button_label'] ?? 'Contact' }}</a>
				</div>
			</div>
		</div>
	</div>
</section>
