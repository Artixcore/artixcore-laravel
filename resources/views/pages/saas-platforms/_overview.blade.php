@php $p = $saasPage; @endphp
@if(!empty($p['overview_title']) || !empty($p['overview_body']))
<section class="py-5">
	<div class="container">
		<div class="row align-items-center g-4 g-lg-5">
			<div class="col-lg-6" data-aos="fade-right">
				@if(!empty($p['overview_title']))
					<h2 class="mb-4">{{ $p['overview_title'] }}</h2>
				@endif
				@if(!empty($p['overview_body']))
					<p class="lead mb-0">{{ $p['overview_body'] }}</p>
				@endif
			</div>
			<div class="col-lg-6 text-center" data-aos="fade-left">
				<img src="{{ asset('theme/images/elements/saas-feature.png') }}" class="img-fluid" alt="">
			</div>
		</div>
	</div>
</section>
@endif
