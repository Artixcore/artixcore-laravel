@php $sp = $servicesPage; @endphp
@if(!empty($sp['intro_title']) || !empty($sp['intro_body']))
<section class="pb-0">
	<div class="container">
		<div class="row mb-3 mb-xl-0">
			<div class="col-xl-10 mx-auto text-center" data-aos="fade-up">
				@if(!empty($sp['intro_title']))
					<h4 class="lh-base mb-0">{{ $sp['intro_title'] }}</h4>
				@endif
				@if(!empty($sp['intro_body']))
					<p class="mb-0 mt-4 text-muted">{{ $sp['intro_body'] }}</p>
				@endif
			</div>
		</div>
	</div>
</section>
@endif
